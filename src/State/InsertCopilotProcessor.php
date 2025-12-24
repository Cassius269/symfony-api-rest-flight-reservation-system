<?php

namespace App\State;

use App\Entity\Copilot;
use App\Dto\CopilotResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Service\HashPasswordService;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class InsertCopilotProcessor implements ProcessorInterface
{
    // Injection de dépendances dans le constructeur
    public function __construct(
        private HashPasswordService $hashPasswordService,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?object
    {
        // Handle the state
        // dd($data);


        // Créer un nouvel objet Copilot à envoyer au serveur
        $copilot = new Copilot;
        $copilot->setFirstname($data->firstname)
            ->setLastname($data->lastname)
            ->setEmail($data->email)
            ->setRoles(['ROLE_COPILOT'])
            ->setCreatedAt(new \DateTimeImmutable('now'));

        $this->hashPasswordService->hashPassword($data->password, $copilot);


        // Validation des données avant envoi en base de données 
        $errors = $this->validator->validate($copilot); // rechercher les erreurs ne remplissant pas les contraintes de validation des données de l'entité Copilote 

        // Si il n'y a pas d'erreur trouvée
        if ($errors == null) {
            // Envoyer la nouvelle ressource en base de données
            $this->entityManager->persist($copilot);
            $this->entityManager->flush();

            // Préparer la réponse à retourner au client
            $copilotResponseDto = new CopilotResponseDto;
            $copilotResponseDto->id = $copilot->getId();
            $copilotResponseDto->firstname = $copilot->getFirstname();
            $copilotResponseDto->lastname = $copilot->getLastname();
            $copilotResponseDto->email = $copilot->getEmail();

            return $copilotResponseDto; // renvoyer le DTO de réponse en cas de succès d'enregistrement de la nouvelle ressource Copilote
        }

        if (count($errors) > 0) { // s'il y a des erreurs trouvées 
            $errorMessages = [];

            // Générer une erreur 400 (= "bad request") avec les messages d'erreur détaillés 
            foreach ($errors as $error) {
                if ($error->getPropertyPath() != "createdAt") {
                    $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
                }
            }

            throw new BadRequestHttpException(json_encode($errorMessages));
        }
    }
}
