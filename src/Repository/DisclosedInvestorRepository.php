<?php

namespace App\Repository;

use App\Entity\DisclosedInvestor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DisclosedInvestor|null find($id, $lockMode = null, $lockVersion = null)
 * @method DisclosedInvestor|null findOneBy(array $criteria, array $orderBy = null)
 * @method DisclosedInvestor[]    findAll()
 * @method DisclosedInvestor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisclosedInvestorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisclosedInvestor::class);
    }

    // /**
    //  * @return DisclosedInvestor[] Returns an array of DisclosedInvestor objects
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
    public function findOneBySomeField($value): ?DisclosedInvestor
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
