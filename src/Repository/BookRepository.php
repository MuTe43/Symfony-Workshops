<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function searchBookByRef(string $ref): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.ref = :ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function booksListByAuthors(): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->addSelect('a')
            ->orderBy('a.username', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBooksBefore2023ByAuthorsWithMoreThan10Books(): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.author', 'a')
            ->addSelect('a')
            ->where('b.published = true')
            ->andWhere('b.publicationDate < :year')
            ->andWhere('a.nb_books > 10')
            ->setParameter('year', new \DateTime('2023-01-01'))
            ->orderBy('b.publicationDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function updateCategorySciFiToRomance(): int
    {
        return $this->createQueryBuilder('b')
            ->update()
            ->set('b.category', ':newCategory')
            ->where('b.category = :oldCategory')
            ->setParameter('newCategory', 'Romance')
            ->setParameter('oldCategory', 'Science-Fiction')
            ->getQuery()
            ->execute();
    }

    public function countRomanceBooks(): int
    {
        $dql = "SELECT COUNT(b) FROM App\Entity\Book b WHERE b.category = :category";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('category', 'Romance');

        return (int) $query->getSingleScalarResult();
    }

    public function findBooksBetweenDates(\DateTime $startDate, \DateTime $endDate): array
    {
        $dql = "SELECT b FROM App\Entity\Book b 
                WHERE b.publicationDate BETWEEN :start AND :end
                ORDER BY b.publicationDate ASC";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('start', $startDate);
        $query->setParameter('end', $endDate);

        return $query->getResult();
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
