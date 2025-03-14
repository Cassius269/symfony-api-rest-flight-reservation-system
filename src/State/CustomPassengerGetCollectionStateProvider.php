<?php

namespace App\State;

use App\Dto\PassengerResponseDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomPassengerGetCollectionStateProvider implements ProviderInterface
{
    // Injection de dépendances
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $collectionProvider
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupérer la liste des passagers
        $data = $this->collectionProvider->provide($operation, $uriVariables, $context);

        // Retourner au client un résultat contenant une réponse avec DTO
        $result = [];

        foreach ($data as $passenger) {
            // Créer un DTO
            $passengerDto = new PassengerResponseDto;
            $passengerDto->id = $passenger->getId();
            $passengerDto->firstname = $passenger->getFirstname();
            $passengerDto->lastname = $passenger->getLastname();
            $passengerDto->email = $passenger->getEmail();

            $result[] = $passengerDto;
        }

        return $result;
    }
}
