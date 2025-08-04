<?php

declare(strict_types=1);

namespace App\Product\Interface\Http\Controller;

use App\Product\Application\Command\CreateProductCommand;
use App\Product\Application\Query\GetProductQuery;
use App\Shared\Application\Bus\Command\CommandBus;
use App\Shared\Application\Bus\Query\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Product\Domain\ProductId;

class ProductController extends AbstractController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {
    }

    #[Route('/products', name: 'app_product_create', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new CreateProductCommand(
            name: $data['name'],
            description: $data['description'],
            price: $data['price']
        );

        $this->commandBus->dispatch($command);

        return $this->json([
            'message' => 'Product created successfully',
        ], Response::HTTP_CREATED);
    }

    #[Route('/products/{id}', name: 'app_product_get', methods: ['GET'])]
    public function getProduct(string $id): JsonResponse
    {
        $product = $this->queryBus->ask(new GetProductQuery(ProductId::fromString($id)));

        if ($product === null) {
            return $this->json([
                'message' => 'Product not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
        ]);
    }
}
