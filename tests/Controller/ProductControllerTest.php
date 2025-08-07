<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Product\Application\DTO\ProductData;
use App\Tests\FixturesTrait;
use App\Tests\Integration\Common\Infrastructure\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ProductControllerTest extends WebTestCase
{
    use FixturesTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateProductSuccess(): void
    {
        $productData = new ProductData('Test Product', 'Test Description', 99.99);

        $this->authenticatedRequest(
            method: 'POST',
            uri: '/api/products',
            content: json_encode($productData) ?: null,
            headers: [
                'CONTENT_TYPE' => 'application/json',
            ],
            emailForBearerToken: 'admin@skeleton.com'
        );
        $responseData = $this->getDecodedResponseContent();

        // Assertions
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertSame('Product created successfully', $responseData['message']);
    }

    public function testGetProductSuccess(): void
    {
        $this->authenticatedRequest('GET', '/api/products/6a032bfd-19bc-4af9-9841-f6b7d7c18afd', emailForBearerToken: 'admin@skeleton.com');
        $responseData = $this->getDecodedResponseContent();

        // Assertions
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertIsString($responseData['id']);
        $this->assertUuid($responseData['id']);
        $this->assertSame('6a032bfd-19bc-4af9-9841-f6b7d7c18afd', $responseData['id']);
        $this->assertEquals('Product 1', $responseData['name']);
        $this->assertEquals('Description for product 1', $responseData['description']);
        $this->assertEqualsWithDelta(19.99, $responseData['price'], PHP_FLOAT_EPSILON);
    }

    public function testGetProductNotFound(): void
    {
        $productId = Uuid::v4();

        $this->authenticatedRequest('GET', "/api/products/{$productId}", emailForBearerToken: 'admin@skeleton.com');
        $responseData = $this->getDecodedResponseContent();

        // Assertions
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertEquals('Product not found', $responseData['message']);
    }
}
