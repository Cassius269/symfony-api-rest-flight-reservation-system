<?php

namespace App\State;

use App\Entity\Reservation;
use App\Service\EmailService;
use ApiPlatform\Metadata\Operation;
use App\Repository\FlightRepository;
use App\Repository\PassengerRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Dto\FlightResponseDto;
use App\Dto\PassengerResponseDto;
use App\Dto\ReservationResponseDto;
use App\Repository\StatusRepository;
use App\Service\PNRGenerationService;
use App\Service\SeatReservationService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ReservationStateProcessor implements ProcessorInterface
{
    // Injection des dépendances
    public function __construct(
        private PassengerRepository $passengerRepository,
        private EntityManagerInterface $entityManager,
        private FlightRepository $flightRepository,
        private StatusRepository $statusRepository,
        // private AirplaneModelRepository $airplaneModelRepository,
        private ValidatorInterface $validator,
        private SeatReservationService $seatReservationService,
        private EmailService $emailService,
        private PNRGenerationService $pnrGenerationService // injection de la dépendance de génération de PNR
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // dd($data);

        // Rechercher si le passager existe déjà dans le serveur de l'API
        $passenger = $data->passenger;
        // dd($passenger);

        $isExistPassenger = $this->passengerRepository->findOneBy(
            [
                // "firstname" => $passenger->firstname,
                // "lastname" => $passenger->lastname,
                "email" => $passenger->email
            ]
        );

        if (!$isExistPassenger) {
            throw new NotFoundHttpException('Le passager avec le mail ' . $data->passenger->email . ' n\'existe pas');
        }
        // dd($isExistPassenger);
        // Ajouter un passager à un vol

        // recherche de vol
        $isExistFlight = $this->flightRepository->find($data->flight->id);

        // dd($isExistFlight);

        // Si le vol n'existe pas, renvoyer une erreur 404 au client
        if (!$isExistFlight) {
            throw new NotFoundHttpException('Le vol n\‘existe pas encore en base de données');
        }

        // Compter le nombre de passagers d'un vols
        $actualReservations = $isExistFlight->getReservations();

        $passengersCount = null;
        foreach ($actualReservations as $actualReservation) {
            $passengersCount++;
        }
        // dd($passengersCount);

        if ($isExistFlight->getAirPlane()->getAirplaneModel()->getcapacity() <= $passengersCount) {
            throw new UnprocessableEntityHttpException(json_encode([ // renvoyer un code d'erreur 422 car problème logique des données
                'message' => 'La capacité maximale de l\'avion choisi pour le vol est atteinte'
            ]));
        }

        // Chercher le status par défaut "En cours"
        $status = $this->statusRepository->findOneBy(['name' => 'En cours']);
        // dd($status);

        $reservation = new Reservation();
        $reservation->setCreatedAt(new \DateTimeImmutable())
            ->setPrice(800) // prix par défaut 800euros
            ->setFlight($isExistFlight)
            ->setPassenger($isExistPassenger)
            ->setStatus($status)
            ->setPassengerNameRecord($this->pnrGenerationService->attributePNRNumber());

        $this->seatReservationService->attributeASeat($isExistFlight, $reservation); // attribuer un siège au passager de la réservation

        // Vérifier les contraintes de validation d'envoyer la ressource au serveur
        $errors = $this->validator->validate($reservation);

        if (count($errors ?? []) > 0) {
            throw new ValidationException((string) $errors);
        }

        // Enregistrer et envoyer en base de données le nouveau passager (si création) et la réservation
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        // dd($passenger);
        // Envoyer un mail de confirmation au passage
        $this->emailService->confirmReservation($reservation); // récuperer les informations depuis le nouvel objet de réservation nouvellement créé 

        // Préparer la réponse à retourner au client
        $reservationDto = new ReservationResponseDto;
        $reservationDto->id = $reservation->getId();
        $reservationDto->numberFlightSeat = $reservation->getNumberFlightSeat();
        $reservationDto->price = $reservation->getPrice();
        $reservationDto->passengerNameRecord = $reservation->getPassengerNameRecord();
        $reservationDto->createdAt = $reservation->getCreatedAt();
        $reservationDto->updatedAt = $reservation->getUpdatedAt();

        $passengerDto = new PassengerResponseDto;
        $passengerDto->firstname = $reservation->getPassenger()->getFirstname();
        $passengerDto->lastname = $reservation->getPassenger()->getLastname();
        $passengerDto->email = $reservation->getPassenger()->getEmail();

        $reservationDto->passenger = $passengerDto;

        $flightDto = new FlightResponseDto;
        $flightDto->company = $reservation->getFlight()->getCompany()->getName();

        $reservationDto->flight = $flightDto;

        return $reservationDto; // retouner les valeurs entrée si pas de traitement particulier en sortie
    }
}
