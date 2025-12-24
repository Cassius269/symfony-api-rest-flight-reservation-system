<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\AirportResponseDto;
use App\Dto\CityResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomAirportsGetCollectionProvider implements ProviderInterface
{
    // Injection de dépendance
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $provider
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Retrieve the state from somewhere
        $data = $this->provider->provide($operation, $uriVariables, $context);

        // dd($data);

        // Retourner la réponse au client sous la forme d'une collection de de DTOS
        $response = [];
        foreach ($data as $airport) {
            $airportDto = new AirportResponseDto;
            $airportDto->id = $airport->getId();
            $airportDto->name = $airport->getName();

            $cityDto = new CityResponseDto;
            $cityDto->name = $airport->getCity()->getName();
            $cityDto->countryName = $airport->getCity()->getCountry()->getName();


            $airportDto->city = $cityDto;


            $response[] = $airportDto;
        }

        return $response;
    }
}
