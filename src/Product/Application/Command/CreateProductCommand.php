<?php

declare(strict_types=1);

namespace App\Product\Application\Command;

use App\Product\Application\DTO\ProductData;

final readonly class CreateProductCommand
{
    public function __construct(
        public ProductData $productData
    ) {
    }

    public function getName(): string
    {
        return $this->productData->name;
    }

    public function getDescription(): string
    {
        return $this->productData->description;
    }

    public function getPrice(): float
    {
        return $this->productData->price;
    }
}
