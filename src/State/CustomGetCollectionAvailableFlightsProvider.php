<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\FlightRepository;

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

        return $availableFlights; // retourner le résultat au client
    }
}
