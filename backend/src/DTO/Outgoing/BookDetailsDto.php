<?php

namespace App\DTO\Outgoing;

use OpenApi\Attributes as OA;

class BookDetailsDto
{
    #[OA\Property(
        property: 'id',
        type: 'integer',
        nullable: false,
        description: 'The unique identifier of the book.'
    )]
    public int $id;

    #[OA\Property(
        property: 'title',
        type: 'string',
        nullable: false,
        description: 'The title of the book.'
    )]
    public string $title;

    #[OA\Property(
        property: 'author',
        type: 'string',
        nullable: false,
        description: 'The author of the book.'
    )]
    public string $author;

    #[OA\Property(
        property: 'isbn',
        type: 'string',
        nullable: false,
        description: 'The isbn of the book.'
    )]
    public string $isbn;

    #[OA\Property(
        property: 'description',
        type: 'string',
        nullable: false,
        description: 'The description of the book.'
    )]
    public string $description;
}
