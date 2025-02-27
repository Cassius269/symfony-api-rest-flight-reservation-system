<?php

namespace App\State;

use App\Dto\CityResponseDto;
use App\Repository\CityRepository;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\Pagination\ArrayPaginator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomCityGetCollectionStateProvier implements ProviderInterface
{
    // Injection de dépendances
    public function __construct(
        private CityRepository $cityRepository,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $providerInterface
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Rechercher toutes les villes
        $data = $this->providerInterface->provide($operation, $uriVariables, $context);

        $results = [];

        foreach ($data as $city) {
            $cityDto = new CityResponseDto;
            $cityDto->id = $city->getId();
            $cityDto->name = $city->getName();
            $cityDto->zipCode = $city->getZipCode();
            $cityDto->countryName = $city->getCountry()->getName();


            $results[] = $cityDto; // stocker le DTO de chaque ville dans un tableau de résultat
        }

        return $results; // renvoyer le résultat au client
    }
}
