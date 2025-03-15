<?php

namespace App\State;

use App\Dto\PassengerResponseDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PassengerStateProvider implements ProviderInterface
{
    // Injection de dépndance
    public function __construct(
        private Security $security,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider,
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupérer le passager
        $passenger = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (!$passenger) {
            throw new NotFoundHttpException('Aucun passager trouvé avec l\'id fourni');
        }

        // Vérifier la permission d'accès à la ressource
        if (!$this->security->isGranted('PASSENGER_VIEW', $passenger)) {
            throw new AccessDeniedException(
                json_encode([
                    'message' => 'désolé vous êtes ni Admin ou auteur des informations personnelles'
                ])
            );
        };

        // Créer un DTO
        $passengerDto = new PassengerResponseDto;
        $passengerDto->id = $passenger->getId();
        $passengerDto->firstname = $passenger->getFirstname();
        $passengerDto->lastname = $passenger->getLastname();
        $passengerDto->email = $passenger->getEmail();

        return $passengerDto;
    }
}
