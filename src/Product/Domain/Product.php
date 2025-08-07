<?php

declare(strict_types=1);

namespace App\Product\Domain;

final readonly class Product
{
    public function __construct(
        private ProductId $id,
        private string $name,
        private string $description,
        private float $price
    ) {
    }

    public function getId(): ProductId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
