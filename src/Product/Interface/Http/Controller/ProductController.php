<?php

declare(strict_types=1);

namespace App\Product\Interface\Http\Controller;

use App\Product\Application\Command\CreateProductCommand;
use App\Product\Application\DTO\ProductData;
use App\Product\Application\Query\GetProductQuery;
use App\Product\Domain\Product;
use App\Product\Domain\ProductId;
use App\Shared\Application\Bus\Command\CommandBus;
use App\Shared\Application\Bus\Query\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
        $command = new CreateProductCommand(
            ProductData::fromJson($request->getContent()),
        );

        $this->commandBus->dispatch($command);

        return $this->json([
            'message' => 'Product created successfully',
        ], Response::HTTP_CREATED);
    }

    #[Route('/products/{id}', name: 'app_product_get', methods: ['GET'])]
    public function getProduct(string $id): JsonResponse
    {
        /** @var Product|null $product */
        $product = $this->queryBus->ask(new GetProductQuery(ProductId::fromString($id)));

        if ($product === null) {
            return $this->json([
                'message' => 'Product not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $product->getId()->toString(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
        ]);
    }
}
