<?php

namespace App\Repository;

use App\Entity\Flight;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Flight>
 */
class FlightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flight::class);
    }

    //    /**
    //     * @return Flight[] Returns an array of Flight objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Flight
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function searchFlightByCitiesAndDates(string $cityDeparture, string $cityArrival,  DateTime $dateDeparture, DateTime $dateArrival)
    {
        return $this->createQueryBuilder('f')
            ->where('f.cityDeparture.name = :cityDeparture')
            ->andWhere('f.cityArrival.name = :cityArrival')
            ->andWhere('f.dateDeparture = :dateDeparture')
            ->andWhere('f.dateArrival = :dateArrival');
    }

    public function findAvailableFlights(): ?array
    {
        return $this->createQueryBuilder('f')
            ->select('f.id, f.dateDeparture, f.dateArrival')
            ->addSelect('cd.name AS cityDeparture, ca.name AS cityArrival')
            ->addSelect('am.capacity')
            ->addSelect('COUNT(r.passenger) AS passengerCount')
            ->innerJoin('f.airplane', 'a')
            ->innerJoin('a.airplaneModel', 'am')
            ->leftJoin('f.reservations', 'r')
            ->leftJoin('f.cityDeparture', 'cd')
            ->leftJoin('f.cityArrival', 'ca')
            ->where('f.dateDeparture > :now')
            ->setParameter('now', new \DateTime())
            ->groupBy('f.id, f.dateDeparture, f.dateArrival, cd.name, ca.name, am.capacity') // Include am.capacity
            ->having('COUNT(r.passenger) < am.capacity') // Compare with the correct capacity
            ->getQuery()
            ->getResult();
    }


    // Compter le nombre de vols occupés pendant la période d'un vol par le commandant de bord
    public function countOverlappingFlightsForCaptain(int $captainId, DateTime $start, DateTime $end): int
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->where('f.captain = :captainId')
            ->andWhere('
                (:start BETWEEN f.dateDeparture AND f.dateArrival) OR
                (:end BETWEEN f.dateDeparture AND f.dateArrival) OR
                (f.dateDeparture BETWEEN :start AND :end) OR
                (f.dateDeparture <= :start AND f.dateArrival >= :end)
            ')
            ->setParameter('captainId', $captainId)
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Compter le nombre de vols occupés pendant la période d'un vol par un copilote
    public function countOverlappingFlightsForCopilot(int $copilotId, DateTime $start, DateTime $end): int
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->join('f.copilots', 'c')
            ->where('c.id = :copilotId')
            ->andWhere('
                    (:start BETWEEN f.dateDeparture AND f.dateArrival) OR
                    (:end BETWEEN f.dateDeparture AND f.dateArrival) OR
                    (f.dateDeparture BETWEEN :start AND :end) OR
                    (f.dateDeparture <= :start AND f.dateArrival >= :end)
                ')
            ->setParameter('copilotId', $copilotId)
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Compter le nombre de vols occupés à l'avenir par un commandant de bord
    public function countNextFlightsForCaptain(int $captainId)
    {
        return $this->createQueryBuilder('f')
            ->select('COUNT(f.id)')
            ->join('f.captain', 'c')
            ->where('c.id = :captainId')
            ->andWhere('f.dateDeparture > :currentDate')
            ->setParameter('captainId', $captainId)
            ->setParameter('currentDate', (new DateTime())->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
