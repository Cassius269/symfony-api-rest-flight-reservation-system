<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\AirplaneResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AirplaneStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider
    ) {}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $data = $this->itemProvider->provide($operation, $uriVariables, $context);

        $airplaneDto = new AirplaneResponseDto;
        $airplaneDto->id = $data->getId();
        $airplaneDto->model = $data->getModel();
        $airplaneDto->capacity = $data->getCapacity();

        return $airplaneDto;
    }
}
