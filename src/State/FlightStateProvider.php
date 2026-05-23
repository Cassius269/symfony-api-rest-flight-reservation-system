<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\AirportResponseDto;
use App\Dto\CaptainResponseDto;
use App\Dto\CityResponseDto;
use App\Dto\CompanyResponseDto;
use App\Dto\FlightResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FlightStateProvider implements ProviderInterface
{
    // Injection de dépendance
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?FlightResponseDto
    {
        // Récupérer le vol
        $data = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (!$data) { // s'il n'ya pas de réservation trouvée envoyer un message d'erreur avec le code 404
            throw new NotFoundHttpException('Aucun vol trouvé avec l\'id fourni');
        }

        // Préparer la réponse à retourner au client
        $flightResponseDto = new FlightResponseDto;
        $flightResponseDto->id = $data->getId();
        $flightResponseDto->airplaneModel = $data->getAirplane()->getAirplaneModel()->getModel();
        $flightResponseDto->dateDeparture = $data->getDateDeparture();
        $flightResponseDto->dateArrival = $data->getdateArrival();
        $flightResponseDto->isCanceled = $data->isCanceled();
        $flightResponseDto->price = $data->getPrice();
        $flightResponseDto->isLate = $data->isLate();
        $flightResponseDto->isDirect = $data->isDirect();
        $flightResponseDto->createdAt = $data->getCreatedAt();
        $flightResponseDto->updatedAt = $data->getUpdatedAt();

        $companyResponseDto = new CompanyResponseDto;
        $companyResponseDto->id = $data->getCompany()->getId();
        $companyResponseDto->name = $data->getCompany()->getName();
        $flightResponseDto->company = $companyResponseDto;

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

        $captainResponseDto = new CaptainResponseDto;
        $captainResponseDto->firstname = $data->getCaptain()->getFirstname();
        $captainResponseDto->lastname = $data->getCaptain()->getLastname();
        $captainResponseDto->email = $data->getCaptain()->getEmail();

        $flightResponseDto->captain = $captainResponseDto;

        return $flightResponseDto;
    }
}
