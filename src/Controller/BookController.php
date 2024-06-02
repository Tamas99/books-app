<?php

namespace App\Controller;

use App\DTO\Incoming\BookCreationDto;
use App\DTO\Incoming\BookQueryParamsDto;
use App\DTO\Incoming\BookUpdateDto;
use App\DTO\Outgoing\BookDetailsDto;
use App\DTO\Outgoing\BookPageDto;
use App\Service\BookService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[OA\tag('books')]
#[Route('/api/v1/books')]
class BookController extends AbstractController
{
    public function __construct(
        private readonly BookService $bookService,
    ) {
    }

    #[OA\Get(
        summary: 'Returns a list of books',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(ref: new Model(type: BookPageDto::class))
            ),
        ]
    )]
    #[Route('', methods: ['GET'])]
    public function findAll(#[MapQueryString] BookQueryParamsDto $params): JsonResponse
    {
        $response = $this->bookService->findAll($params);

        return new JsonResponse($response);
    }

    #[OA\Get(
        summary: 'Returns the details of a book',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(ref: new Model(type: BookDetailsDto::class))
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request - Missing ID',
            ),
            new OA\Response(
                response: 404,
                description: 'The book was not found with the provided ID',
            ),
        ]
    )]
    #[Route('/{bookId}', methods: ['GET'])]
    public function findOne(int $bookId): JsonResponse
    {
        $response = $this->bookService->findOneById($bookId);

        return new JsonResponse($response);
    }

    #[OA\Post(
        summary: 'Creates a new book in the database',
        responses: [
            new OA\Response(
                response: 201,
                description: 'Successful operation',
                content: new OA\JsonContent()
            ),
        ]
    )]
    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] BookCreationDto $bookCreationDto)
    {
        $createdId = $this->bookService->create($bookCreationDto);

        return new Response(content: "Book with ID: $createdId was created.", status: Response::HTTP_CREATED);
    }

    #[OA\Put(
        summary: 'Updates a book by its ID',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Book updated successfully',
                content: new OA\JsonContent(ref: new Model(type: BookDetailsDto::class))
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request - Invalid update data or missing ID',
            ),
            new OA\Response(
                response: 404,
                description: 'The book was not found with the provided ID',
            ),
        ]
    )]
    #[Route('/{bookId}', methods: ['PUT'])]
    public function update(#[MapRequestPayload] BookUpdateDto $bookUpdateDto, int $bookId): JsonResponse
    {
        $updatedBookDetailsDto = $this->bookService->update($bookId, $bookUpdateDto);
    
        return new JsonResponse($updatedBookDetailsDto);
    }

    #[OA\Delete(
        summary: 'Deletes a book',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Book was deleted successfully',
                content: new OA\JsonContent()
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request - Missing ID',
            ),
            new OA\Response(
                response: 404,
                description: 'The book was not found with the provided ID',
            ),
        ]
    )]
    #[Route('/{bookId}', methods: ['DELETE'])]
    public function deleteOne(int $bookId): JsonResponse
    {
        $this->bookService->deleteOneById($bookId);

        return new JsonResponse("Book with ID: $bookId was deleted.");
    }
}
