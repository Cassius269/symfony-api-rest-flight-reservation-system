<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CopilotResponseDto;
use App\Entity\Copilot;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomCopilotsGetCollectionProvider implements ProviderInterface
{
    // Injection de dépendances
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $providerInterface
    ){}
    
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?array
    {
        // Récupérer les données
        $data = $this->providerInterface->provide($operation, $uriVariables, $context);

        $response = [];

        // Préparer la réponse à retourner au client
        foreach($data as $copilot){
             $copilotDto = new CopilotResponseDto;
            $copilotDto->id = $copilot->getId();
            $copilotDto->firstname = $copilot->getFirstname();
            $copilotDto->lastname = $copilot->getLastname();
            $copilotDto->email = $copilot->getEmail();


            $response[] = $copilotDto;
        }

        return $response;
    }
}
