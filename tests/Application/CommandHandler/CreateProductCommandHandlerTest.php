<?php

declare(strict_types=1);

namespace App\Tests\Application\CommandHandler;

use App\Product\Application\Command\CreateProductCommand;
use App\Product\Application\CommandHandler\CreateProductCommandHandler;
use App\Product\Domain\Product;
use App\Product\Domain\Repository\ProductRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateProductCommandHandlerTest extends TestCase
{
    private MockObject|ProductRepositoryInterface $productRepository;
    private CreateProductCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->handler = new CreateProductCommandHandler($this->productRepository);
    }

    public function testHandle(): void
    {
        $command = new CreateProductCommand('Test Product', 'This is a test product.', 19.99);

        // Expect the repository's save method to be called with a Product object
        $this->productRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Product::class));

        // Call the handler
        ($this->handler)($command);
    }
}
