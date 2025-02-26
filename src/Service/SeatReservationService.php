<?php

namespace App\Service;

use App\Entity\Flight;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;

class SeatReservationService
{
    // Déclaration d'une constante avec les lettres des sièges
    public  const LETTERS = ['A', 'B', 'C', 'D', 'E', 'F'];

    // Injection de dépendance
    public function __construct(
        private ReservationRepository $reservationRepository
    ) {}

    public function attributeASeat(Flight $flight, Reservation $reservation)
    {
        // ----- Algo d'attribution de siège ----
        // Etape 1: Rechercher la capacité de l'avion
        // Etape 2: Définir le nombre de rangée maximal possible (6 sièges par rangée)
        // Etape 3: Assigner le premier siège disponible au passager

        $reservations = $flight->getReservations();
        $countPassengers = count($reservations); // compter le nombre de réservations actuelles d'un vol

        $airplaneCapacity = $flight->getAirplane()->getCapacity(); // retrouver la capacité maximale de l'avion en termes de passagers
        $maxRangees = ceil(($airplaneCapacity / 6)); // le nombre de rangée maximum dans l'avion

        // Si l'avion n'est pas plein
        if ($countPassengers < $airplaneCapacity) {

            for ($i = 1; $i <= $maxRangees; $i++) {

                foreach (SeatReservationService::LETTERS as $letter) {

                    $seat = $this->reservationRepository->findOneBy([
                        "numberFlightSeat" => $i . $letter,
                        "flight" => $flight
                    ]);

                    if (!$seat) {
                        $reservation->setNumberFlightSeat($i . $letter);
                        return; // Quitter la méthode après avoir attribué le premier disponible
                    }
                }
            }
        }
    }
}
