<?php

namespace App\Service;

use App\Entity\Reservation;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailService
{
    // Injection de dépendances
    public function __construct(
        private MailerInterface $mailer
    ) {}


    public function confirmReservation(Reservation $reservation)
    {
        // Création du mail
        $email = (new TemplatedEmail())
            ->from('Service client App Réservation de vols <contact@fahami.fr>')
            ->to($reservation->getPassenger()->getEmail()) // configurer le mail du destinataire dynamiquement depuis la réservation du passager 
            ->subject('Votre réservation est confirmée')
            ->htmlTemplate('@emails/email_confirmation_reservation.html.twig')
            ->context(
                [ // Passer des variables à la template Twig du mail de confirmation
                    'reservation' => $reservation
                ]
            );

        // Envoi du mail
        $this->mailer->send($email);
    }
}
