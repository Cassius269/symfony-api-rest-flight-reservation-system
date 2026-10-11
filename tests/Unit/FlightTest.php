<?php

namespace App\Tests\Unit;

use App\Entity\Copilot;
use App\Entity\Flight;
use App\Entity\Stop;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FlightTest extends TestCase
{
    #[Test]
    public function it_creates_a_flight_with_given_values(): void
    {
        // Arranger
        $now = new \DateTimeImmutable('now');
        $price = '500.50';
        $isDirect = true;
        $isCanceled = false;
        $isLate = true;
        $dateDeparture = new \DateTimeImmutable('2026-10-10 10:10:10');
        $dateArrival = new \DateTimeImmutable('2026-10-10 20:10:10');

        // Agir
        $flight = new Flight();
        $flight->setDateDeparture($dateDeparture)
            ->setDateArrival($dateArrival)
            ->setPrice($price)
            ->setIsCanceled($isCanceled)
            ->setIsDirect($isDirect)
            ->setIsLate($isLate)
            ->setCreatedAt($now);

        // Vérifier
        $this->assertSame($now, $flight->getCreatedAt());
        $this->assertSame($price, $flight->getPrice());

        $this->assertSame($dateDeparture, $flight->getDateDeparture());
        $this->assertSame($dateArrival, $flight->getDateArrival());

        $this->assertFalse($flight->isCanceled());
        $this->assertTrue($flight->isLate());
        $this->assertTrue($flight->isDirect());
    }

    #[Test]
    public function it_adds_many_copilots_to_a_flight(): void
    {
        $now = new \DateTimeImmutable('now');

        $flight = new Flight();
        $flight->setDateDeparture(new \DateTimeImmutable('2026-10-10 10:10:10'))
            ->setDateArrival(new \DateTimeImmutable('2026-10-10 20:10:10'))
            ->setPrice('505.5')
            ->setIsCanceled(false)
            ->setIsDirect(true)
            ->setIsLate(false)
            ->setCreatedAt($now);

        $copilot1 = new Copilot();
        $copilot1->setFirstname('Jean')
            ->setLastname('DUPONT')
            ->setEmail('jean-dupont@exemple.com')
            ->setPassword('12455&fezf')
            ->setCreatedAt($now);

        $copilot2 = new Copilot();
        $copilot2->setFirstname('Yves')
            ->setLastname('ROBERT')
            ->setEmail('y-robert@exemple.com')
            ->setPassword('4frV55&fefzf')
            ->setCreatedAt(new \DateTimeImmutable('now'));

        // Agir
        $flight->addCopilot($copilot1)
            ->addCopilot($copilot2);

        // Vérifier
        $this->assertCount(2, $flight->getCopilots());
        $this->assertContains($copilot1, $flight->getCopilots(), 'Copilote introuvable dans le vol');
        $this->assertContains($copilot2, $flight->getCopilots(), 'Copilote introuvable dans le vol');
    }

    #[Test]
    public function it_adds_one_or_several_stops_to_a_flight(): void
    {
        // Arranger
        $flight = new Flight();

        $stop1 = new Stop();
        $stop2 = new Stop();
        $stop3 = new Stop();

        // Agir
        $flight->addStop($stop1)
            ->addStop($stop2)
            ->addStop($stop3);

        // Vérifier
        $this->assertCount(3, $flight->getStops(), 'Nombre d\'escales incorrect');
        $this->assertContains($stop1, $flight->getStops());
        $this->assertContains($stop2, $flight->getStops());
        $this->assertContains($stop3, $flight->getStops());
    }
}
