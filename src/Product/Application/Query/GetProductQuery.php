<?php

declare(strict_types=1);

namespace App\Product\Application\Query;

use App\Product\Domain\ProductId;

final readonly class GetProductQuery
{
    public function __construct(
        public ProductId $id
    ) {
    }

    public function getId(): ProductId
    {
        return $this->id;
    }
}
