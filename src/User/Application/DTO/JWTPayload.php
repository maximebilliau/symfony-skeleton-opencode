<?php

declare(strict_types=1);

namespace App\User\Application\DTO;

readonly class JWTPayload
{
    /**
     * @param array<string> $roles
     */
    public function __construct(
        public string $username,
        public int $iat,
        public int $exp,
        public array $roles = []
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $username = $payload['username'] ?? null;
        if (!is_string($username) || ($username === '' || $username === '0')) {
            throw new \InvalidArgumentException('Invalid token structure: username is required.');
        }

        $iat = $payload['iat'] ?? null;
        if (!is_int($iat)) {
            throw new \InvalidArgumentException('Invalid token structure: iat must be an integer.');
        }

        $exp = $payload['exp'] ?? null;
        if (!is_int($exp)) {
            throw new \InvalidArgumentException('Invalid token structure: exp must be an integer.');
        }

        /** @var array<string> $roles */
        $roles = $payload['roles'] ?? [];

        return new self($username, $iat, $exp, $roles);
    }

    public function isExpired(): bool
    {
        return time() > $this->exp;
    }
}
