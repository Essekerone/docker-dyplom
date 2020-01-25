<?php

namespace App\Repository;

use App\Entity\Joker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Joker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Joker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Joker[]    findAll()
 * @method Joker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JokerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Joker::class);
    }

    // /**
    //  * @return Joker[] Returns an array of Joker objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Joker
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
