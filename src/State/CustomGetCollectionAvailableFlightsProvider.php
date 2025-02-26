<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\AvailableFlightResponseDto;
use App\Dto\CityResponseDto;
use App\Dto\FlightResponseDto;
use App\Repository\FlightRepository;

class CustomGetCollectionAvailableFlightsProvider implements ProviderInterface
{
    // Injection de dépendances
    public function __construct(
        private FlightRepository $flightRepository
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // 1ère méthode : retourner un resultat direct des résultats de la recherche personnalisée Doctrine
        // Rechercher tous les vols disponibles 
        $availableFlights = $this->flightRepository->findAvailableFlights();
        // return $availableFlights; // retourner le résultat au client

        // 2ème méthode: utilisation de DTO
        $availableFlightsResults = [];

        foreach ($availableFlights as $flight) {
            $flightDto = new AvailableFlightResponseDto();
            $flightDto->id = $flight['id'];

            $cityDepartureDto = new CityResponseDto();
            $cityDepartureDto->name = $flight['cityDeparture'];

            $cityArrivalDto = new CityResponseDto();
            $cityArrivalDto->name = $flight['cityArrival'];

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
