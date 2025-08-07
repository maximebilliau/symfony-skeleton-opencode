<?php

declare(strict_types=1);

namespace App\User\Interface\API\Controller;

use App\Shared\Application\Bus\Query\QueryBus;
use App\User\Application\DTO\RefreshTokenData;
use App\User\Application\Query\RefreshTokenQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

#[Route('/api/token/refresh', name: 'api_refresh_token', methods: ['POST'])]
final class RefreshTokenController extends AbstractController
{
    public function __construct(
        private readonly QueryBus $queryBus
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $refreshTokenData = RefreshTokenData::fromJson($request->getContent());
            $newToken = $this->queryBus->ask(new RefreshTokenQuery($refreshTokenData->token));

            return new JsonResponse([
                'token' => $newToken,
            ]);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (AuthenticationException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_UNAUTHORIZED);
        } catch (\Exception) {
            return new JsonResponse([
                'message' => 'An error occurred while refreshing the token',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
