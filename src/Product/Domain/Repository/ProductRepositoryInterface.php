<?php

declare(strict_types=1);

namespace App\Product\Domain\Repository;

use App\Product\Domain\Product;
use App\Product\Domain\ProductId;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;

    public function findById(ProductId $id): Product|null;
}
