<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function listAuthorByEmail(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.email', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAuthorsByBookCountRange(int $min, int $max): array
    {
        $dql = "SELECT a FROM App\Entity\Author a 
                WHERE a.nb_books BETWEEN :min AND :max
                ORDER BY a.nb_books ASC";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('min', $min);
        $query->setParameter('max', $max);

        return $query->getResult();
    }


    public function deleteAuthorsWithNoBooks(): int
    {
        $dql = "DELETE FROM App\Entity\Author a WHERE a.nb_books = 0";
        $query = $this->_em->createQuery($dql);

        return $query->execute(); // Retourne le nombre d'auteurs supprimés
    }
    //    /**
    //     * @return Author[] Returns an array of Author objects
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

    //    public function findOneBySomeField($value): ?Author
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
