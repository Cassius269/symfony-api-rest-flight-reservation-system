<?php

namespace App\Dto;

// Création d'un DTO pour le transfert de données lors de la récupération de ressource de type Avion
class AirplaneModelRequestDto
{
    public ?string $model = null;
    public ?int $capacity = null;
}
