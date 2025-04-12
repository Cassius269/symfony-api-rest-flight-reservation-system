<?php

namespace App\State;

use App\Dto\CopilotResponseDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CopilotStateProvider implements ProviderInterface
{
    //Injection de dépendance(s)
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider,
        private Security $securiy
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Récuperer la donnée grâce à son ID
        $data = $this->itemProvider->provide($operation, $uriVariables, $context);

        // Vérifier si l'utilisateur authentifié a la permission de récupérer les informations d'un copilote
        if (!$this->securiy->isGranted('COPILOT_VIEW', $data)) {
            throw new AccessDeniedHttpException('Accès refusé car vous n\'êtes ni Admin ni propriétaire des données personnelles');
        }


        // Gérer l'ereur en cas de ressource non trouvée
        if (!$data) {
            throw new NotFoundHttpException('Aucune ressource trouvée avec l\'ID fourni');
        }

        // Préparation la réponse à envoyer au client
        $copilotDto = new CopilotResponseDto;
        $copilotDto->id = $data->getId();
        $copilotDto->firstname = $data->getFirstname();
        $copilotDto->lastname = $data->getLastname();
        $copilotDto->email = $data->getEmail();

        return $copilotDto;
    }
}
