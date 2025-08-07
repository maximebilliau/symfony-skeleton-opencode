<?php

declare(strict_types=1);

namespace App\Product\Interface\API\Controller;

use App\Product\Application\Command\CreateProductCommand;
use App\Product\Application\DTO\ProductData;
use App\Shared\Application\Bus\Command\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products', name: 'app_product_create', methods: ['POST'])]
final readonly class CreateProductController
{
    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $command = new CreateProductCommand(
            ProductData::fromJson($request->getContent()),
        );

        $this->commandBus->dispatch($command);

        return new JsonResponse([
            'message' => 'Product created successfully',
        ], Response::HTTP_CREATED);
    }
}
