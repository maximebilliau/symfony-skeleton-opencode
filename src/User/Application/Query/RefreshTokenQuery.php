<?php

declare(strict_types=1);

namespace App\User\Application\Query;

final readonly class RefreshTokenQuery
{
    public function __construct(
        public string $token
    ) {
    }
}
