<?php

namespace App\Dto;

// Création d'un DTO pour retourner les données d'une nouvelle ville créée
class CityResponseDto
{
    public ?int $id = null;
    public ?string $name =  null;
    public ?string $zipCode = null;
    public ?string $countryName = null;
}
