<?php

namespace App\State;

use DateTime;
use App\Entity\Flight;
use App\Dto\FlightResponseDto;
use ApiPlatform\Metadata\Operation;
use App\Repository\FlightRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\AirportResponseDto;
use App\Dto\CityResponseDto;
use App\Repository\AirplaneRepository;
use App\Repository\AirportRepository;
use App\Repository\CaptainRepository;
use App\Repository\CompanyRepository;
use App\Repository\CopilotRepository;
use App\Repository\StatusRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InsertFlightStateProcessor implements ProcessorInterface
{
    public function __construct(
        private AirplaneRepository $airplaneRepository,
        private FlightRepository $flightRepository,
        private CaptainRepository $captainRepository,
        private CopilotRepository $copilotRepository,
        private EntityManagerInterface $entityManager,
        private AirportRepository $airportRepository,
        private CompanyRepository $companyRepository,
        private StatusRepository $statusRepository,
        private ValidatorInterface $validator
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // dd($this->statusRepository->findOneBy(["name" => "Confirmé"]));

        // Rechercher les villes de départ et de destination à l'aide du nom de la ville et du pays
        $isExistAirportDeparture = $this->airportRepository->findDestination($data->airportDeparture->name, $data->airportDeparture->city->countryName);

        $isExistAirportArrival = $this->airportRepository->findDestination($data->airportArrival->name, $data->airportArrival->city->countryName);

        // dd($isExistAirportDeparture);
        // dd($isExistAirportArrival);
        // Rechercher l'avion à assigner
        $isExistAirplane = $this->airplaneRepository->findOneBy(
            [
                "reference" => $data->airplane->reference
            ]
        );

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
        if (isset($data->captain)) {
            $isExistCaptain = $this->captainRepository->findOneBy([
                'email' => $data->captain->email
            ]);

            // dd($isExistCaptain);

            if (!$isExistCaptain) {
                throw new ConflictHttpException("Aucun commandant de bord trouvé avec les informations fournies");
            }

            //Vérifier si le commandant de bord est disponible
            $numberFleetsByCaptainInPeriod = $this->flightRepository->countOverlappingFlightsForCaptain($isExistCaptain->getId(), $data->dateDeparture, $data->dateArrival);

            if ($numberFleetsByCaptainInPeriod > 0) {
                throw new ConflictHttpException('Le commandant de bord est occupé pendant la même période du vol');
            }
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
                    $numberFleetsByCopilotInPeriod = $this->flightRepository->countOverlappingFlightsForCopilot($isCopilotExist->getId(), $data->dateDeparture, $data->dateArrival);

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
            'dateDeparture' => $data->dateDeparture,
            'dateArrival' => $data->dateArrival,
            'airportDeparture' => $isExistAirportDeparture,
            'airportArrival' => $isExistAirportArrival,
            'airplane' => $isExistAirplane
        ]);


        if ($isExistFlight) {
            throw new ConflictHttpException('Un vol similaire portant les mêmes informations de vol existent');
        }
        // dd($isExistFlight);

        // Vérifier si la comapgnie existe
        $isCompanyExist = $this->companyRepository->findOneBy(["name" => $data->company->name]);

        if (!$isCompanyExist) {
            throw new NotFoundHttpException('Aucune compagnie trouvée avec le nom fourni');
        }

        // Ecrire la requête et envoyer au serveur le nouveau vol d'avion
        $flight = new Flight;
        $flight->setCreatedAt(new \DateTimeImmutable())
            ->setAirportDeparture($isExistAirportDeparture)
            ->setAirportArrival($isExistAirportArrival)
            ->setAirplane($isExistAirplane)
            ->setStatus($this->statusRepository->findOneBy(["name" => "Confirmé"]))
            ->setPrice($data->price)
            ->setIsDirect(true)
            ->setIsCanceled(false)
            ->setIsLate(false)
            ->setDateDeparture($data->dateDeparture)
            ->setDateArrival($data->dateArrival)
            ->setCompany($isCompanyExist);

        if (isset($data->captain)) {
            $flight->setCaptain($isExistCaptain);
        }

        if (isset($data->company)) {
            $flight->setCompany($isCompanyExist);
        }

        // Si disponibilité de chaque copilote validée en amont, enregistrer chaque copilote au vol
        // foreach ($copilots as $copilot) {
        //     $flight->addCopilot($copilot);
        // }

        // dd($flight);


        // Asigner un avion 
        $flight->setAirplane($isExistAirplane);


        // Validation des données avant envoi en base de données 
        $errors = $this->validator->validate($flight); // rechercher les erreurs ne remplissant pas les contraintes de validation des données de l'entité Article 

        // Si il n'y a pas d'erreur trouvée
        if ($errors == null) {
            // Créer la requête et env
            $this->entityManager->persist($flight);
            $this->entityManager->flush();
        }

        if (count($errors ?? []) > 0) { // s'il y a des erreurs trouvées 
            $errorMessages = [];

            // Générer une erreur 400 (= "bad request") avec les messages d'erreur détaillés 
            foreach ($errors as $error) {
                if ($error->getPropertyPath() !== "createdAt") {
                    $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
                }
            }

            throw new BadRequestHttpException(json_encode($errorMessages));
        }

        // Retourner une réponse au client (exemple navigateur ou Postman)
        $airportDepartureDto = new AirportResponseDto; // Exceptionnellement j'ai utilisé ce DTO de requête car la structure de données n'est pas pareille qu'avec le DTO CityResponseDto
        $airportDepartureDto->name = $flight->getAirportDeparture()->getName();

        $cityDepartureDto = new CityResponseDto;
        $cityDepartureDto->name = $flight->getAirportDeparture()->getCity()->getName();
        $cityDepartureDto->countryName = $flight->getAirportDeparture()->getCity()->getCountry()->getName();
        $airportDepartureDto->city = $cityDepartureDto;

        $airportArrivalDto = new AirportResponseDto; // Exceptionnellement j'ai utilisé ce DTO de requête car la structure de données n'est pas pareille qu'avec le DTO CityResponseDto
        $airportArrivalDto->name = $flight->getAirportArrival()->getName();


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
        $flitghtDto->status = $flight->getStatus()->getName();

        return $flitghtDto; // retourner le DTO contenant les informations du vol
    }
}
