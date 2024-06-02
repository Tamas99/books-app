<?php

namespace App\DTO\Outgoing;

use OpenApi\Attributes as OA;

abstract class PageDto
{
    #[OA\Property(
        property: 'total_pages',
        type: 'integer',
        nullable: false,
        description: 'The number of pages.'
    )]
    public int $total_pages;

    #[OA\Property(
        property: 'current_page',
        type: 'integer',
        nullable: false,
        description: 'The current pages.'
    )]
    public int $current_page;

    #[OA\Property(
        property: 'total_results',
        type: 'integer',
        nullable: false,
        description: 'Total results.'
    )]
    public int $total_results;
}
