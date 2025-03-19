<?php

namespace App\State;

use App\Dto\CityRequestDto;
use App\Dto\FlightResponseDto;
use App\Dto\PassengerResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Dto\ReservationResponseDto;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomReservationGetCollectionStateProvider implements ProviderInterface
{
    // Injection de dépendances
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $collectionProvider,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupérer la liste des réservations classiques ou bien par filtre
        $data = $this->collectionProvider->provide($operation, $uriVariables, $context);

        // Restituer un résultat user-friendly
        $results = [];

        foreach ($data as $reservation) {
            // Création d'un Dto de réservation
            $reservationDto = new ReservationResponseDto;
            $reservationDto->id = $reservation->getId();
            $reservationDto->numberFlightSeat = $reservation->getNumberFlightSeat();
            $reservationDto->price = $reservation->getPrice();
            $reservationDto->passengerNameRecord = $reservation->getPassengerNameRecord();

            // création de Dto imbriqué du passager
            $passengerDto = new PassengerResponseDto;
            $passengerDto->firstname = $reservation->getPassenger()->getFirstname();
            $passengerDto->lastname = $reservation->getPassenger()->getLastname();
            $passengerDto->email = $reservation->getPassenger()->getEmail();

            // création de Dto imbriqué du vol
            $flightDto = new FlightResponseDto;
            $flightDto->dateDeparture = $reservation->getFlight()->getDateDeparture();
            $flightDto->dateArrival = $reservation->getFlight()->getDateArrival();

            // Création d'un Dto imbriqué de la ville de départ
            $cityDepartureDto = new CityRequestDto;
            $cityDepartureDto->name = $reservation->getFlight()->getCityDeparture()->getName();
            $cityDepartureDto->country = $reservation->getFlight()->getCityDeparture()->getCountry()->getName();

            // Création d'un Dto imbriqué de la ville d'arrivée
            $cityArrivalDto = new CityRequestDto;
            $cityArrivalDto->name = $reservation->getFlight()->getCityArrival()->getName();
            $cityArrivalDto->country = $reservation->getFlight()->getCityArrival()->getCountry()->getName();

            // Compléter les informations du DTO de la réservation
            $reservationDto->passenger = $passengerDto;
            $reservationDto->flight =  $flightDto;
            $flightDto->cityDeparture = $cityDepartureDto;
            $flightDto->cityArrival = $cityArrivalDto;

            // Ajouter chaque réservation trouvée à la liste des résultats sous forme de tableau
            $results[] = $reservationDto;
        };

        return $results;
    }
}
