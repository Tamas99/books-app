<?php

namespace App\DTO\Incoming;

use Symfony\Component\Validator\Constraints as Assert;

class PaginationDto
{
    #[Assert\Type(type: 'integer', )]
    #[Assert\Range(min: 1)]
    public ?int $page = 1;

    #[Assert\Type(type: 'integer', )]
    #[Assert\Range(min: 1)]
    public ?int $page_size = 10;
}
