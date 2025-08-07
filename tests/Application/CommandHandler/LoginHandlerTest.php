<?php

declare(strict_types=1);

namespace App\Tests\Application\CommandHandler;

use App\User\Application\Command\LoginCommand;
use App\User\Application\CommandHandler\LoginHandler;
use App\User\Domain\User;
use App\User\Domain\UserId;
use App\User\Infrastructure\Repository\UserRepository;
use App\User\Infrastructure\Security\JWTTokenManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Uid\Uuid;

// Import User class

class LoginHandlerTest extends TestCase
{
    private UserRepository|MockObject $userRepository;
    private UserPasswordHasherInterface|MockObject $passwordHasher;
    private JWTTokenManagerInterface|MockObject $jwtManager;
    private LoginHandler $loginHandler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->jwtManager = $this->createMock(JWTTokenManagerInterface::class);
        $this->loginHandler = new LoginHandler($this->userRepository, $this->passwordHasher, $this->jwtManager);
    }

    public function testSuccessfulLogin(): void
    {
        $command = new LoginCommand('test@example.com', 'password');
        $user = new User(UserId::fromString(Uuid::v4()->toString()), 'test@example.com', 'password', []);

        /** @phpstan-ignore method.notFound */
        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        /** @phpstan-ignore method.notFound */
        $this->passwordHasher
            ->expects($this->once())
            ->method('isPasswordValid')
            ->willReturn(true);

        /** @phpstan-ignore method.notFound */
        $this->jwtManager
            ->expects($this->once())
            ->method('create')
            ->willReturn('jwt_token');

        $token = $this->loginHandler->__invoke($command);

        $this->assertSame('jwt_token', $token);
    }

    public function testInvalidCredentials(): void
    {
        $this->expectException(AuthenticationException::class);

        $command = new LoginCommand('test@example.com', 'wrong_password');
        /** @phpstan-ignore method.notFound */
        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn(null);

        $this->loginHandler->__invoke($command);
    }
}
