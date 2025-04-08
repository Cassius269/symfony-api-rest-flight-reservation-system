<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Dto\CaptainResponseDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UpdateCaptainStateProcessor implements ProcessorInterface
{
    // Injection de dépendance(s)
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Vérifier la permission de l'utilisateur pour mettre à jour la ressource via un voter
        if (!$this->security->isGranted('CAPTAIN_EDIT', $data)) {
            throw new AccessDeniedException('Accès refusé car vous n\'est ni ADMIN ni auteur des données personnelles');
        }

        // Envoyer les modifications au serveur de l'API
        $data->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();

        // Préparer une réponse à renvoyer au client à l'aide de DTO
        $captainDto = new CaptainResponseDto;
        $captainDto->id = $data->getId();
        $captainDto->firstname = $data->getFirstname();
        $captainDto->lastname = $data->getLastname();
        $captainDto->email = $data->getEmail();
        $captainDto->createdAt = $data->getCreatedAt();
        $captainDto->updatedAt = $data->getUpdatedAt();

        return $captainDto;
    }
}
