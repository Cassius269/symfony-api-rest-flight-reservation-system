<?php

namespace App\State;

use App\Dto\FlightResponseDto;
use App\Dto\PassengerResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Dto\ReservationResponseDto;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Dto\AirportResponseDto;
use App\Dto\CityResponseDto;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReservationStateProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupérer la réservation
        $reservation = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (!$reservation) { // s'il n'ya pas de réservation trouvée envoyer un message d'erreur avec le code 404
            throw new NotFoundHttpException('Aucune réservation trouvée avec l\'id fourni');
        }

        // Refuser l'accès si l'utilasateur n'est pas Admin ou propriétaire de la réservation
        if (!$this->security->isGranted('RESERVATION_VIEW', $reservation)) {
            // renvoyer un code d'erreur 403 car accès ressource interdit
            throw new AccessDeniedException('accès refusé');
        };

        // Préparer un DTO à retourner au client
        $reservationDto = new ReservationResponseDto;
        $reservationDto->id = $reservation->getId();
        $reservationDto->numberFlightSeat = $reservation->getNumberFlightSeat();
        $reservationDto->price = $reservation->getPrice();
        $reservationDto->status = $reservation->getStatus()->getName();
        $reservationDto->passengerNameRecord = $reservation->getPassengerNameRecord();
        $reservationDto->createdAt = $reservation->getCreatedAt();
        $reservationDto->updatedAt = $reservation->getUpdatedAt();

        $passengerDto = new PassengerResponseDto;
        $passengerDto->firstname = $reservation->getPassenger()->getFirstname();
        $passengerDto->lastname = $reservation->getPassenger()->getLastname();
        $passengerDto->email = $reservation->getPassenger()->getEmail();

        $flightDto = new FlightResponseDto;

        $reservationDto->passenger = $passengerDto;
        $reservationDto->flight = $flightDto;

        $cityDeparture = new CityResponseDto;
        $cityDeparture->name = $reservation->getFlight()->getAirportDeparture()->getCity()->getName();
        $cityDeparture->countryName = $reservation->getFlight()->getAirportDeparture()->getCity()->getCountry()->getName();


        $cityArrival = new CityResponseDto;
        $cityArrival->name = $reservation->getFlight()->getAirportArrival()->getCity()->getName();
        $cityArrival->countryName = $reservation->getFlight()->getAirportArrival()->getCity()->getCountry()->getName();

        $airportDeparture = new AirportResponseDto();
        $airportArrival = new AirportResponseDto();
        $airportDeparture->city = $cityDeparture;
        $airportArrival->city = $cityArrival;

        $flightDto->airportDeparture = $airportDeparture;
        $flightDto->airportArrival = $airportArrival;

        if ($reservation->getFlight()->isDirect()) {
            // dd('vol direct');
            $flightDto->dateDeparture = $reservation->getFlight()->getDateDeparture();
            $flightDto->dateArrival = $reservation->getFlight()->getDateArrival();
        }

        return $reservationDto;
    }
}
