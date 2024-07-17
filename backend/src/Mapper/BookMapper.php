<?php

namespace App\Mapper;

use App\DTO\Incoming\BookCreationDto;
use App\DTO\Outgoing\BookDetailsDto;
use App\DTO\Outgoing\BookListDto;
use App\DTO\Outgoing\BookPageDto;
use App\Entity\Book;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

class BookMapper
{
    public function mapBooksToBookPageDto(Paginator $paginator, int $page, int $pageSize): BookPageDto
    {
        $booksTotalResults = count($paginator);
        $booksCount = ceil(count($paginator) / $pageSize);
        $books = $paginator->getQuery()->getResult();

        $bookListDtos = $this->mapBooksToBookListDtos($books);

        $bookPageDto = new BookPageDto();
        $bookPageDto->total_results = $booksTotalResults;
        $bookPageDto->total_pages = $booksCount;
        $bookPageDto->current_page = $page;
        $bookPageDto->items = $bookListDtos;

        return $bookPageDto;
    }

    /**
     * @return BookListDto[] Returns an array of BookListDto objects
     */
    public function mapBooksToBookListDtos(array $books): array
    {
        return array_map([$this, 'mapBookToBookListDto'], $books);
    }

    public function mapBookToBookListDto(Book $book): BookListDto
    {
        $bookListDto = new BookListDto();
        $bookListDto->id = $book->getId();
        $bookListDto->title = $book->getTitle();
        $bookListDto->author = $book->getAuthor();
        $bookListDto->isbn = $book->getIsbn();

        return $bookListDto;
    }

    /**
     * @return BookDetailsDto[] Returns an array of BookDetailsDto objects
     */
    public function mapBooksToBookDetailsDtos(array $books): array
    {
        return array_map([$this, 'mapBookToBookDetailsDto'], $books);
    }

    public function mapBookToBookDetailsDto(Book $book): BookDetailsDto
    {
        $bookDetailsDto = new BookDetailsDto();
        $bookDetailsDto->id = $book->getId();
        $bookDetailsDto->title = $book->getTitle();
        $bookDetailsDto->author = $book->getAuthor();
        $bookDetailsDto->isbn = $book->getIsbn();
        $bookDetailsDto->description = $book->getDescription();

        return $bookDetailsDto;
    }

    public function mapBookCreationDtoToBook(BookCreationDto $bookCreationDto): Book
    {
        $book = new Book();
        $book->setTitle($bookCreationDto->title);
        $book->setAuthor($bookCreationDto->author);
        $book->setCreatedDate(new DateTime($bookCreationDto->createdDate));
        $book->setIsbn($bookCreationDto->isbn);
        $book->setDescription($bookCreationDto->description);

        return $book;
    }
}
