<?php

namespace App\State;

use App\Dto\CityResponseDto;
use App\Dto\FlightResponseDto;
use App\Dto\AirportResponseDto;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CustomFlightsGetCollection implements ProviderInterface
{
    // Injection de dépendance(s)
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $providerInterface
    ) {}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?object
    {
        // Récupérer les ressources dans le serveur
        $data = $this->providerInterface->provide($operation, $uriVariables, $context);

        // dd($context['filters']);
        // dd($data);
        if ($data instanceof PaginatorInterface) {
            // Préparer la réponse à envoyer au client
            $results = [];

            foreach ($data as $flight) {
                $flightResponseDto = new FlightResponseDto;
                $flightResponseDto->id = $flight->getId();
                $flightResponseDto->airplaneModel = $flight->getAirplane()->getAirplaneModel()->getModel();
                $flightResponseDto->company = $flight->getCompany()->getName();
                $flightResponseDto->dateDeparture = $flight->getDateDeparture();
                $flightResponseDto->dateArrival = $flight->getdateArrival();
                $flightResponseDto->isDirect = $flight->isDirect();
                $flightResponseDto->createdAt = $flight->getCreatedAt();
                $flightResponseDto->updatedAt = $flight->getUpdatedAt();

                $airportDepartureDto = new AirportResponseDto;
                $cityDeparture = new CityResponseDto;
                $cityDeparture->name = $flight->getAirportDeparture()->getCity()->getName();
                $cityDeparture->countryName = $flight->getAirportDeparture()->getCity()->getCountry()->getName();

                $airportDepartureDto->name = $flight->getAirportDeparture()->getName();
                $airportDepartureDto->city = $cityDeparture;


                $airportArrivalDto = new AirportResponseDto;
                $cityArrival = new CityResponseDto;
                $cityArrival->name = $flight->getAirportArrival()->getCity()->getName();
                $cityArrival->countryName = $flight->getAirportArrival()->getCity()->getCountry()->getName();

                $airportArrivalDto->name = $flight->getAirportArrival()->getName();
                $airportArrivalDto->city = $cityArrival;

                $flightResponseDto->airportDeparture = $airportDepartureDto;
                $flightResponseDto->airportArrival = $airportArrivalDto;

                $results[] = $flightResponseDto;
            }


            // Retourner des résultats paginables avec les options de page courante par exemple
            return new TraversablePaginator(
                new \ArrayIterator(($results)),
                $data->getCurrentPage(),
                $data->getItemsPerPage(),
                $data->getTotalItems()
            );
        }
    }
}
