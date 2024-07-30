<?php

namespace App\DTO\Incoming;

use Symfony\Component\Validator\Constraints as Assert;

class BookCreationDto
{
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 3, max: 64)]
    #[Assert\NotNull]
    public string $title;

    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 3, max: 64)]
    #[Assert\NotNull]
    public string $author;

    #[Assert\Type(type: 'string')]
    #[Assert\NotNull]
    public string $isbn;

    #[Assert\Type(type: 'string')]
    #[Assert\NotNull]
    #[Assert\DateTime(format: 'Y-m-d H:i:s')]
    public string $createdDate;

    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 3, max: 255)]
    #[Assert\NotNull]
    public string $description;
}
