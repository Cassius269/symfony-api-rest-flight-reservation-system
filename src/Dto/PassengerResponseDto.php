<?php

namespace App\Dto;

use DateTimeImmutable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

// Création d'un DTO pour retourner la réponse d'une ressource de type Passager d'un vol
class PassengerResponseDto
{
    // Rendre publiques les données que l'on souhaite traiter
    public ?int $id = null;
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $email = null;
}
