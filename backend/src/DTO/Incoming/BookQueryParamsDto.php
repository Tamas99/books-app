<?php

namespace App\DTO\Incoming;

use Symfony\Component\Validator\Constraints as Assert;

class BookQueryParamsDto extends PaginationDto
{
    #[Assert\Choice(choices: ['publication_time'])]
    public ?string $sort_by = 'publication_time';

    #[Assert\Choice(choices: ['asc', 'desc'])]
    public ?string $sort_order = 'desc';
}
