<?php

namespace App\Dto;

use DateTime;

class FlightResponseDto
{

    public ?CityRequestDto $cityDeparture = null; // ville de départ

    public ?CityRequestDto $cityArrival = null; // ville d'arrivée

    public ?int $airplaneId = null;

    public ?DateTime $dateDeparture = null;

    public ?DateTime $dateArrival = null;
}
