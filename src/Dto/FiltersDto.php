<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class FiltersDto
{
    public ?int $limit = null;

    #[Assert\Choice(
        choices: ['banner', 'popup'],
        message: 'size must be one of {{ choices }}',
    )]
    public ?string $size = null;
}
