<?php

namespace App\Dto;

use DateTime;
use DateTimeImmutable;

// Création d'un DTO pour retourner la réponse d'une ressource de type User de l'API
class UserResponseDto
{
    // Les données à rendre accessible au client (exple navigateur, Postman)
    public ?int $id = null;
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?array $roles = null;
    public ?string $email = null;
    public ?DateTimeImmutable $createdAt = null;
    public ?DateTime $updtatedAt = null;
}
