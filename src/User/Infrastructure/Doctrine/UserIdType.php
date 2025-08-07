<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Doctrine;

use App\Shared\Infrastructure\Doctrine\IdType;
use App\User\Domain\UserId;

final class UserIdType extends IdType
{
    private const NAME = 'user_id';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function getIdentifierClass(): string
    {
        return UserId::class;
    }
}
