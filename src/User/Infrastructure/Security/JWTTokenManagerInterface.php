<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Security;

use App\User\Domain\User;

interface JWTTokenManagerInterface
{
    public function create(User $user): string;

    /** @return  array<string, mixed> */
    public function parse(string $token): array;

    public function invalidate(string $token): void;
}
