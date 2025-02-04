<?php

namespace App\Dto;

// Création d'un DTO pour retourner les données d'un nouvel avion créé
class AirplaneResponseDto
{
    public ?string $model = null;
    public ?int $capacity = null;
}
