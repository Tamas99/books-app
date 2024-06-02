<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    private readonly EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
        $this->entityManager = $registry->getManager();
    }

    public function findAllBook(int $page, int $limit, string $sortBy, string $orderBy): Paginator
    {
        $qb = $this->createQueryBuilder('b');

        switch ($sortBy) {
            case 'created_date':
                $qb->orderBy('b.createdDate', $orderBy);

                break;
            default:
        }

        $qb->setFirstResult($limit * ($page - 1));
        $qb->setMaxResults($limit);

        return new Paginator($qb->getQuery());
    }

    public function create(Book $book): ?int {
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $book->getId();
    }

    public function update(Book $book): Book {
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $book;
    }

    public function delete(Book $book): void
    {
        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }
}
