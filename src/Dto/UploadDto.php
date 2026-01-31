<?php

namespace App\Dto;

use App\Enums\SizeEnum;
use Symfony\Component\Validator\Constraints as Assert;

final class UploadDto
{
    public ?string $clickout = null;

    #[Assert\Choice(
        choices: ['banner', 'popup'],
        message: 'size must be one of {{ choices }}',
    )]
    public string $size = SizeEnum::BANNER->value;

    public ?string $description = null;
}
