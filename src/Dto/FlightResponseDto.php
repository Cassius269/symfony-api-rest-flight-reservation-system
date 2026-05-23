<?php

namespace App\Dto;

use DateTime;
use DateTimeImmutable;

// Création d'un DTO pour retourner les données d'une nouveau vol créée
class FlightResponseDto
{
    public ?int $id = null;
    public ?string $airplaneModel = null;
    public ?CompanyResponseDto $company = null;
    public ?CaptainResponseDto $captain = null;
    public ?AirportResponseDto $airportArrival = null; // aéroport d'arrivée
    public ?AirportResponseDto $airportDeparture = null; // aéroport de départ
    public ?DateTime $dateDeparture = null;
    public ?DateTime $dateArrival = null;
    public ?float $price = null;
    public ?string $status = null;
    public ?bool $isDirect = null;
    public ?bool $isCanceled = null;
    public ?bool $isLate = null;
    public ?DateTimeImmutable $createdAt = null;
    public ?DateTime $updatedAt = null;
}
