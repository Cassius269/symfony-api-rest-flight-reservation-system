<?php

namespace App\Dto;

use DateTimeImmutable;

// Création d'un DTO pour le transfert de données lors de la récupération de ressource de type Réservation
class ReservationResponseDto
{
    public ?int $id = null;
    public ?string $numberFlightSeat = null;
    public ?float $price = null;
    public ?PassengerResponseDto $passenger = null;
    public ?FlightResponseDto $flight = null;
    public ?string $status = null;
    public ?string $passengerNameRecord = null;
    public ?\DateTimeImmutable $createdAt = null;
    public ?\DateTime $updatedAt = null;
}
