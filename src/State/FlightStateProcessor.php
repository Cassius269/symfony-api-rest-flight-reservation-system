<?php

namespace App\State;

use DateTime;
use App\Entity\Flight;
use App\Dto\CityRequestDto;
use App\Dto\FlightResponseDto;
use App\Repository\CityRepository;
use ApiPlatform\Metadata\Operation;
use App\Repository\FlightRepository;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\AirplaneModelRepository;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class FlightStateProcessor implements ProcessorInterface
{
    public function __construct(
        private CityRepository $cityRepository,
        private CountryRepository $countryRepository,
        private AirplaneModelRepository $airplaneModelRepository,
        private FlightRepository $flightRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Rechercher les villes de départ et de destination à l'aide du nom de la ville et du pays
        $isExistCityDeparture = $this->cityRepository->findDestinationByCityAndCountry($data->getCityDeparture()->name, $data->getCityDeparture()->country);

        $isExistCityArrival = $this->cityRepository->findDestinationByCityAndCountry($data->getCityArrival()->name, $data->getCityArrival()->country);

        // dd($isExistCityArrival);

        // Rechercher l'avion à assigner
        $isExistAirplane = $this->airplaneModelRepository->findOneById($data->airplaneId);

        // Vérifier si les villes de départ et d'arrivée ainsi que l'avion pour le vol existent dans le serveur
        if (!$isExistCityArrival || !$isExistCityDeparture || !$isExistAirplane) {

            if (!$isExistCityArrival) {
                throw new NotFoundHttpException(json_encode([ // renvoyer un code d'erreur 404 car ressource non trouvée
                    'message' => 'La ville d\'arrivée choisie est introuvable dans le système'
                ]));
            }

            if (!$isExistCityDeparture) {
                throw new NotFoundHttpException(json_encode([ // renvoyer un code d'erreur 404 car ressource non trouvée
                    'message' => 'La ville de départ choisie est introuvable dans le système'
                ]));
            }

            if (!$isExistAirplane) {
                throw new NotFoundHttpException(json_encode([ // renvoyer un code d'erreur 404 car ressource non trouvée
                    'message' => 'L\'avion choisi est introuvable'
                ]));
            }
        }


        // Vérifier si les villes de destination et d'arrivée sont bien differentes
        if ($isExistCityDeparture == $isExistCityArrival) {
            throw new UnprocessableEntityHttpException(json_encode([ // renvoyer un code d'erreur 422 car problème logique des données
                'message' => 'Les villes de départ et de destination doivent être differentes'
            ]));
        }

        // Vérifier si la date d'arrivée est superieure à la date de départ
        if ($data->dateDeparture >= $data->dateArrival) {
            throw new UnprocessableEntityHttpException(json_encode([ // renvoyer un code d'erreur 422 car problème logique des données
                'message' => 'La date d\'arrivée doit être supérieure à la date de départ'
            ]));
        }

        if ($data->dateDeparture <= new DateTime()) {
            throw new unprocessableEntityHttpException(json_encode( // renvoyer un code d'erreur 422 car problème logique des données
                [
                    'message' => 'La date date départ ne doit pas être inférieure à la date du jour'
                ]
            ));
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
            throw new ConflictHttpException(json_encode([
                'message' => 'Un vol similaire portant les mêmes informations de vol existent'
            ]));
        }

        // Ecrire la requête et envoyer au serveur le nouveau vol d'avion
        $flight = new Flight;
        $flight->setCreatedAt(new \DateTimeImmutable())
            ->setCityDeparture($isExistCityDeparture)
            ->setCityArrival($isExistCityArrival)
            // ->setAirplaneModel($isExistAirplane)
            ->setDateDeparture($data->dateDeparture)
            ->setDateArrival($data->dateArrival);

        // dd($flight);
        $this->entityManager->persist($flight);
        $this->entityManager->flush();

        // Retourner une réponse au client (exemple navigateur ou Postman)
        $CityDepartureDto = new CityRequestDto; // Exceptionnellement j'ai utilisé ce DTO de requête car la structure de données n'est pas pareille qu'avec le DTO CityResponseDto
        $CityDepartureDto->name = $flight->getCityArrival()->getName();
        $CityDepartureDto->country = $flight->getCityDeparture()->getCountry()->getName();

        $CityArrivalDto = new CityRequestDto; // Exceptionnellement j'ai utilisé ce DTO de requête car la structure de données n'est pas pareille qu'avec le DTO CityResponseDto
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
