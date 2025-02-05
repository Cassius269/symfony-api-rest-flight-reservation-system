<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Country>
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    //    /**
    //     * @return Country[] Returns an array of Country objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Country
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // Requête personnalisée pour trouver un pays à l'aide de son nom
    public function findCountryByName(string $countryName)
    {
        return $this->createQueryBuilder('c')
            ->select(['c.id', 'c.name']) // nettoyer la récupération pour qu'il y ait uniquement l'ID et le nom du pays une fois trouvé
            ->where('c.name = :countryName') // mise en place de la requête conditionnelle pour trier le résultat
            ->setParameter('countryName', $countryName) // définition du marqueur nommé "countryName" pour éviter des injections SQL
            ->getQuery() // écrire la requête
            ->getOneOrNullResult(); // renvoyer le seul résultat trouvé sous format de tableau associatif ou bien null si aucun résultat trouvé
    }
}
