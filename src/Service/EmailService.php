<?php

namespace App\Service;

use DateTime;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    // Injection de dépendances
    public function __construct(
        private MailerInterface $mailer
    ) {}


    public function confirmReservation(string $passengerEmail, string $cityDeparture, string $cityArrival, DateTime $dateDeparture)
    {
        // Création du mail
        $email = (new Email())
            ->from('Service client <contact@fahami.fr>')
            ->to($passengerEmail)
            ->subject('Votre réservation est confirmée')
            ->text('Nous vous confirmons la réservation du vol en provenance de ' . $cityDeparture . ' pour ' . $cityArrival . ' le ' . $dateDeparture->format('Y-m-d H:i:s'));

        // Envoi du mail
        $this->mailer->send($email);
    }
}
