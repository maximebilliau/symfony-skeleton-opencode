<?php

declare(strict_types=1);

namespace App\User\Application\QueryHandler;

use App\User\Application\DTO\JWTPayload;
use App\User\Application\Query\RefreshTokenQuery;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Infrastructure\Security\JWTTokenManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

#[AsMessageHandler]
final readonly class RefreshTokenQueryHandler
{
    public function __construct(
        private JWTTokenManagerInterface $jwtTokenManager,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(RefreshTokenQuery $query): string
    {
        try {
            /** @var array<string, mixed> $payloadArray */
            $payloadArray = $this->jwtTokenManager->parse($query->token);
            $payload = JWTPayload::fromArray($payloadArray);

            if ($payload->isExpired()) {
                throw new AuthenticationException('Token has expired.');
            }

            $user = $this->userRepository->findByEmail($payload->username);

            if (!$user instanceof User) {
                throw new AuthenticationException('User not found.');
            }

            return $this->jwtTokenManager->create($user);
        } catch (\InvalidArgumentException $e) {
            throw new AuthenticationException('Invalid token structure: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
