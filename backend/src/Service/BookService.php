<?php

namespace App\Service;

use App\DTO\Incoming\BookCreationDto;
use App\DTO\Incoming\BookQueryParamsDto;
use App\DTO\Incoming\BookUpdateDto;
use App\DTO\Outgoing\BookDetailsDto;
use App\DTO\Outgoing\BookPageDto;
use App\Mapper\BookMapper;
use App\Repository\BookRepository;
use Exception;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly BookMapper $bookMapper,
    ) {
    }
    
    public function findAll(BookQueryParamsDto $params): BookPageDto
    {
        $paginator = $this->bookRepository->findAllBook($params->page, $params->page_size, $params->sort_by, $params->sort_order);
        $bookPageDto = $this->bookMapper->mapBooksToBookPageDto($paginator, $params->page, $params->page_size);

        return $bookPageDto;
    }

    public function findOneById(int $id): BookDetailsDto
    {
        $book = $this->bookRepository->findOneBy(['id' => $id]);
        if(!$book) {
            throw new NotFoundHttpException("Book not found with ID: $id");
        }

        $bookDetailsDto = $this->bookMapper->mapBookToBookDetailsDto($book);
        return $bookDetailsDto;
    }

    public function create(BookCreationDto $bookCreationDto): int
    {
        $book = $this->bookMapper->mapBookCreationDtoToBook($bookCreationDto);
        $createdBookId = $this->bookRepository->create($book);
        if (!$createdBookId) {
            throw new Exception('Book Couldn\'t be created');
        }

        return $createdBookId;
    }

    public function update(int $id, BookUpdateDto $bookUpdateDto): BookDetailsDto
    {
        $bookToUpdate = $this->bookRepository->findOneBy(['id' => $id]);
    
        if (!$bookToUpdate) {
            throw new NotFoundHttpException("Book not found with ID: $id");
        }
    
        $reflectionClass = new ReflectionClass($bookToUpdate);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $setterName = 'set' . ucfirst($propertyName);
            
            // Check if the update DTO has a value for this property
            if (isset($bookUpdateDto->$propertyName) && $bookUpdateDto->$propertyName !== null) {
                $setter = $reflectionClass->getMethod($setterName);
                $setter->invoke($bookToUpdate, $bookUpdateDto->$propertyName);
            }
        }

        $updatedBook = $this->bookRepository->update($bookToUpdate);
        $updatedBookDetailsDto = $this->bookMapper->mapBookToBookDetailsDto($updatedBook);

        return $updatedBookDetailsDto;
    }

    public function deleteOneById(int $id): void
    {
        $bookToDelete = $this->bookRepository->findOneBy(['id' => $id]);
    
        if (!$bookToDelete) {
            throw new NotFoundHttpException("Book not found with ID: $id");
        }
        
        $this->bookRepository->delete($bookToDelete);
    }
}
