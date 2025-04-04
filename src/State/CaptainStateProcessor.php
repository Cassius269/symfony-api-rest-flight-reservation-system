<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\CaptainResponseDto;
use App\Entity\Captain;
use App\Repository\CaptainRepository;
use App\Service\HashPasswordService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CaptainStateProcessor implements ProcessorInterface
{
    // Injection de dépendance
    public function __construct(
        private HashPasswordService $passwordHasher,
        private EntityManagerInterface $entityManager,
        private CaptainRepository $captainRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Rechercher s'il n'existe pas encore le commandant
        $isExistCaptain = $this->captainRepository->findOneByEmail($data->email);
        // dd($isExistCaptain);

        if ($isExistCaptain) {
            throw new UnprocessableEntityHttpException('un commandant de bord avec le mail existe déjà');
        };

        $captain = new Captain;
        $captain->setFirstname($data->firstname)
            ->setLastname($data->lastname)
            ->setEmail($data->email)
            ->setRoles(['ROLE_CAPTAIN'])
            ->setCreatedAt(new \DateTimeImmutable());

        // Hasher le mot de passer à l'aide du service personnalisé
        $this->passwordHasher->hashPassword(
            $data->password,
            $captain
        );

        // Persister et envoyer au serveur de l'API
        $this->entityManager->persist($captain);
        $this->entityManager->flush();

        // Préparer la réponse sous forme de DTO à retourner au client
        $captainDto = new CaptainResponseDto;
        $captainDto->id = $captain->getId();
        $captainDto->firstname = $captain->getFirstname();
        $captainDto->lastname = $captain->getLastname();
        $captainDto->email = $captain->getEmail();
        $captainDto->createdAt = $captain->getCreatedAt();

        // Retourner une réponse au client
        return $captainDto;
    }
}
