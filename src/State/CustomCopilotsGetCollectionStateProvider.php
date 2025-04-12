<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CopilotResponseDto;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CustomCopilotsGetCollectionStateProvider implements ProviderInterface
{
    // Injection de dépendance(s)
    public function __construct(
        private Security $security,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $collectionProvider
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récuperer la liste des ressources
        $data = $this->collectionProvider->provide($operation, $uriVariables, $context);

        // Retourner la réponse au client 
        $response = [];

        foreach ($data as $copilot) {
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
