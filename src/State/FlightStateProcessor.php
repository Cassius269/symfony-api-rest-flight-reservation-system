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
use App\Repository\AirplaneRepository;
use App\Repository\CaptainRepository;
use App\Repository\CopilotRepository;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class FlightStateProcessor implements ProcessorInterface
{
    public function __construct(
        private CityRepository $cityRepository,
        private CountryRepository $countryRepository,
        private AirplaneRepository $airplaneRepository,
        private FlightRepository $flightRepository,
        private CaptainRepository $captainRepository,
        private CopilotRepository $copilotRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // dd($data);
        // Rechercher les villes de départ et de destination à l'aide du nom de la ville et du pays
        $isExistCityDeparture = $this->cityRepository->findDestinationByCityAndCountry($data->getCityDeparture()->name, $data->getCityDeparture()->country);
        // dd($isExistCityDeparture);

        $isExistCityArrival = $this->cityRepository->findDestinationByCityAndCountry($data->getCityArrival()->name, $data->getCityArrival()->country);

        // dd($isExistCityArrival);

        // Rechercher l'avion à assigner
        $isExistAirplane = $this->airplaneRepository->findOneById($data->airplaneId);

        // Vérifier si les villes de départ et d'arrivée ainsi que l'avion pour le vol existent dans le serveur
        if (!$isExistCityArrival || !$isExistCityDeparture || !$isExistAirplane) {

            if (!$isExistCityArrival) {
                // renvoyer un code d'erreur 404 car ressource non trouvée
                throw new NotFoundHttpException(
                    'La ville d\'arrivée choisie est introuvable dans le système'
                );
            }

            if (!$isExistCityDeparture) {
                // renvoyer un code d'erreur 404 car ressource non trouvée
                throw new NotFoundHttpException('La ville de départ choisie est introuvable dans le système');
            }

            if (!$isExistAirplane) {
                throw new NotFoundHttpException( // renvoyer un code d'erreur 404 car ressource non trouvée
                    'L\'avion choisi est introuvable'
                );
            }
        }


        // Vérifier si les villes de destination et d'arrivée sont bien differentes
        if ($isExistCityDeparture == $isExistCityArrival) {
            // renvoyer un code d'erreur 422 car problème logique des données
            throw new UnprocessableEntityHttpException('Les villes de départ et de destination doivent être differentes');
        }

        // Vérifier si la date d'arrivée est superieure à la date de départ
        if ($data->dateDeparture >= $data->dateArrival) {
            // renvoyer un code d'erreur 422 car problème logique des données
            throw new UnprocessableEntityHttpException('La date d\'arrivée doit être supérieure à la date de départ');
        }

        if ($data->dateDeparture <= new DateTime()) {
            // renvoyer un code d'erreur 422 car problème logique des données
            throw new UnprocessableEntityHttpException('La date date départ ne doit pas être inférieure à la date du jour');
        }

        // Déterminer la durée du voyage
        $durationFlight = date_diff($data->dateDeparture, $data->dateArrival);

        // Si le voyage dure au moins plus de 24heures, renvoyer une erreur
        if ($durationFlight->m >= 1) {
            throw new UnprocessableEntityHttpException('Ce vol direct dépasse anormalement 1 mois');
        }
        if ($durationFlight->d >= 1) {
            throw new UnprocessableEntityHttpException('Ce vol direct dépasse anormalement 24 heures');
        }

        // Vérifier si le pilote existe
        $isExistCaptain = $this->captainRepository->findOneBy([
            'firstname' => $data->captain->firstname,
            'lastname' => $data->captain->lastname,
            'email' => $data->captain->email
        ]);

        if (!$isExistCaptain) {
            throw new ConflictHttpException("Aucun commandant de bord trouvé avec les informations fournies");
        }

        //Vérifier si le commandant de bord est disponible
        $numberFleetsByCaptainInPeriod = $this->flightRepository->countOverlappingFlightsForCaptain($isExistCaptain->getId(), $data->dateDeparture, $data->dateArrival);

        if ($numberFleetsByCaptainInPeriod > 1) {
            throw new ConflictHttpException('Le commandant est occupé pendant la même période du vol');
        }

        // dd($numberFleetsByCaptainInPeriod);

        // Enregistrement des copilotes dans le vol
        // Vérifier si les copilotes existent
        $copilots = [];

        // Vérifier si max 2 copilotes maximums acceptés pour un vol n'est pas atteinte    
        if (count($data->copilots) > 2) {
            throw new ConflictHttpException('Maximum de copilots atteint');
        }

        // recueillir les informations des copilotes
        foreach ($data->copilots as $copilot) {
            // Rechercher l'existence de chacun d'eux
            $isCopilotExist = $this->copilotRepository->findOneBy([
                'firstname' => $copilot['firstname'],
                'lastname' => $copilot['lastname'],
                'email' => $copilot['email']
            ]);



            if ($isCopilotExist) { // Si le copilote existe, le joindre à la sous-équipe des copilotes
                // Vérifier si le copilote est disponible pendant la période du vol
                $numberFleetsByCopilotInPeriod = $this->flightRepository->countOverlappingFlightsForCopilot($isCopilotExist->getId(), $data->dateDeparture, $data->dateArrival);

                if ($numberFleetsByCopilotInPeriod > 0) {
                    throw new ConflictHttpException('Le copilote ' . $isCopilotExist->getFullname() . ' n\'est pas disponible pendant la période du vol');
                }

                $copilots[] = $isCopilotExist;
            } else {
                throw new ConflictHttpException('Un copilote renseigné du nom de ' .  $copilot['lastname'] . ' ' .  $copilot['firstname'] . ' n\'existe pas');
            }
        }

        // dd($copilots);

        // Rechercher s'il n'y pas de vol similaire
        $isExistFlight = $this->flightRepository->findOneBy([
            'dateDeparture' => $data->dateDeparture,
            'dateArrival' => $data->dateArrival,
            'cityDeparture' => $isExistCityDeparture,
            'cityArrival' => $isExistCityArrival,
            'airplane' => $isExistAirplane
        ]);


        if ($isExistFlight) {
            throw new ConflictHttpException('Un vol similaire portant les mêmes informations de vol existent');
        }
        // dd($isExistFlight);

        // Ecrire la requête et envoyer au serveur le nouveau vol d'avion
        $flight = new Flight;
        $flight->setCreatedAt(new \DateTimeImmutable())
            ->setCityDeparture($isExistCityDeparture)
            ->setCityArrival($isExistCityArrival)
            ->setAirplane($isExistAirplane)
            ->setDateDeparture($data->dateDeparture)
            ->setDateArrival($data->dateArrival)
            ->setCaptain($isExistCaptain);

        // Si disponibilité de chaque copilote validée en amont, enregistrer chaque copilote au vol
        foreach ($copilots as $copilot) {
            $flight->addCopilot($copilot);
        }

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
        $flitghtDto->id = $flight->getId();
        $flitghtDto->dateDeparture = $flight->getDateDeparture();
        $flitghtDto->dateArrival = $flight->getDateArrival();
        $flitghtDto->cityDeparture = $CityDepartureDto;
        $flitghtDto->cityArrival = $CityArrivalDto;

        return $flitghtDto; // retourner le DTO contenant les informations du vol
    }
}
