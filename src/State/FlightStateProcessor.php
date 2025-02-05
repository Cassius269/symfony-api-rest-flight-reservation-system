<?php

namespace App\State;

use App\Entity\Flight;
use App\Dto\CityRequestDto;
use App\Dto\FlightResponseDto;
use App\Repository\CityRepository;
use ApiPlatform\Metadata\Operation;
use App\Repository\CountryRepository;
use App\Repository\AirplaneRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\FlightRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FlightStateProcessor implements ProcessorInterface
{
    public function __construct(
        private CityRepository $cityRepository,
        private CountryRepository $countryRepository,
        private AirplaneRepository $airplaneRepository,
        private FlightRepository $flightRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Rechercher l'objet pays d'arrivée
        $countryArrival = $this->countryRepository->findCountryByName($data->getCityArrival()->country);

        // Rechercher l'objet pays de départ
        $countryDeparture = $this->countryRepository->findCountryByName($data->getCityDeparture()->country);

        // Rechercher les villes de départ et de destination
        $isExistCityDeparture = $this->cityRepository->findOneBy(
            [
                'name' => $data->getCityDeparture()->name, // le nom de la ville de départ
                'country' => $countryDeparture // le pays de départ
            ]
        );

        dd($isExistCityDeparture);

        $isExistCityArrival = $this->cityRepository->findOneBy(
            [
                'name' => $data->getCityArrival()->name,
                'country' => $countryArrival
            ]
        );

        // Rechercher l'avion à assigner
        $isExistAirplane = $this->airplaneRepository->findOneById($data->airplaneId);

        // dd($isExistAirplane);

        // vérifier si les villes de départ et d'arrivée ainsi que l'avion pour le vol existent dans le serveur
        if (!$isExistCityArrival || !$isExistCityDeparture || !$isExistAirplane) {

            if (!$isExistCityArrival) {
                throw new BadRequestHttpException(json_encode([
                    'message' => 'La ville d\'arrivée choisie n\'est pas desservie pour le moment'
                ]));
            }

            if (!$isExistCityDeparture) {
                throw new BadRequestHttpException(json_encode([
                    'message' => 'La ville de départ choisie n\'est pas desservie pour le moment'
                ]));
            }

            if (!$isExistAirplane) {
                throw new BadRequestHttpException(json_encode([
                    'message' => 'L\'avion choisi n\'est pas trouvé'
                ]));
            }
        }


        // Vérifier si les villes de destination et d'arrivée sont bien differentes
        if ($isExistCityDeparture == $isExistCityArrival) {
            throw new BadRequestHttpException(json_encode([
                'message' => 'Les villes de départ et de destination doivent être differentes'
            ]));
        }

        // Vérifier si la date d'arrivée est superieure à la date de départ
        if ($data->dateDeparture >= $data->dateArrival) {
            throw new BadRequestHttpException(json_encode([
                'message' => 'La date d\'arrivée doit être supérieure à la date de départ'
            ]));
        }

        // Rechercher s'il n'y pas de vol similaire
        $isExistFlight = $this->flightRepository->findOneBy([
            'dateDeparture' => $data->dateDeparture,
            'dateArrival' => $data->dateArrival,
            'cityDeparture' => $isExistCityDeparture,
            'cityArrival' => $isExistCityArrival,
            'airplane' => $isExistAirplane
        ]);

        // dd($isExistFlight);

        if ($isExistFlight) {
            throw new BadRequestHttpException(json_encode([
                'message' => 'Un vol similaire portant les mêmes informations de vol existent'
            ]));
        }

        // Ecrire la requête et envoyer au serveur le nouveau vol d'avion
        $flight = new Flight;
        $flight->setCreatedAt(new \DateTimeImmutable())
            ->setCityDeparture($isExistCityDeparture)
            ->setCityArrival($isExistCityArrival)
            ->setAirplane($isExistAirplane)
            ->setDateDeparture($data->dateDeparture)
            ->setDateArrival($data->dateArrival);

        // dd($flight);
        $this->entityManager->persist($flight);
        $this->entityManager->flush();

        // Retourner une réponse au client (exemple navigateur ou Postman)
        $CityDepartureDto = new CityRequestDto; // Exceptionnellement j'ai utilisé ce DTO car la structure de données n'est pas pareille qu'avec le DTO CityResponseDto
        $CityDepartureDto->name = $flight->getCityArrival()->getName();
        $CityDepartureDto->country = $flight->getCityDeparture()->getCountry()->getName();

        $CityArrivalDto = new CityRequestDto; // Exceptionnellement j'ai utilisé ce DTO car la structure de données n'est pas pareille qu'avec le DTO CityResponseDto
        $CityArrivalDto->name = $flight->getCityArrival()->getName();
        $CityArrivalDto->country = $flight->getCityArrival()->getCountry()->getName();


        $flitghtDto = new FlightResponseDto;
        $flitghtDto->dateDeparture = $flight->getDateDeparture();
        $flitghtDto->dateArrival = $flight->getDateArrival();
        $flitghtDto->cityDeparture = $CityDepartureDto;
        $flitghtDto->cityArrival = $CityArrivalDto;

        return $flitghtDto; // retourner le DTO contenant les informations du vol
    }
}
