<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\AirportResponseDto;
use App\Dto\CityResponseDto;
use App\Dto\FlightResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class FlightStateProvider implements ProviderInterface
{
    // Injection de dépendance
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?FlightResponseDto
    {
        $data = $this->itemProvider->provide($operation, $uriVariables, $context);

        // dd($data->isDirect());

        // Préparer la réponse à retourner au client
        $flightResponseDto = new FlightResponseDto;
        $flightResponseDto->id = $data->getId();
        $flightResponseDto->airplaneModel = $data->getAirplane()->getAirplaneModel()->getModel();
        $flightResponseDto->company = $data->getCompany()->getName();
        $flightResponseDto->dateDeparture = $data->getDateDeparture();
        $flightResponseDto->dateArrival = $data->getdateArrival();
        $flightResponseDto->isCanceled = $data->isCanceled();
        $flightResponseDto->isLate = $data->isLate();
        $flightResponseDto->isDirect = $data->isDirect();
        $flightResponseDto->createdAt = $data->getCreatedAt();
        $flightResponseDto->updatedAt = $data->getUpdatedAt();

        $airportDepartureDto = new AirportResponseDto;
        $cityDeparture = new CityResponseDto;
        $cityDeparture->name = $data->getAirportDeparture()->getCity()->getName();
        $cityDeparture->countryName = $data->getAirportDeparture()->getCity()->getCountry()->getName();

        $airportDepartureDto->name = $data->getAirportDeparture()->getName();
        $airportDepartureDto->city = $cityDeparture;


        $airportArrivalDto = new AirportResponseDto;
        $cityArrival = new CityResponseDto;
        $cityArrival->name = $data->getAirportArrival()->getCity()->getName();
        $cityArrival->countryName = $data->getAirportArrival()->getCity()->getCountry()->getName();

        $airportArrivalDto->name = $data->getAirportArrival()->getName();
        $airportArrivalDto->city = $cityArrival;

        $flightResponseDto->airportDeparture = $airportDepartureDto;
        $flightResponseDto->airportArrival = $airportArrivalDto;

        return $flightResponseDto;
    }
}
