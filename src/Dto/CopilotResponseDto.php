<?php

namespace App\Dto;

use DateTimeImmutable;

// Création d'un DTO pour retourner la réponse d'une ressource de type Copilote d'un vol
class CopilotResponseDto
{
    // Rendre publiques les données que l'on souhaite traiter
    public ?int $id = null;
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $email = null;
    public ?DateTimeImmutable $createdAt = null;
}
