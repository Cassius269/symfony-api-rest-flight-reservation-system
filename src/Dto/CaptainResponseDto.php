<?php

namespace App\Dto;

use DateTime;
use DateTimeImmutable;

// Création d'un DTO pour retourner les données d'un Commandant de bord au client
class CaptainResponseDto
{
    public ?int $id = null;
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $email = null;
    public ?DateTimeImmutable $createdAt = null;
    public ?DateTime $updatedAt = null;
}
