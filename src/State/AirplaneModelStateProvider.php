<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Dto\AirplaneModelResponseDto;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AirplaneModelStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider
    ) {}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $data = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (!$data) {
            throw new NotFoundHttpException('Aucun avion retrouvé avec l\'id fourni');
        }

        $airplaneDto = new AirplaneModelResponseDto;
        $airplaneDto->id = $data->getId();
        $airplaneDto->model = $data->getModel();
        $airplaneDto->capacity = $data->getCapacity();

        return $airplaneDto;
    }
}
