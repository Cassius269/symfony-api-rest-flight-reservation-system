<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

// Création d'un DTO pour le recueille des données de création d'un nouvel aéroport
class AirportRequestDto
{
    public string $name;
    public string $codeIata;
    public CityRequestDto $city;
}
