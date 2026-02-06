<?php

namespace App\State;

use App\Dto\CityRequestDto;
use App\Dto\FlightResponseDto;
use App\Dto\PassengerResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Dto\ReservationResponseDto;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Dto\AirportResponseDto;
use App\Dto\CityResponseDto;
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
            $reservationDto->status = $reservation->getStatus()->getName();

            // Création d'un Dto imbriqué de passager
            $passengerDto = new PassengerResponseDto;
            $passengerDto->firstname = $reservation->getPassenger()->getFirstname();
            $passengerDto->lastname = $reservation->getPassenger()->getLastname();
            $passengerDto->email = $reservation->getPassenger()->getEmail();

            $flightDto = new FlightResponseDto;

            $reservationDto->passenger = $passengerDto;
            $reservationDto->flight = $flightDto;

            // Création d'un Dto imbriqué de la ville de départ pour chaque érservation
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
                $flightDto->dateDeparture = $reservation->getFlight()->getDateDeparture();
                $flightDto->dateArrival = $reservation->getFlight()->getDateArrival();
            }
            // Ajouter chaque réservation trouvée à la liste des résultats sous forme de tableau
            $results[] = $reservationDto;
        };

        return $results;
    }
}
