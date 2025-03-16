<?php

namespace App\State;

use App\Dto\PassengerRequestDto;
use App\Dto\PassengerResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Repository\PassengerRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UpdatePassengerProcessor implements ProcessorInterface
{
    //Injection de dépendances 
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PassengerRepository $passengerRepository
    ) {}


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Récuperer l'objet Passager présent en BDD avant mise à jour
        $passenger = $this->passengerRepository->findOneById($uriVariables['id']);

        // Mettre à jour le passager
        if ($data->firstname) {
            $passenger->setFirstname($data->firstname);
        }

        if ($data->lastname) {
            $passenger->setLastname($data->lastname);
        }

        if ($data->email) {
            $passenger->setEmail($data->email);
        }

        if ($data->password) {
            $passenger->setPassword($data->password);
        }
        if (!$data->firstname && !$data->lastname  && !$data->email && !$data->password) {
            throw new UnprocessableEntityHttpException(json_encode([
                'message' => 'La requête est vide ou ne possède pas les propriétés attendues'
            ]));
        }

        $passenger->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($passenger); // cette instruction est optionnelle
        $this->entityManager->flush();

        // Préparer la réponse 
        $passengerResponseDto = new PassengerResponseDto;
        $passengerResponseDto->id = $passenger->getId();
        $passengerResponseDto->firstname = $passenger->getFirstname();
        $passengerResponseDto->lastname = $passenger->getLastname();
        $passengerResponseDto->email = $passenger->getEmail();

        return $passengerResponseDto;
    }
}
