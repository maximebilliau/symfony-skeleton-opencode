<?php

declare(strict_types=1);

namespace App\Tests\Application\QueryHandler;

use App\Product\Application\Query\GetProductQuery;
use App\Product\Application\QueryHandler\GetProductQueryHandler;
use App\Product\Domain\Product;
use App\Product\Domain\ProductId;
use App\Product\Domain\Repository\ProductRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class GetProductQueryHandlerTest extends TestCase
{
    private MockObject|ProductRepositoryInterface $productRepository;
    private GetProductQueryHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->handler = new GetProductQueryHandler($this->productRepository);
    }

    public function testHandle(): void
    {
        $productId = ProductId::fromString(Uuid::v4()->toString());
        $product = new Product($productId, 'Test Product', 'This is a test product.', 19.99);

        // Expect the repository's findById method to be called with the correct ID
        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn($product);

        // Call the handler
        $result = ($this->handler)(new GetProductQuery($productId));

        $this->assertInstanceOf(Product::class, $result);
        $this->assertSame($productId, $result->getId());
    }

    public function testHandleProductNotFound(): void
    {
        $productId = ProductId::fromString(Uuid::v4()->toString());

        // Expect the repository's findById method to be called and return null
        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn(null);

        // Call the handler
        $result = ($this->handler)(new GetProductQuery($productId));

        $this->assertNotInstanceOf(Product::class, $result);
    }
}
