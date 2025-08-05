<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Doctrine;

use App\Product\Domain\ProductId;
use App\Shared\Infrastructure\Doctrine\IdType;

final class ProductIdType extends IdType
{
    private const NAME = 'product_id';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function getIdentifierClass(): string
    {
        return ProductId::class;
    }
}
