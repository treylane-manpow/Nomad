<?php

namespace App\Repository;

use App\Entity\Duplicate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Duplicate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Duplicate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Duplicate[]    findAll()
 * @method Duplicate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DuplicateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Duplicate::class);
    }

    // /**
    //  * @return Duplicate[] Returns an array of Duplicate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Duplicate
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
