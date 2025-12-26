<?php

namespace App\State;

use App\Dto\CityRequestDto;
use App\Dto\FlightResponseDto;
use App\Dto\PassengerResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Dto\ReservationResponseDto;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\ReservationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use ApiPlatform\Validator\Exception\ValidationException;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use ApiPlatform\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateReservationProcessor implements ProcessorInterface
{
    // Injection de dépendance
    public function __construct(
        private ReservationRepository $reservationRepository,
        private StatusRepository $statusRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Vérifier l’existence de la ressource dans le serveur 
        $isExistReservation = $this->reservationRepository->findOneById($uriVariables['id']);


        // Mettre à jour la réservation
        $isExistStatus = $this->statusRepository->findOneBy(['name' => $data->status]);

        if (!$isExistStatus) {
            throw new NotFoundHttpException('Le status n\'a pas a été trouvé dans le serveur');
        }

        $isExistReservation->setStatus($isExistStatus);
        $isExistReservation->setUpdatedAt(new \DateTime());


        // Préparer la réponse à envoyer au client

        // Vérifier les contraintes de validation d'envoyer la ressource au serveur
        $errors = $this->validator->validate($isExistReservation);

        if (count($errors ?? []) > 0) {
            throw new ValidationException((string) $errors);
        }

        // Enregistrer et envoyer en base de données le nouveau passager (si création) et la réservation
        $this->entityManager->persist($isExistReservation);
        $this->entityManager->flush();

        // dd($passenger);

        // Préparer la réponse à retourner au client
        $reservationDto = new ReservationResponseDto;
        $reservationDto->id = $isExistReservation->getId();
        $reservationDto->numberFlightSeat = $isExistReservation->getNumberFlightSeat();
        $reservationDto->price = $isExistReservation->getPrice();
        $reservationDto->passengerNameRecord = $isExistReservation->getPassengerNameRecord();
        $reservationDto->status = $isExistReservation->getStatus()->getName();
        $reservationDto->createdAt = $isExistReservation->getCreatedAt();
        $reservationDto->updatedAt = $isExistReservation->getUpdatedAt();

        $passengerDto = new PassengerResponseDto;
        $passengerDto->firstname = $isExistReservation->getPassenger()->getFirstname();
        $passengerDto->lastname = $isExistReservation->getPassenger()->getLastname();
        $passengerDto->email = $isExistReservation->getPassenger()->getEmail();

        $reservationDto->passenger = $passengerDto;

        $flightDto = new FlightResponseDto;
        $flightDto->company = $isExistReservation->getFlight()->getCompany()->getName();

        $reservationDto->flight = $flightDto;

        return $reservationDto; // retouner les valeurs entrée si pas de traitement particulier en sortie
    }
}
