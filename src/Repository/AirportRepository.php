<?php

namespace App\Repository;

use App\Entity\Airport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Airport>
 */
class AirportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Airport::class);
    }

//    /**
//     * @return Airport[] Returns an array of Airport objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Airport
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findDestinationByCityAndCountry(string $airportName, string $countryName): ?Airport
    {
        return $this->createQueryBuilder('a') // alias de la table City
            // ->select('c.city', 'country.name')
            ->innerJoin('c.city', 'city') // alias de la table city
            ->where('c.name = :cityName')
            ->andWhere('country.name = :countryName')
            ->setParameter('airportName', $airportName)
            ->setParameter('countryName', $countryName)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
