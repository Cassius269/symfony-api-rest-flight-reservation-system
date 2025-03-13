<?php

namespace App\State;

use App\Dto\CityRequestDto;
use App\Dto\FlightResponseDto;
use App\Dto\PassengerResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Dto\ReservationResponseDto;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;

class ReservationStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $reservation = $this->itemProvider->provide($operation, $uriVariables, $context);

        // Refuser l'accès si l'utilasateur n'est pas Admin ou propriétaire de la réservation
        if (!$this->security->isGranted('RESERVATION_VIEW', $reservation)) {
            throw new AccessDeniedException(json_encode([ // renvoyer un code d'erreur 403 car accès ressource interdit
                'message' => 'accès refusé'
            ]));
        };

        $reservationDto = new ReservationResponseDto;
        $reservationDto->id = $reservation->getId();
        $reservationDto->numberFlightSeat = $reservation->getNumberFlightSeat();
        $reservationDto->price = $reservation->getPrice();

        $passengerDto = new PassengerResponseDto;
        $passengerDto->firstname = $reservation->getPassenger()->getFirstname();
        $passengerDto->lastname = $reservation->getPassenger()->getLastname();
        $passengerDto->email = $reservation->getPassenger()->getEmail();

        $flightDto = new FlightResponseDto;

        $reservationDto->passenger = $passengerDto;
        $reservationDto->flight = $flightDto;

        $cityDeparture = new CityRequestDto;
        $cityDeparture->name = $reservation->getFlight()->getCityDeparture()->getName();
        $cityDeparture->country = $reservation->getFlight()->getCityDeparture()->getCountry()->getName();

        $cityArrival = new CityRequestDto;
        $cityArrival->name = $reservation->getFlight()->getCityArrival()->getName();
        $cityArrival->country = $reservation->getFlight()->getCityArrival()->getCountry()->getName();

        $flightDto->cityDeparture = $cityDeparture;
        $flightDto->cityArrival = $cityArrival;

        return $reservationDto;
    }
}
