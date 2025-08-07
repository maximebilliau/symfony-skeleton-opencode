<?php

declare(strict_types=1);

namespace App\Product\Interface\API\Controller;

use App\Product\Application\Query\GetProductQuery;
use App\Product\Domain\Product;
use App\Product\Domain\ProductId;
use App\Shared\Application\Bus\Query\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products/{id}', name: 'app_product_get', methods: ['GET'])]
final readonly class GetProductController
{
    public function __construct(
        private QueryBus $queryBus
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        /** @var Product|null $product */
        $product = $this->queryBus->ask(new GetProductQuery(ProductId::fromString($id)));

        if ($product === null) {
            return new JsonResponse([
                'message' => 'Product not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $product->getId()->toString(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
        ]);
    }
}
