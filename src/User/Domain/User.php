<?php

declare(strict_types=1);

namespace App\User\Domain;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @param array<string> $roles
     */
    public function __construct(
        private readonly UserId $id,
        private readonly string $email,
        private string $password,
        private readonly array $roles,
    ) {
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getUserIdentifier(): string
    {
        if ($this->email === '' || $this->email === '0') {
            throw new \LogicException('User email cannot be empty');
        }

        return $this->email;
    }

    public function eraseCredentials(): void
    {
        $this->password = '';
    }
}
