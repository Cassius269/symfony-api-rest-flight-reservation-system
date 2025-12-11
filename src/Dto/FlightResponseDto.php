<?php

namespace App\Dto;

use DateTime;

// Création d'un DTO pour retourner les données d'une nouveau vol créée
class FlightResponseDto
{
    public ?int $id = null;
    public ?int $airplaneId = null;
    public ?AirportResponseDto $airportArrival = null; // aéroport d'arrivée
    public ?AirportResponseDto $airportDeparture = null; // aéroport de départ
    public ?DateTime $dateDeparture = null;
    public ?DateTime $dateArrival = null;
}
