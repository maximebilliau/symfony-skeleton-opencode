<?php

declare(strict_types=1);

namespace App\User\Application\DTO;

readonly class RefreshTokenData
{
    public function __construct(
        public string $token
    ) {
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON provided.');
        }

        $token = $data['token'] ?? null;
        if (!is_string($token) || ($token === '' || $token === '0')) {
            throw new \InvalidArgumentException('Token is required and must be a non-empty string.');
        }

        return new self($token);
    }
}
