<?php

namespace App\Dto;

// Création d'un DTO pour le transfert de données lors de la création d'une nouvelle ressource Avion
class AirplaneResponseDto
{
    public ?string $model = null;
    public ?int $capacity = null;
}
