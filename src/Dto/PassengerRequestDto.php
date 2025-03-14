<?php

namespace App\Dto;

use DateTimeImmutable;

// Création d'un DTO pour récupérer les données d'un passager entrées côtés client
class PassengerRequestDto
{
    public ?string $firstname = null;
    public ?string $lastname = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?DateTimeImmutable $createdAt = null;
}
