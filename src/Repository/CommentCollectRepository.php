<?php

namespace App\Repository;

use App\Entity\CommentCollect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentCollect|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentCollect|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentCollect[]    findAll()
 * @method CommentCollect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentCollectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentCollect::class);
    }

    // /**
    //  * @return CommentCollect[] Returns an array of CommentCollect objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommentCollect
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
