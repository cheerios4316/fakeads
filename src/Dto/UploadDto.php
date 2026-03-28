<?php

namespace App\Dto;

use App\Enums\CategoryEnum;
use Symfony\Component\Validator\Constraints as Assert;

final class UploadDto
{
    public ?string $clickout = null;

    #[Assert\Choice(
        choices: ['banner', 'popup', 'gemjak'],
        message: 'size must be one of {{ choices }}',
    )]
    public string $category = CategoryEnum::BANNER->value;

    public ?string $description = null;
}
