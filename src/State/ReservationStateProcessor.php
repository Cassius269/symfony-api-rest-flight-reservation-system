<?php

namespace App\State;

use App\Entity\Passenger;
use App\Entity\Reservation;
use App\Repository\CityRepository;
use ApiPlatform\Metadata\Operation;
use App\Repository\FlightRepository;
use App\Service\HashPasswordService;
use App\Repository\PassengerRepository;
use App\Service\SeatReservationService;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\AirplaneModelRepository;
use ApiPlatform\Validator\ValidatorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Service\EmailService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ReservationStateProcessor implements ProcessorInterface
{
    // Injection des dépendances
    public function __construct(
        private PassengerRepository $passengerRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private FlightRepository $flightRepository,
        private CityRepository $cityRepository,
        // private AirplaneModelRepository $airplaneModelRepository,
        private ValidatorInterface $validator,
        private HashPasswordService $hashPasswordService,
        private SeatReservationService $seatReservationService,
        private EmailService $emailService
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // dd($data);

        // Rechercher si le passager existe déjà dans le serveur de l'API
        $passenger = $data->passenger;
        // dd($passenger);

        $isExistPassenger = $this->passengerRepository->findOneBy(
            [
                // "firstname" => $passenger->firstname,
                // "lastname" => $passenger->lastname,
                "email" => $passenger->email
            ]
        );

        // dd($isExistPassenger);

        // Si le passager n'existe pas, le créer
        if (!$isExistPassenger) {
            $newPassenger = new Passenger();
            $newPassenger->setCreatedAt(new \DateTimeImmutable())
                ->setFirstname($passenger->firstname)
                ->setLastname($passenger->lastname)
                ->setEmail($passenger->email)
                ->setRoles(["ROLE_PASSENGER"]);

            // Hasher le mot de passe à l'aide d'un service personnalisé de hashage de mot de passage
            $this->hashPasswordService->hashPassword("123456789", $newPassenger);

            // Préparer la requête avant d'envoyer en base de données
            $this->entityManager->persist($newPassenger);
        }

        // Ajouter un passager à un vol
        // Rechercher les villes de départ et de destination à l'aide du nom de la ville et du pays

        $isExistCityDeparture = $this->cityRepository->findDestinationByCityAndCountry($data->flight->getCityDeparture()->name, $data->flight->getCityDeparture()->country);

        $isExistCityArrival = $this->cityRepository->findDestinationByCityAndCountry($data->flight->getCityArrival()->name, $data->flight->getCityArrival()->country);

        // Rechercher l'avion à assigner
        // Vérifier si les villes de départ et d'arrivée ainsi que l'avion pour le vol existent dans le serveur
        if (!$isExistCityArrival || !$isExistCityDeparture) {

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
        }

        // Vérifier si les villes de destination et d'arrivée sont bien differentes
        if ($isExistCityDeparture == $isExistCityArrival) {
            throw new UnprocessableEntityHttpException(json_encode([ // renvoyer un code d'erreur 422 car problème logique des données
                'message' => 'Les villes de départ et de destination doivent être differentes'
            ]));
        }

        // Vérifier si la date d'arrivée est supérieure à la date de départ
        if ($data->flight->dateArrival <= $data->flight->dateDeparture) {
            throw new UnprocessableEntityHttpException(json_encode([
                'message' => 'La date d\'arrivée doit être supérieure à la date de départ'
            ]));
        }

        // recherche de vol
        $isExistFlight = $this->flightRepository->findOneBy([
            'dateDeparture' => $data->flight->dateDeparture,
            'dateArrival' => $data->flight->dateArrival,
            'cityDeparture' => $isExistCityDeparture,
            'cityArrival' => $isExistCityArrival,
        ]);


        // Si le vol n'existe pas, renvoyer une erreur 404 au client
        if (!$isExistFlight) {
            throw new NotFoundHttpException('Le vol n\‘existe pas encore en base de données');
        }

        // Compter le nombre de passagers d'un vols
        $actualReservations = $isExistFlight->getReservations();

        $passengersCount = null;
        foreach ($actualReservations as $actualReservation) {
            $passengersCount++;
        }
        // dd($passengersCount);

        if ($isExistFlight->getAirPlane()->getAirplaneModel()->getcapacity() <= $passengersCount) {
            throw new UnprocessableEntityHttpException(json_encode([ // renvoyer un code d'erreur 422 car problème logique des données
                'message' => 'La capacité maximale de l\'avion choisi pour le vol est atteinte'
            ]));
        }

        $reservation = new Reservation();
        $reservation->setCreatedAt(new \DateTimeImmutable())
            // ->setNumberFlightSeat('7A')
            ->setPrice(800) // prix par défaut 800euros
            ->setFlight($isExistFlight)
            ->setPassenger($isExistPassenger ?? $newPassenger);

        $this->seatReservationService->attributeASeat($isExistFlight, $reservation);

        $errors = $this->validator->validate($reservation);

        if (count($errors ?? []) > 0) {
            throw new ValidationException((string) $errors);
        }

        // Enregistrer et envoyer en base de données le nouveau passager (si création) et la réservation
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        // dd($passenger);
        // Envoyer un mail de confirmation au passage
        $this->emailService->confirmReservation($passenger->email, $isExistCityDeparture->getName(), $isExistCityArrival->getName(), $reservation->getFlight()->getDateDeparture()); // récuperer l'information depuis le DTO de requête

        // Renvoyer une réponse JSON au client en cas de réussite de la création de la réservation pour un passager
        return $data; // retouner les valeurs entrée si pas de traitement particulier en sortie
    }
}
