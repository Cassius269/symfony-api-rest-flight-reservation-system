<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CityResponseDto;
use App\Dto\FlightResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomFlightsGetCollection implements ProviderInterface
{
    // Injection de dépendance(s)
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $providerInterface
    ){}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?array
    {
        // Récupérer les ressources dans le serveur
        $data = $this->providerInterface->provide($operation,$uriVariables, $context);

        // dd($data);

        // Préparer la réponse à envoyer au client
        $response = [];
        
        foreach($data as $flight){
            // dd($flight);
            $cityDepartureDto = new CityResponseDto;
            $cityDepartureDto->name = $flight->getAirportArrival()->getName();
            $cityDepartureDto->countryName = $flight->getAirportArrival()->getCity()->getCountry()->getName();

            $cityArrivalDto = new CityResponseDto;            
            $cityArrivalDto->name = $flight->getAirportArrival()->getCity()->getName();
            $cityArrivalDto->countryName = $flight->getAirportArrival()->getCity()->getName();

            $flightDto = new FlightResponseDto;
            $flightDto->id = $flight->getId();
            $flightDto->cityDeparture = $cityDepartureDto;
            $flightDto->cityArrival = $cityArrivalDto;
            $flightDto->dateDeparture = $flight->getDateDeparture();
            $flightDto->dateArrival = $flight->getDateArrival();

            $response [] = $flightDto;
    }

    return $response;
}

}