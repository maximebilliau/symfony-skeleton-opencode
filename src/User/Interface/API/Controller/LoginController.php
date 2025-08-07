<?php

declare(strict_types=1);

namespace App\User\Interface\API\Controller;

use App\Shared\Application\Bus\Command\CommandBus;
use App\User\Application\Command\LoginCommand;
use App\User\Application\DTO\UserData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/login', name: 'api_login', methods: ['POST'])]
final class LoginController extends AbstractController
{
    public function __construct(
        private readonly CommandBus $commandBus
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = UserData::fromJson($request->getContent());

        if (!$user->email || !$user->password) {
            return new JsonResponse([
                'message' => 'Missing email or password',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $result = $this->commandBus->dispatch(new LoginCommand($user->email, $user->password));

            return new JsonResponse([
                'token' => $result,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }
    }
}
