<?php

namespace App\Dto;

use App\Dto\CityRequestDto;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

// Création d'un DTO pour le recueille des informations de la requête de création d'un nouveau vol d'avion

class FlightRequestDto
{
    public ?int $id = null;
    public ?int $airplaneId = null;
    public ?AirportResponseDto $airportArrival = null; // aéroport d'arrivée
    public ?AirportResponseDto $airportDeparture = null; // aéroport de départ
    public ?DateTime $dateDeparture = null;
    public ?DateTime $dateArrival = null;
}
