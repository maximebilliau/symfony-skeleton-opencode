<?php

declare(strict_types=1);

namespace App\Tests\Application\QueryHandler;

use App\User\Application\Query\RefreshTokenQuery;
use App\User\Application\QueryHandler\RefreshTokenQueryHandler;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\UserId;
use App\User\Infrastructure\Security\JWTTokenManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Uid\Uuid;

class RefreshTokenQueryHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private JWTTokenManagerInterface|MockObject $jwtTokenManager;
    private RefreshTokenQueryHandler $refreshTokenQueryHandler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->jwtTokenManager = $this->createMock(JWTTokenManagerInterface::class);
        $this->refreshTokenQueryHandler = new RefreshTokenQueryHandler(
            $this->jwtTokenManager,
            $this->userRepository
        );
    }

    public function testSuccessfulTokenRefresh(): void
    {
        $query = new RefreshTokenQuery('valid.jwt.token');
        $user = new User(UserId::fromString(Uuid::v4()->toString()), 'test@example.com', 'password', []);

        $payload = [
            'username' => 'test@example.com',
            'iat' => time() - 1000,
            'exp' => time() + 3600,
            'roles' => [],
        ];

        /** @phpstan-ignore method.notFound */
        $this->jwtTokenManager
            ->expects($this->once())
            ->method('parse')
            ->with('valid.jwt.token')
            ->willReturn($payload);

        /** @phpstan-ignore method.notFound */
        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn($user);

        /** @phpstan-ignore method.notFound */
        $this->jwtTokenManager
            ->expects($this->once())
            ->method('create')
            ->with($user)
            ->willReturn('new.jwt.token');

        $newToken = $this->refreshTokenQueryHandler->__invoke($query);

        $this->assertSame('new.jwt.token', $newToken);
    }

    public function testTokenRefreshWithExpiredToken(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Token has expired.');

        $query = new RefreshTokenQuery('expired.jwt.token');

        $expiredPayload = [
            'username' => 'test@example.com',
            'iat' => time() - 7200,
            'exp' => time() - 3600, // Token expired 1 hour ago
            'roles' => [],
        ];

        /** @phpstan-ignore method.notFound */
        $this->jwtTokenManager
            ->expects($this->once())
            ->method('parse')
            ->with('expired.jwt.token')
            ->willReturn($expiredPayload);

        $this->refreshTokenQueryHandler->__invoke($query);
    }

    public function testTokenRefreshWithUserNotFound(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('User not found.');

        $query = new RefreshTokenQuery('valid.jwt.token');

        $payload = [
            'username' => 'nonexistent@example.com',
            'iat' => time() - 1000,
            'exp' => time() + 3600,
            'roles' => [],
        ];

        /** @phpstan-ignore method.notFound */
        $this->jwtTokenManager
            ->expects($this->once())
            ->method('parse')
            ->with('valid.jwt.token')
            ->willReturn($payload);

        /** @phpstan-ignore method.notFound */
        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with('nonexistent@example.com')
            ->willReturn(null);

        $this->refreshTokenQueryHandler->__invoke($query);
    }

    public function testTokenRefreshWithInvalidTokenStructure(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid token structure:');

        $query = new RefreshTokenQuery('invalid.jwt.token');

        $invalidPayload = [
            'iat' => time() - 1000,
            'exp' => time() + 3600,
            // Missing 'username' field
        ];

        /** @phpstan-ignore method.notFound */
        $this->jwtTokenManager
            ->expects($this->once())
            ->method('parse')
            ->with('invalid.jwt.token')
            ->willReturn($invalidPayload);

        $this->refreshTokenQueryHandler->__invoke($query);
    }

    public function testTokenRefreshWithParseException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Token parsing failed');

        $query = new RefreshTokenQuery('malformed.jwt.token');

        /** @phpstan-ignore method.notFound */
        $this->jwtTokenManager
            ->expects($this->once())
            ->method('parse')
            ->with('malformed.jwt.token')
            ->willThrowException(new \Exception('Token parsing failed'));

        $this->refreshTokenQueryHandler->__invoke($query);
    }

    public function testTokenRefreshWithMissingRequiredFields(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid token structure:');

        $query = new RefreshTokenQuery('invalid.jwt.token');

        $payloadMissingIat = [
            'username' => 'test@example.com',
            'exp' => time() + 3600,
            // Missing 'iat' field
        ];

        /** @phpstan-ignore method.notFound */
        $this->jwtTokenManager
            ->expects($this->once())
            ->method('parse')
            ->with('invalid.jwt.token')
            ->willReturn($payloadMissingIat);

        $this->refreshTokenQueryHandler->__invoke($query);
    }
}
