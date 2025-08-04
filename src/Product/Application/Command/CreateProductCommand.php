<?php

declare(strict_types=1);

namespace App\Product\Application\Command;

final readonly class CreateProductCommand
{
    public function __construct(
        public string $name,
        public string $description,
        public float $price
    ) {
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
