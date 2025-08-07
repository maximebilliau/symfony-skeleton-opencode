<?php

declare(strict_types=1);

namespace App\User\Application\DTO;

readonly class UserData
{
    /**
     * @param array<string> $roles
     */
    public function __construct(
        public string $email,
        public string $password,
        public array $roles,
    ) {
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON provided.');
        }

        $email = $data['email'] ?? null;
        if (!is_string($email)) {
            throw new \InvalidArgumentException('User email is required and must be a string.');
        }

        $password = $data['password'] ?? null;
        if (!is_string($password)) {
            throw new \InvalidArgumentException('User password is required and must be a string.');
        }

        /** @var array<string> $roles */
        $roles = $data['roles'];

        return new self($email, $password, $roles);
    }
}
