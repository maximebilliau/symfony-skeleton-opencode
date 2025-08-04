<?php

declare(strict_types=1);

namespace App\Product\Application\CommandHandler;

use App\Product\Domain\ProductId;
use Symfony\Component\Uid\UuidV4;

#[AsMessageHandler]
final readonly class CreateProductCommandHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function __invoke(CreateProductCommand $command): void
    {
        $product = new Product(
            id: new ProductId(value: (string) new UuidV4()),
            name: $command->getName(),
            description: $command->getDescription(),
            price: $command->getPrice()
        );

        $this->productRepository->save($product);
    }
}
