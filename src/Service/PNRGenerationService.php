<?php

namespace App\Service;

use App\Repository\ReservationRepository;

// Service pour générer la référence unique de chaque réservation de vol par un passager
class PNRGenerationService
{
    // Injection de dépendances
    public function __construct(
        private ReservationRepository $reservationRepository
    ) {}

    public function attributePNRNumber(): string
    {
        // Tant que le PNR existe, générer un nouveau jusqu'à trouver un PNR unique à assigner à la réservation
        do { // executer la boucle au moins une fois
            $pnr = bin2hex(random_bytes(3));

            $isPNRexist =  $this->reservationRepository->findOneBy([
                'passengerNameRecord' => $pnr
            ]);

            // if (!$isPNRexist) {
            //     return $pnr;
            // }
        } while ($isPNRexist); // continuer d'itérér jusqu'à trouver une valeur PNR unique

        return strtoupper($pnr); // dans tous les cas, un PNR unique finira par être généré et retourner sa valeur
    }
}
