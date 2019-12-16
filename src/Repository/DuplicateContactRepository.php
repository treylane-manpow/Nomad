<?php

namespace App\Repository;

use App\Entity\DuplicateContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method DuplicateContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method DuplicateContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method DuplicateContact[]    findAll()
 * @method DuplicateContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DuplicateContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DuplicateContact::class);
    }

    // /**
    //  * @return DuplicateContact[] Returns an array of DuplicateContact objects
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
    public function findOneBySomeField($value): ?DuplicateContact
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
