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

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, CommandBus $commandBus): JsonResponse
    {
        $user = UserData::fromJson($request->getContent());

        if (!$user->email || !$user->password) {
            return new JsonResponse([
                'message' => 'Missing email or password',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $result = $commandBus->dispatch(new LoginCommand($user->email, $user->password));

            return new JsonResponse($result);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }
    }
}
