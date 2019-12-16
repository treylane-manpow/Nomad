<?php

namespace App\Repository;

use App\Entity\LeadSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LeadSource|null find($id, $lockMode = null, $lockVersion = null)
 * @method LeadSource|null findOneBy(array $criteria, array $orderBy = null)
 * @method LeadSource[]    findAll()
 * @method LeadSource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeadSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeadSource::class);
    }

    // /**
    //  * @return LeadSource[] Returns an array of LeadSource objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LeadSource
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
