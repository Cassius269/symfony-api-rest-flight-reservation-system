<?php

namespace App\Dto;

// Création d'un DTO pour retourner les données de pays
class CountryResponseDto
{
    public ?int $id = null;
    public ?string $name = null;
    public ?string $isoCode = null;
    public ?string $timezone = null;
}
