<?php

namespace App\DTO\Incoming;

use Symfony\Component\Validator\Constraints as Assert;

class BookUpdateDto
{
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 3, max: 64)]
    public ?string $title;

    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 3, max: 64)]
    public ?string $author;

    #[Assert\Type(type: 'string')]
    public ?string $isbn;

    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 3, max: 255)]
    public ?string $description;
}
