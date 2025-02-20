<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\FlightRequestDto;

// Création d'un DTO pour le transfert de données lors de la récupération de ressource de type Réservation
class ReservationRequestDto
{
    public ?string $price = null;
    public ?PassengerRequestDto $passenger = null;
    public ?FlightRequestDto $flight = null;
}
