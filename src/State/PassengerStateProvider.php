<?php

namespace App\State;

use App\Dto\PassengerResponseDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PassengerStateProvider implements ProviderInterface
{
    // Injection de dépndance
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupérer le passager
        $passenger = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (!$passenger) {
            throw new NotFoundHttpException('Aucun passager trouvé avec l\'id fourni');
        }

        // Créer un DTO
        $passengerDto = new PassengerResponseDto;
        $passengerDto->id = $passenger->getId();
        $passengerDto->firstname = $passenger->getFirstname();
        $passengerDto->lastname = $passenger->getLastname();
        $passengerDto->email = $passenger->getEmail();

        return $passengerDto;
    }
}
