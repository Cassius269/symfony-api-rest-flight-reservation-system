<?php

namespace App\Tests\Unit;

use App\Entity\Flight;
use App\Entity\Passenger;
use App\Entity\Reservation;
use DateTime;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PassengerTest extends TestCase
{
    #[Test]
    public function it_creates_a_passenger_with_given_values(): void
    {
        // Arranger
        $firstname = 'Jean';
        $lastname = 'DUPONT';
        $email = 'jean-dupont@email.com';
        $birthDate = new \DateTime('2000-01-01');
        $rolePassenger = 'ROLE_PASSENGER';
        $password = '123456789';
        $today = new DateTime('now');

        // Agir
        $passenger = new Passenger();
        $passenger->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setBirthDate($birthDate)
            ->setRoles([$rolePassenger])
            ->setPassword($password)
            ->setCreatedAt(new \DateTimeImmutable('now'));

        // Vérifier
        $this->assertSame($firstname, $passenger->getFirstname());
        $this->assertSame($lastname, $passenger->getLastname());
        $this->assertSame($email, $passenger->getEmail());
        $this->assertSame($birthDate, $passenger->getBirthDate());
        $this->assertContains($rolePassenger, $passenger->getRoles());
        $this->assertSame($password, $passenger->getPassword());
    }

    #[Test]
    public function it_adds_a_reservation_to_a_passenger(): void
    {
        // Arranger
        $passenger = new Passenger();
        $passenger->setFirstname('Jean')
            ->setLastname('DUPONT')
            ->setEmail('j-dupont@test.fr')
            ->setBirthDate(new DateTime('2000-01-01'))
            ->setRoles(['ROLE_PASSENGER'])
            ->setPassword('123456789')
            ->setCreatedAt(new \DateTimeImmutable('now'));

        $reservation1 = new Reservation();
        $reservation1->setNumberFlightSeat('2A')
            ->setPassengerNameRecord('FR4FGT')
            ->setPrice('21.4')
            ->setCreatedAt(new \DateTimeImmutable('now'));

        $reservation2 = new Reservation();
        $reservation2->setNumberFlightSeat('5B')
            ->setPassengerNameRecord('FR7RKT')
            ->setPrice('26.4')
            ->setCreatedAt(new \DateTimeImmutable('now'));

        // Agir
        $passenger->addReservation($reservation1);
        $passenger->addReservation($reservation2);

        // Vérifier
        $reservations = $passenger->getReservations();
        // dd($passenger->getReservations());
        $this->assertCount(2, $reservations);
        $this->assertContains($reservation1, $reservations);
        $this->assertContains($reservation2, $reservations);
        $this->assertSame($passenger, $reservation1->getPassenger());
        $this->assertSame($passenger, $reservation2->getPassenger());
    }

    #[Test]
    public function it_removes_a_reservation_from_a_passenger(): void
    {
        // Arranger
        $passenger = new Passenger();
        $passenger->setFirstname('Jean')
            ->setLastname('DUPONT')
            ->setEmail('j-dupont@test.fr')
            ->setBirthDate(new DateTime('2000-01-01'))
            ->setRoles(['ROLE_PASSENGER'])
            ->setPassword('123456789')
            ->setCreatedAt(new \DateTimeImmutable('now'));

        $reservation = new Reservation();
        $reservation->setNumberFlightSeat('2A')
            ->setPassengerNameRecord('FR4FGT')
            ->setPrice('21.4')
            ->setCreatedAt(new \DateTimeImmutable('now'));

        // Agir
        $passenger->addReservation($reservation);

        $passenger->removeReservation($reservation);

        // Vérifier
        $reservations = $passenger->getReservations();
        $this->assertFalse($passenger->getReservations()->contains($reservation));
        $this->assertCount(0, $reservations);
    }

    #[Test]
    public function it_disallow_two_reservations_for_a_same_passenger_in_a_flight(): void
    {
        // Arrange
        $passenger = new Passenger();

        $reservation1 = new Reservation();
        $reservation1->setNumberFlightSeat('2B')
            ->setPassengerNameRecord('FR4FGT')
            ->setPrice('29.40')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setPassenger($passenger);

        $reservation2 = new Reservation();
        $reservation2->setNumberFlightSeat('2T')
            ->setPassengerNameRecord('ABCDEF')
            ->setPrice('30.40')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setPassenger($passenger);

        $flight = new Flight();

        // Act : la première réservation est acceptée
        $flight->addReservation($reservation1);

        // Assert : la seconde doit être refusée
        $this->expectException(\DomainException::class);


        $flight->addReservation($reservation2);
    }
}
