<?php

namespace App\Dto;

use DateTime;
use DateTimeImmutable;

// Création d'un DTO pour le recueille des données de création d'un nouvel aéroport
class AirportResponseDto
{
    public int $id;
    public string $name;
    public string $codeIata;
    public CityResponseDto $city;
    public ?DateTimeImmutable $createdAt = null;
    public ?DateTime $updatedAt = null;
}
