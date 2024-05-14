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

    public function findByIsbn(
        string $isbn
    ): ?Book {
        $result = $this->createQueryBuilder('b')
        ->andWhere('b.isbn = :val')
        ->setParameter('val', $isbn)
        ->setMaxResults(1)
        ->getQuery()
        ->getResult();
        if (is_array($result) && count($result) > 0) {
            return $result[0];
        }
        return null;
    }
}
