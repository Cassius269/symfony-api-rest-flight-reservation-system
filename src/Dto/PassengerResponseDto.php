<?php

namespace App\Dto;

use DateTimeImmutable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class PassengerResponseDto implements PasswordAuthenticatedUserInterface
{
    // Rendre publiques les données que l'on souhaite traiter
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?DateTimeImmutable $createdAt = null;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
}
