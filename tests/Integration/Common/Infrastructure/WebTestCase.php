<?php

declare(strict_types=1);

namespace App\Tests\Integration\Common\Infrastructure;

use App\User\Domain\User;
use App\User\Infrastructure\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class WebTestCase extends BaseWebTestCase
{
    private const UUID_PATTERN = '/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}/';

    private const CONTENT_TYPE = 'CONTENT_TYPE';

    protected Response $response;

    protected Generator|null $faker = null;

    private KernelBrowser|null $client = null;

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    public function assertUuid(string $string, string $message = ''): void
    {
        $this->assertMatchesRegularExpression(self::UUID_PATTERN, $string, $message);
    }

    /**
     * @param array<string, string> $headers
     * @param array<mixed> $files
     * @param array<mixed> $post
     */
    protected function request(
        string $method,
        string $uri,
        string|null $content = null,
        array $headers = [],
        array $files = [],
        array $post = [],
    ): Response {
        $server = [
            self::CONTENT_TYPE => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ];

        foreach ($headers as $key => $value) {
            if (\strtolower($key) === 'content-type') {
                $server[self::CONTENT_TYPE] = $value;

                continue;
            }

            $server['HTTP_' . \strtoupper(\str_replace('-', '_', $key))] = $value;
        }

        $this->client?->request($method, $uri, $post, $files, $server, $content);

        $this->response = $this->client?->getResponse() ?? new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);

        return $this->response;
    }

    /**
     * @param array<string, string> $headers
     * @param array<mixed> $files
     * @param array<mixed> $post
     */
    protected function authenticatedRequest(
        string $method,
        string $uri,
        string|null $content = null,
        array $headers = [],
        array $files = [],
        array $post = [],
        string|null $emailForBearerToken = null,
    ): Response {
        $headers = \array_merge(
            $headers,
            [
                'Authorization' => 'Bearer ' . $this->createBearerToken($emailForBearerToken ?? ''),
            ],
        );

        return $this->request($method, $uri, $content, $headers, $files, $post);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDecodedResponseContent(): array
    {
        /** @var array<string, mixed>|null $decodedContent */
        $decodedContent = \json_decode($this->response->getContent() ?: '', true, 512, \JSON_THROW_ON_ERROR);

        if ($decodedContent === null) {
            return [];
        }

        return $decodedContent;
    }

    protected function dispatch(object $command): void
    {
        /** @var MessageBusInterface $commandBus */
        $commandBus = self::getContainer()->get('messenger.bus.commands');

        $commandBus->dispatch($command);
    }

    protected function createBearerToken(string $email): string
    {
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findByEmail($email);

        if (!$user instanceof User) {
            throw new \Exception('User with email ' . $email . ' not found');
        }

        return $this->getJwtManager()->create($user);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        return $entityManager;
    }

    private function getUserRepository(): UserRepository
    {
        /** @var UserRepository $userRepository */
        $userRepository = self::getContainer()->get(UserRepository::class);

        return $userRepository;
    }

    private function getJwtManager(): JWTManager
    {
        /** @var JWTManager $jwtManager */
        $jwtManager = self::getContainer()->get('lexik_jwt_authentication.jwt_manager');

        return $jwtManager;
    }
}
