<?php

declare(strict_types=1);

namespace App\Product\Application\CommandHandler;

use App\Product\Application\Command\CreateProductCommand;
use App\Product\Domain\Product;
use App\Product\Domain\ProductId;
use App\Product\Domain\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

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
            id: ProductId::fromString(Uuid::v4()->toString()),
            name: $command->getName(),
            description: $command->getDescription(),
            price: $command->getPrice()
        );

        $this->productRepository->save($product);
    }
}
