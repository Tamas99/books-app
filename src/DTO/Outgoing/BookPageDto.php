<?php

namespace App\DTO\Outgoing;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class BookPageDto extends PageDto
{
    #[OA\Property(
        property: 'items',
        type: 'array',
        items: new OA\Items(ref: new Model(type: BookListDto::class)),
        nullable: true,
        description: 'Page items.'
    )]
    public array $items;
}
