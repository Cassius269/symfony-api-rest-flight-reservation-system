<?php

namespace App\Dto;

use App\Dto\FlightRequestDto;

// Création d'un DTO pour le transfert de données lors de la récupération de ressource de type Réservation
class ReservationResponseDto
{
    public ?int $id = null;
    public ?string $numberFlightSeat = null;
    public ?float $price = null;
    public ?PassengerResponseDto $passenger = null;
    public ?FlightResponseDto $flight = null;
}
