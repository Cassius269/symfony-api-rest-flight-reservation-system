<?php

namespace App\Dto;

use DateTimeInterface;

class AvailableFlightResponseDto
{
    public ?int $id;
    public DateTimeInterface $dateDeparture;
    public DateTimeInterface $dateArrival;
    public ?CityResponseDto $cityDeparture;
    public ?CityResponseDto $cityArrival;
    public ?int $capacity;
    public ?int $passengerCount;
}
