<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Dto\AirplaneModelResponseDto;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomAirplaneGetCollectionStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $collectionProvider
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $data = $this->collectionProvider->provide($operation, $uriVariables, $context);
        // dd($data);

        $results = [];
        foreach ($data as $airplane) {
            $airplaneDto = new AirplaneModelResponseDto;
            $airplaneDto->id = $airplane->getId();
            $airplaneDto->model = $airplane->getModel();
            $airplaneDto->capacity = $airplane->getCapacity();

            $results[] = $airplaneDto;
        }

        return $results;
    }
}
