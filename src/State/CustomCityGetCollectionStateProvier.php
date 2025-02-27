<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CityResponseDto;
use App\Repository\CityRepository;

class CustomCityGetCollectionStateProvier implements ProviderInterface
{
    // Injection de dépendances
    public function __construct(private CityRepository $cityRepository) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Rechercher toutes les villes
        $cities = $this->cityRepository->findAll();

        $results = [];

        foreach ($cities as $city) {
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
