<?php

namespace App\Repository;

use App\Entity\CommentFilm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentFilm|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentFilm|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentFilm[]    findAll()
 * @method CommentFilm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentFilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentFilm::class);
    }

    // /**
    //  * @return CommentFilm[] Returns an array of CommentFilm objects
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
    public function findOneBySomeField($value): ?CommentFilm
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
