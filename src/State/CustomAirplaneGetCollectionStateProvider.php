<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\AirplaneResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomAirplaneGetCollectionStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $providerInterface
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $data = $this->providerInterface->provide($operation, $uriVariables, $context);
        // dd($data);

        $results = [];
        foreach ($data as $airplane) {
            $airplaneDto = new AirplaneResponseDto;
            $airplaneDto->id = $airplane->getId();
            $airplaneDto->model = $airplane->getModel();
            $airplaneDto->capacity = $airplane->getCapacity();

            $results[] = $airplaneDto;
        }

        return $results;
    }
}
