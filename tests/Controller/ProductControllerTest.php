<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Product\Application\Command\CreateProductCommand;
use App\Product\Application\Query\GetProductQuery;
use App\Product\Domain\ProductId;
use App\Shared\Application\Bus\Command\CommandBus;
use App\Shared\Application\Bus\Query\QueryBus;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ProductControllerTest extends WebTestCase
{
    private CommandBus|MockObject $commandBus;
    private QueryBus|MockObject $queryBus;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les mocks des bus
        $this->commandBus = $this->createMock(CommandBus::class);
        $this->queryBus = $this->createMock(QueryBus::class);
    }

    private function createClientWithMockedServices(): KernelBrowser
    {
        $client = static::createClient();

        // Remplacer les services dans le conteneur
        self::getContainer()->set(CommandBus::class, $this->commandBus);
        self::getContainer()->set(QueryBus::class, $this->queryBus);

        return $client;
    }

    public function testCreateProductSuccess(): void
    {
        $client = $this->createClientWithMockedServices();

        // Données du produit à créer
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
        ];

        // Vérifier que le command bus est appelé avec la bonne commande
        $this->commandBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(fn (CreateProductCommand $command) => $command->name === $productData['name']
                && $command->description === $productData['description']
                && $command->price === $productData['price']));

        // Faire la requête POST
        $client->request(
            'POST',
            '/products',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($productData)
        );

        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // Assertions
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('Product created successfully', $responseData['message']);
    }

    public function testGetProductSuccess(): void
    {
        $client = $this->createClientWithMockedServices();
        $productId = 'fe4c6f31-cfa9-4924-b427-effe3da8e8d5';

        // Mock du produit retourné
        $mockProduct = $this->createMockProduct();

        // Configurer le query bus pour retourner le produit
        $this->queryBus
            ->expects($this->once())
            ->method('ask')
            ->with($this->isInstanceOf(GetProductQuery::class))
            ->willReturn($mockProduct);

        // Faire la requête GET
        $client->request('GET', "/products/{$productId}");

        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // Assertions
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('fe4c6f31-cfa9-4924-b427-effe3da8e8d5', $responseData['id']);
        $this->assertEquals('Test Product', $responseData['name']);
        $this->assertEquals('Test Description', $responseData['description']);
        $this->assertEqualsWithDelta(99.99, $responseData['price'], PHP_FLOAT_EPSILON);
    }

    public function testGetProductNotFound(): void
    {
        $client = $this->createClientWithMockedServices();
        $productId = Uuid::v4();

        // Configurer le query bus pour retourner null
        $this->queryBus
            ->expects($this->once())
            ->method('ask')
            ->with($this->isInstanceOf(GetProductQuery::class))
            ->willReturn(null);

        // Faire la requête GET
        $client->request('GET', "/products/{$productId}");

        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        // Assertions
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals('Product not found', $responseData['message']);
    }

    private function createMockProduct(): object
    {
        return new class {
            public function getId(): ProductId
            {
                return ProductId::fromString('fe4c6f31-cfa9-4924-b427-effe3da8e8d5');
            }

            public function getName(): string
            {
                return 'Test Product';
            }

            public function getDescription(): string
            {
                return 'Test Description';
            }

            public function getPrice(): float
            {
                return 99.99;
            }
        };
    }
}
