<?php

namespace App\State;

use App\Entity\Flight;
use App\Dto\CityResponseDto;
use App\Dto\FlightResponseDto;
use App\Dto\AirportResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Repository\FlightRepository;
use App\Repository\AirportRepository;
use App\Repository\CaptainRepository;
use App\Repository\CompanyRepository;
use App\Repository\CopilotRepository;
use App\Repository\AirplaneRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;


class UpdateFlightProcessor implements ProcessorInterface
{
    public function __construct(
        private AirplaneRepository $airplaneRepository,
        private FlightRepository $flightRepository,
        private CaptainRepository $captainRepository,
        private CopilotRepository $copilotRepository,
        private EntityManagerInterface $entityManager,
        private AirportRepository $airportRepository,
        private CompanyRepository $companyRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // Récuperer l'objet Passager présent en BDD avant mise à jour
        $flight = $this->flightRepository->findOneById($uriVariables['id']);

        if (!$flight) {
            throw new NotFoundHttpException('Aucun vol avec l\'id renseigné');
        }

        $airportDeparture = $data->airportDeparture ?? null;
        $airportArrival = $data->airportArrival ?? null;
        $airplane = $data->airplane ?? null;
        $company = $data->company ?? null;
        $captain = $data->captain ?? null;
        $dateDeparture = $data->dateDeparture ?? $flight->getDateDeparture();
        $dateArrival = $data->dateArrival ?? $flight->getDateArrival();

        // Rechercher les villes de départ et de destination à l'aide du nom de la ville et du pays
        $isExistAirportDeparture = $airportDeparture
            ? $this->airportRepository->findDestination($airportDeparture->name, $airportDeparture->city->countryName)
            : $flight->getAirportDeparture();

        $isExistAirportArrival = $airportArrival
            ? $this->airportRepository->findDestination($airportArrival->name, $airportArrival->city->countryName)
            : $flight->getAirportArrival();

        // Rechercher l'avion à assigner
        $isExistAirplane = $airplane
            ? $this->airplaneRepository->findOneBy(["reference" => $airplane->reference])
            : $flight->getAirplane();

        // dd($isExistAirplane);

        // Vérifier si les aéroports de départ et d'arrivée ainsi que l'avion pour le vol existent dans le serveur
        if (!$isExistAirportDeparture || !$isExistAirportArrival || !$isExistAirplane) {

            if (!$isExistAirportArrival) {
                // renvoyer un code d'erreur 404 car ressource non trouvée
                throw new NotFoundHttpException(
                    'La ville d\'arrivée choisie est introuvable dans le système'
                );
            }

            if (!$isExistAirportDeparture) {
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
        if ($isExistAirportDeparture == $isExistAirportArrival) {
            // renvoyer un code d'erreur 422 car problème logique des données
            throw new UnprocessableEntityHttpException('Les aéroports de départ et de destination doivent être differents');
        }

        // Vérifier si la date d'arrivée est superieure à la date de départ
        if ($dateDeparture >= $dateArrival) {
            // renvoyer un code d'erreur 422 car problème logique des données
            throw new UnprocessableEntityHttpException('La date d\'arrivée doit être supérieure à la date de départ');
        }

        if ($dateDeparture <= new \DateTime()) {
            // renvoyer un code d'erreur 422 car problème logique des données
            throw new UnprocessableEntityHttpException('La date date départ ne doit pas être inférieure à la date du jour');
        }

        // Déterminer la durée du voyage
        $durationFlight = date_diff($dateDeparture, $dateArrival);

        // Si le voyage dure au moins plus de 24heures, renvoyer une erreur
        if ($durationFlight->m >= 1) {
            throw new UnprocessableEntityHttpException('Ce vol direct dépasse anormalement 1 mois');
        }
        if ($durationFlight->d >= 1) {
            throw new UnprocessableEntityHttpException('Ce vol direct dépasse anormalement 24 heures');
        }

        // Vérifier si le pilote existe
        $isExistCaptain = $flight->getCaptain();

        if ($captain) {
            $isExistCaptain = $this->captainRepository->findOneBy([
                'email' => $captain->email
            ]);

            if (!$isExistCaptain) {
                throw new ConflictHttpException("Aucun commandant de bord trouvé avec les informations fournies");
            }

            // //Vérifier si le commandant de bord est disponible
            // $numberFleetsByCaptainInPeriod = $this->flightRepository->countOverlappingFlightsForCaptain($isExistCaptain->getId(), $data->dateDeparture, $data->dateArrival);

            // if ($numberFleetsByCaptainInPeriod > 0) {
            //     throw new ConflictHttpException('Le commandant de bord est occupé pendant la même période du vol');
            // }
        }



        // dd($numberFleetsByCaptainInPeriod);

        // Enregistrement des copilotes dans le vol
        // Vérifier si les copilotes existent
        $copilots = [];

        // Vérifier si max 2 copilotes maximums acceptés pour un vol n'est pas atteinte    
        if (isset($data->copilots)) {
            if (count($data->copilots) > 2) {
                throw new ConflictHttpException('Maximum de copilots atteint');
            }

            foreach ($data->copilots as $copilot) {
                // Rechercher l'existence de chacun d'eux
                $isCopilotExist = $this->copilotRepository->findOneBy([
                    'firstname' => $copilot['firstname'],
                    'lastname' => $copilot['lastname'],
                    'email' => $copilot['email']
                ]);



                if ($isCopilotExist) { // Si le copilote existe, le joindre à la sous-équipe des copilotes
                    // Vérifier si le copilote est disponible pendant la période du vol
                    $numberFleetsByCopilotInPeriod = $this->flightRepository->countOverlappingFlightsForCopilot($isCopilotExist->getId(), $dateDeparture, $dateArrival);

                    if ($numberFleetsByCopilotInPeriod > 0) {
                        throw new ConflictHttpException('Le copilote ' . $isCopilotExist->getFullname() . ' n\'est pas disponible pendant la période du vol');
                    }

                    $copilots[] = $isCopilotExist;
                } else {
                    throw new ConflictHttpException('Un copilote renseigné du nom de ' .  $copilot['lastname'] . ' ' .  $copilot['firstname'] . ' n\'existe pas');
                }
            }
        }

        // recueillir les informations des copilotes

        // dd($copilots);

        // Rechercher s'il n'y pas de vol similaire
        $isExistFlight = $this->flightRepository->findOneBy([
            'dateDeparture' => $dateDeparture,
            'dateArrival' => $dateArrival,
            'airportDeparture' => $isExistAirportDeparture,
            'airportArrival' => $isExistAirportArrival,
            'airplane' => $isExistAirplane,
            'price' => $data->price ?? $flight->getPrice(),
            'captain' => $isExistCaptain
        ]);


        if ($isExistFlight) {
            throw new ConflictHttpException('Un vol similaire portant les mêmes informations de vol existe');
        }
        // dd($isExistFlight);

        // Vérifier si la comapgnie existe
        $isCompanyExist = $company
            ? $this->companyRepository->findOneBy(["name" => $company->name])
            : $flight->getCompany();

        if (!$isCompanyExist) {
            throw new NotFoundHttpException('Aucune compagnie trouvée avec le nom fourni');
        }

        // chercher le vol et le mettre à jour dans le serveur API

        $flight->setUpdatedAt(new \DateTime)
            ->setAirportDeparture($isExistAirportDeparture)
            ->setAirportArrival($isExistAirportArrival)
            ->setAirplane($isExistAirplane)
            ->setPrice($data->price ?? $flight->getPrice())
            ->setIsDirect($data->isDirect ?? $flight->isDirect())
            ->setIsCanceled($data->isCanceled ?? $flight->isCanceled())
            ->setIsLate($data->isLate ?? $flight->isLate())
            ->setDateDeparture($dateDeparture)
            ->setDateArrival($dateArrival)
            ->setCompany($isCompanyExist);

        if ($captain) {
            $flight->setCaptain($isExistCaptain);
        }

        if ($company) {
            $flight->setCompany($isCompanyExist);
        }

        // Si disponibilité de chaque copilote validée en amont, enregistrer chaque copilote au vol
        // foreach ($copilots as $copilot) {
        //     $flight->addCopilot($copilot);
        // }

        // dd($flight);


        // Asigner un avion 
        $flight->setAirplane($isExistAirplane);


        $this->entityManager->persist($flight);
        $this->entityManager->flush();

        // dd($flight);

        // Retourner une réponse au client (exemple navigateur ou Postman)
        $airportDepartureDto = new AirportResponseDto; // Exceptionnellement j'ai utilisé ce DTO de requête car la structure de données n'est pas pareille qu'avec le DTO CityResponseDto
        $airportDepartureDto->name = $flight->getAirportDeparture()->getCity()->getCountry()->getName();

        $cityDepartureDto = new CityResponseDto;
        $cityDepartureDto->name = $flight->getAirportDeparture()->getCity()->getName();
        $cityDepartureDto->countryName = $flight->getAirportDeparture()->getCity()->getCountry()->getName();
        $airportDepartureDto->city = $cityDepartureDto;

        $airportArrivalDto = new AirportResponseDto; // Exceptionnellement j'ai utilisé ce DTO de requête car la structure de données n'est pas pareille qu'avec le DTO CityResponseDto
        $airportArrivalDto->name = $flight->getAirportArrival()->getCity()->getCountry()->getName();


        $cityArrivalDto = new CityResponseDto;
        $cityArrivalDto->name = $flight->getAirportArrival()->getCity()->getName();
        $cityArrivalDto->countryName = $flight->getAirportArrival()->getCity()->getCountry()->getName();
        $airportArrivalDto->city = $cityArrivalDto;



        $flitghtDto = new FlightResponseDto;
        $flitghtDto->id = $flight->getId();
        $flitghtDto->dateDeparture = $flight->getDateDeparture();
        $flitghtDto->dateArrival = $flight->getDateArrival();
        $flitghtDto->airportDeparture = $airportDepartureDto;
        $flitghtDto->airportArrival = $airportArrivalDto;
        $flitghtDto->price = $flight->getPrice();
        $flitghtDto->isDirect = $flight->isDirect();
        $flitghtDto->isLate = $flight->isLate();
        $flitghtDto->isCanceled = $flight->isCanceled();
        $flitghtDto->createdAt = $flight->getCreatedAt();
        $flitghtDto->updatedAt = $flight->getUpdatedAt();

        return $flitghtDto; // retourner le DTO contenant les informations du vol
    }
}
