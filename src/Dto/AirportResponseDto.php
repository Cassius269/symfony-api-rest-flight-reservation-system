<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

// Création d'un DTO pour le recueille des données de création d'une nouvelle ville
class AirportResponseDto
{
    public ?string $name = null;
    public ?string $country = null;
}
