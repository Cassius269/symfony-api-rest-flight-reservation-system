<?php

namespace App\Dto;

use DateTime;

// Création d'un DTO pour le recueille des informations de la requête de création d'un nouveau vol d'avion

class FlightRequestDto
{
    public ?int $id = null;
    public ?AirplaneRequestDto $airplane = null;
    public ?AirportResponseDto $airportDeparture = null; // aéroport de départ
    public ?AirportResponseDto $airportArrival = null; // aéroport d'arrivée
    public ?CompanyRequestDto $company = null;
    public ?CaptainRequestDto $captain = null;
    public ?DateTime $dateDeparture = null;
    public ?DateTime $dateArrival = null;
    public ?array $copilots = null;
}
