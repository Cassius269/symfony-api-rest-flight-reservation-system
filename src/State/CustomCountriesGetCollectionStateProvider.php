<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CountryResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomCountriesGetCollectionStateProvider implements ProviderInterface
{
    // Injection de dépendance(s)
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $providerInterface
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupérer les données
        $data = $this->providerInterface->provide($operation, $uriVariables, $context);

        // Utiliser un DTO plutôt que l'entité Country
        $results = [];

        foreach ($data as $country) {
            $countryDto = new CountryResponseDto;
            $countryDto->id = $country->getId();
            $countryDto->name = $country->getName();
            $countryDto->isoCode = $country->getIsoCode();
            $countryDto->timezone = $country->getTimezone();

            $results[] = $countryDto;
        }

        // Retourner le résultat 
        return $results;
    }
}
