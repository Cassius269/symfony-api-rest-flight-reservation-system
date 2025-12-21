<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CopilotResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CopilotStateProvider implements ProviderInterface
{
    // Injection de dépendances
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider
    ) {}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?object
    {
        // Récuperer le copilote
        $data = $this->itemProvider->provide($operation, $uriVariables, $context);

        // dd($data);
        // Préparer la réponse à renvoyer au client
        $copilotDto = new CopilotResponseDto;
        $copilotDto->id = $data->getId();
        $copilotDto->firstname = $data->getFirstname();
        $copilotDto->lastname = $data->getLastname();
        $copilotDto->email = $data->getEmail();

        return $copilotDto;
    }
}
