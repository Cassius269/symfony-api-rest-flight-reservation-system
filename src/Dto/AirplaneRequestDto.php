<?php

namespace App\Dto;

// Création d'un DTO pour récupréer les données d'un nouvel avion 
class AirplaneRequestDto
{
    public ?string $model = null;
    public ?string $reference = null;
}
