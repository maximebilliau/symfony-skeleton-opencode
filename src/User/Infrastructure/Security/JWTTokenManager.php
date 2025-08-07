<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Security;

use App\User\Domain\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface as LexikJWTTokenManagerInterface;

final readonly class JWTTokenManager implements JWTTokenManagerInterface
{
    public function __construct(
        private LexikJWTTokenManagerInterface $lexikJWTTokenManager
    ) {
    }

    public function create(User $user): string
    {
        return $this->lexikJWTTokenManager->create($user);
    }

    public function parse(string $token): array
    {
        /** @var array<string, mixed> */
        return $this->lexikJWTTokenManager->parse($token);
    }

    public function invalidate(string $token): void
    {
        // Implementation depends on your requirements
        // This could involve blacklisting the token, etc.
    }
}
