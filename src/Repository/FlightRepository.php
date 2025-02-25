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

    public function findAvailableFlights()
    {
        return $this->createQueryBuilder('f')
            ->select('f.id , f.dateDeparture, f.dateArrival')
            ->addSelect('cd.name AS departure_city, ca.name AS arrival_city')
            // ->addSelect('a.id AS airplane_id, a.capacity')
            ->addSelect('COUNT(r.passenger) AS passenger_count')
            ->innerJoin('f.airplane', 'a')
            ->leftJoin('f.reservations', 'r')
            ->leftJoin('f.cityDeparture', 'cd') // Join to cityDeparture
            ->leftJoin('f.cityArrival', 'ca')   // Join to cityArrival
            ->where('f.dateDeparture > :now')
            ->setParameter('now', new \DateTime())
            ->groupBy('f.id, f.dateDeparture, f.dateArrival, cd.name, ca.name, a.id, a.capacity')
            ->getQuery()
            ->getResult();
    }
}
