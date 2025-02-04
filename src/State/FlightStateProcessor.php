<?php

namespace App\State;

use App\Repository\CityRepository;
use ApiPlatform\Metadata\Operation;
use App\Repository\CountryRepository;
use App\Repository\AirplaneRepository;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Flight;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FlightStateProcessor implements ProcessorInterface
{
    public function __construct(
        private CityRepository $cityRepository,
        private CountryRepository $countryRepository,
        private AirplaneRepository $airplaneRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        // Rechercher l'objet pays de départ
        $countryArrival = $this->countryRepository->findOneByName([
            'name' => $data->getCityArrival()->country
        ]);

        // Rechercher l'objet pays de départ
        $countryDeparture = $this->countryRepository->findOneByName([
            'name' => $data->getCityDeparture()->country
        ]);


        // // Rechercher les villes de départ et de destination
        $isExistCityDeparture = $this->cityRepository->findOneBy(
            [
                'name' => $data->getCityDeparture()->name, // le nom de la ville de départ
                'country' => $countryDeparture // le pays de départ
            ]
        );

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

        // dd($data->dateDeparture);

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
        // $flitghtDto = new
    }
}
