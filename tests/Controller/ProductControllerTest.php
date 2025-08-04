<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Product\Application\Command\CreateProductCommand;
use App\Product\Application\Query\GetProductQuery;
use App\Product\Domain\Product;
use App\Product\Domain\Repository\ProductRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private MockObject|MessageBusInterface $messageBus;
    private MockObject|ProductRepositoryInterface $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        // Mock the MessageBus and ProductRepository
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);

        // Replace the real services with their mocks
        static::getContainer()->set(MessageBusInterface::class, $this->messageBus);
        static::getContainer()->set(ProductRepositoryInterface::class, $this->productRepository);
    }

    public function testCreateProduct(): void
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'This is a test product.',
            'price' => 19.99,
        ];

        // Expect the CreateProductCommand to be dispatched
        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CreateProductCommand::class));

        $this->client->request('POST', '/products', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($productData));

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"message": "Product created successfully"}', $this->client->getResponse()->getContent());
    }

    public function testGetProduct(): void
    {
        $productId = '123';
        $product = new Product($productId, 'Test Product', 'This is a test product.', 19.99);

        // Expect the GetProductQuery to be dispatched and return a product
        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(GetProductQuery::class))
            ->willReturn($product); // This return value needs to be adjusted based on how the message bus handles query results

        // Mock the repository to return a product when findById is called
        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn($product);

        $this->client->request('GET', '/products/' . $productId);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
        ]), $this->client->getResponse()->getContent());
    }

    public function testGetProductNotFound(): void
    {
        $productId = 'nonexistent_id';

        // Expect the GetProductQuery to be dispatched and return null
        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(GetProductQuery::class))
            ->willReturn(null); // Adjust if your query bus returns a different structure for not found

        // Mock the repository to return null when findById is called
        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn(null);

        $this->client->request('GET', '/products/' . $productId);

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString('{"message": "Product not found"}', $this->client->getResponse()->getContent());
    }
}
