<?php

namespace App\State;

use App\Dto\AirplaneResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Dto\AirplaneModelResponseDto;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\AirplaneModelRepository;
use ApiPlatform\Validator\ValidatorInterface;
use ApiPlatform\Validator\Exception\ValidationException;

class UpdateAirplaneModelProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AirplaneModelRepository $airplaneRepository,
        private ValidatorInterface $validator
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // 1ère façon : mettre à jour une ressource de type avion en utilisant un DTO
        // Rechercher la ressource à mettre à jour sans exposer l'entité
        $airplane = $this->airplaneRepository->findOneById($uriVariables["id"]);

        $airplane->setModel($data->model)
            ->setCapacity($data->capacity)
            ->setUpdatedAt(new \DateTime());


        // Valider les données
        $errors = $this->validator->validate($airplane);

        if (count($errors ?? []) > 0) {
            throw new ValidationException((string) $errors);
        }

        // Envoyer la ressource au serveur
        $this->entityManager->flush();

        // Préparer la réponse sous forme de DTO
        $airplaneResponseDto = new AirplaneModelResponseDto;
        $airplaneResponseDto->model = $airplane->getModel();
        $airplaneResponseDto->capacity = $airplane->getCapacity();

        return $airplaneResponseDto;

        //2ème façon de mettre à jour la donnée, sans utilisation de DTO
        // $data->setUpdatedAt(new \DateTime()); // mettre à jour la date de mise à la date du jour

        // $this->entityManager->flush(); // envoyer la ressource de type avion à jour au serveur
        // return $data; // renvoyer la donnée reçu en tant que résultat
    }
}
