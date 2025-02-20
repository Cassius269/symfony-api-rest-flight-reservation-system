<?php

namespace App\Dto;

use DateTime;

// Création d'un DTO pour retourner les données d'une nouveau vol créée
class FlightResponseDto
{
    public ?int $airplaneId = null;
    public ?CityRequestDto $cityDeparture = null; // ville de départ
    public ?CityRequestDto $cityArrival = null; // ville d'arrivée
    public ?DateTime $dateDeparture = null;
    public ?DateTime $dateArrival = null;
}
