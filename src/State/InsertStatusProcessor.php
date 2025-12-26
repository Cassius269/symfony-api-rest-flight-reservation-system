<?php

namespace App\State;

use App\Entity\Status;
use App\Dto\StatusResponseDto;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InsertStatusProcessor implements ProcessorInterface
{
    // Injection de dépendance(s)
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?StatusResponseDto
    {
        // dd($data);
        if (!$data->name) {
            throw new UnprocessableEntityHttpException('Aucune donnée formatable trouvée');
        }

        $status = new Status;
        $status->setName($data->name)
            ->setCreatedAt(new \DateTimeImmutable());

        // Vérifier les contraintes de validation des données
        $errors = $this->validator->validate($status);

        if (count($errors ?? []) > 0) {
            throw new ValidationException((string) $errors);
        }

        // Persister et envoyer en base de données la nouvelle ressource de type Status
        $this->entityManager->persist($status);
        $this->entityManager->flush();

        // Préparer la réponse à envoyer au client
        $statusDto = new StatusResponseDto;
        $statusDto->name = $status->getName();
        $statusDto->createdAt = $status->getCreatedAt();

        return $statusDto;
    }
}
