<?php

namespace App\State;

use App\Dto\CityRequestDto;
use App\Dto\FlightResponseDto;
use App\Dto\PassengerResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Dto\ReservationResponseDto;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\ReservationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;

class UpdateReservationProcessor implements ProcessorInterface
{
    // Injection de dépendance
    public function __construct(
        private ReservationRepository $reservationRepository,
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Vérifier l’existence de la ressource dans le serveur 
        $isExistReservation = $this->reservationRepository->findOneById($uriVariables['id']);

        // Vérifier la permission d'accès à la ressource via un voter
        if (!$this->security->isGranted('RESERVATION_EDIT', $isExistReservation)) {
            throw new AccessDeniedException(
                json_encode([
                    'message' => 'désolé vous êtes ni Admin ni propriétaire de la réservation'
                ])
            );
        };
        // Mettre à jour la réservation
        if ($data->price) {
            $isExistReservation->setPrice($data->price);
        }

        if ($data->passenger->firstname) {
            $isExistReservation->getPassenger()->setFirstname($data->passenger->firstname);
        }

        if ($data->passenger->lastname) {
            $isExistReservation->getPassenger()->setLastname($data->passenger->lastname);
        }

        if ($data->passenger->email) {
            $isExistReservation->getPassenger()->setEmail($data->passenger->email);
        }

        $isExistReservation->setUpdatedAt(new \DateTime());

        // Envoyer la ressource à jour au serveur
        $this->entityManager->persist($isExistReservation);
        $this->entityManager->flush();

        // Préparer la réponse à envoyer au client
        $reservationResponseDto = new ReservationResponseDto;
        $reservationResponseDto->id = $isExistReservation->getId();
        $reservationResponseDto->numberFlightSeat = $isExistReservation->getNumberFlightSeat();
        $reservationResponseDto->price = $isExistReservation->getPrice();

        $passengerResponseDto = new PassengerResponseDto;
        $passengerResponseDto->firstname = $isExistReservation->getPassenger()->getFirstname();
        $passengerResponseDto->lastname = $isExistReservation->getPassenger()->getLastname();
        $passengerResponseDto->email = $isExistReservation->getPassenger()->getEmail();


        $flightResponseDto = new FlightResponseDto;
        $flightResponseDto->dateDeparture = $isExistReservation->getFlight()->getDateDeparture();
        $flightResponseDto->dateArrival = $isExistReservation->getFlight()->getDateArrival();

        $cityDepartureDto = new CityRequestDto;
        $cityDepartureDto->name = $isExistReservation->getFlight()->getcityDeparture()->getName();
        $cityDepartureDto->country = $isExistReservation->getFlight()->getcityDeparture()->getCountry()->getName();

        $cityArrivalDto = new CityRequestDto;
        $cityArrivalDto->name = $isExistReservation->getFlight()->getcityArrival()->getName();
        $cityArrivalDto->country = $isExistReservation->getFlight()->getCityArrival()->getCountry()->getName();


        $flightResponseDto->cityDeparture = $cityDepartureDto;
        $flightResponseDto->cityArrival = $cityArrivalDto;

        $reservationResponseDto->passenger = $passengerResponseDto;
        $reservationResponseDto->flight = $flightResponseDto;

        // Retourner la réponse au client avec un DTO contenant les informations de la réservation du passager
        return $reservationResponseDto;
    }
}
