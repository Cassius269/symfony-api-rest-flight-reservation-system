<?php

namespace App\State;

use App\Dto\CityResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Repository\FlightRepository;
use App\Dto\AvailableFlightResponseDto;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomGetCollectionAvailableFlightsProvider implements ProviderInterface
{
    // Injection de dépendances
    public function __construct(
        private FlightRepository $flightRepository
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Rechercher tous les vols disponibles 
        $availableFlights = $this->flightRepository->findAvailableFlights();

        if (!$availableFlights) {
            throw new NotFoundHttpException('Aucun vol disponible trouvé');
        }

        $availableFlightsResults = [];

        foreach ($availableFlights as $flight) {
            $flightDto = new AvailableFlightResponseDto();
            $flightDto->id = $flight['id'];

            // dd($flight);
            $cityDepartureDto = new CityResponseDto();
            $cityDepartureDto->name = $flight["airportDeparture"];

            $cityArrivalDto = new CityResponseDto();
            $cityArrivalDto->name = $flight["airportArrival"];

            $flightDto->cityDeparture = $cityDepartureDto;
            $flightDto->cityArrival = $cityArrivalDto;


            $flightDto->dateDeparture = $flight['dateDeparture'];
            $flightDto->dateArrival = $flight['dateArrival'];

            $flightDto->capacity = $flight['capacity'];
            $flightDto->passengerCount = $flight['passengerCount'];

            $availableFlightsResults[] = $flightDto;
        }

        return $availableFlightsResults;
    }
}
