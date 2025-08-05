<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Fixtures;

use App\Product\Domain\Product;
use App\Product\Domain\ProductId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create sample products
        $products = [
            new Product(ProductId::fromString('6a032bfd-19bc-4af9-9841-f6b7d7c18afd'), 'Product 1', 'Description for product 1', 19.99),
            new Product(ProductId::fromString('6a032bfd-19bc-4af9-9841-f6b7d7c18afb'), 'Product 2', 'Description for product 2', 29.99),
            new Product(ProductId::fromString('6a032bfd-19bc-4af9-9841-f6b7d7c18afc'), 'Product 3', 'Description for product 3', 39.99),
        ];

        foreach ($products as $product) {
            $manager->persist($product);
        }

        $manager->flush();
    }
}
