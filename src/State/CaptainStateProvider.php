<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CaptainResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CaptainStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Rechercher le commandant de bord
        $data = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (!$data) {
            throw  new NotFoundHttpException('aucun commandant de bord trouvé avec l\'id fourni');
        };

        // Préparer la réponse avec un DTO
        $captainDto = new CaptainResponseDto;
        $captainDto->id = $data->getId();
        $captainDto->firstname = $data->getFirstname();
        $captainDto->lastname = $data->getLastname();
        $captainDto->email = $data->getEmail();
        $captainDto->createdAt = $data->getCreatedAt();
        $captainDto->updatedAt = $data->getUpdatedAt();

        // Retourner une réponse DTO au client
        return $captainDto;
    }
}
