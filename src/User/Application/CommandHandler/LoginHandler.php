<?php

declare(strict_types=1);

namespace App\User\Application\CommandHandler;

use App\User\Application\Command\LoginCommand;
use App\User\Domain\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

#[AsMessageHandler]
class LoginHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface $jwtManager
    ) {
    }

    public function __invoke(LoginCommand $command): string
    {
        $user = $this->userRepository->findByEmail($command->getUsername());

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $command->getPassword())) {
            throw new AuthenticationException('Invalid credentials.');
        }

        return $this->jwtManager->create($user);
    }
}
