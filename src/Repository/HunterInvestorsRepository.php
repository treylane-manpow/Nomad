<?php

namespace App\Repository;

use App\Entity\HunterInvestors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HunterInvestors|null find($id, $lockMode = null, $lockVersion = null)
 * @method HunterInvestors|null findOneBy(array $criteria, array $orderBy = null)
 * @method HunterInvestors[]    findAll()
 * @method HunterInvestors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HunterInvestorsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HunterInvestors::class);
    }

    // /**
    //  * @return HunterInvestors[] Returns an array of HunterInvestors objects
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
    public function findOneBySomeField($value): ?HunterInvestors
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
