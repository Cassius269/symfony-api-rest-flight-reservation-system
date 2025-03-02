<?php

namespace App\State;

use App\Entity\AirplaneModel;
use ApiPlatform\Metadata\Operation;
use App\Dto\AirplaneModelResponseDto;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AirplaneModelStateProcessor implements ProcessorInterface
{
    // Injection des dépendances
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // dd($data);

        $airplane = new AirplaneModel;
        $airplane->setModel($data->model)
            ->setCapacity($data->capacity)
            ->setCreatedAt(new \DateTimeImmutable());


        // Validation des données avant envoi en base de données
        $errors = $this->validator->validate($airplane); // rechercher les erreurs ne remplissant pas les contraintes de validation des données de l'entité Article

        if (count($errors) > 0) { // s'il y a des erreurs trouvées
            $errorMessages = [];

            // Générer une erreur 400 (= "bad request") avec les messages d'erreur détaillés
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
            }
            throw new BadRequestHttpException(json_encode($errorMessages));
        }

        // Enregistrer la requête et envoyer le nouvel objet avion au serveur
        $this->entityManager->persist($airplane);
        $this->entityManager->flush();


        // Retourner une réponse à l'interface API
        $airplaneDto = new AirplaneModelResponseDto;
        $airplaneDto->model = $airplane->getModel();
        $airplaneDto->capacity = $airplane->getCapacity();

        return $airplaneDto; // renvoyer la ressource nouvellement créé au client (navigateur, utilisateur par exemple) en passant par le DTO
    }
}
