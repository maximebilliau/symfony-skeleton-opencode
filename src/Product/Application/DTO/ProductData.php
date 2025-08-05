<?php

declare(strict_types=1);

namespace App\Product\Application\DTO;

class ProductData
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly float $price,
    ) {
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON provided.');
        }

        $name = $data['name'] ?? null;
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Product name is required and must be a string.');
        }

        $description = $data['description'] ?? null;
        if (!is_string($description)) {
            throw new \InvalidArgumentException('Product description is required and must be a string.');
        }

        $price = $data['price'] ?? null;
        if (!is_float($price)) {
            throw new \InvalidArgumentException('Product price is required and must be a float.');
        }

        return new self($name, $description, $price);
    }
}
