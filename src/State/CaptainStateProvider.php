<?php

namespace App\State;

use App\Dto\CaptainResponseDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CaptainStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Rechercher le commandant de bord
        $data = $this->itemProvider->provide($operation, $uriVariables, $context);

        // Vérifier la permission de l'utilisateur pour mettre à jour la ressource via un voter
        if (!$this->security->isGranted('CAPTAIN_VIEW', $data)) {
            throw new AccessDeniedException('Accès refusé car vous n\'est ni ADMIN ni auteur des données personnelles');
        }

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
