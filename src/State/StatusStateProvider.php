<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\StatusResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class StatusStateProvider implements ProviderInterface
{
    // Injection de dépdenance(s)
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupérer la ressource
        $data = $this->itemProvider->provide($operation, $uriVariables, $context);
        // dd($data);

        // Préparer la réponse à retourner au client
        $statusDto = new StatusResponseDto;
        $statusDto->id = $data->getId();
        $statusDto->name = $data->getName();
        $statusDto->createdAt = $data->getCreatedAt();
        $statusDto->updatedAt = $data->getUpdatedAt();

        return $statusDto;
    }
}
