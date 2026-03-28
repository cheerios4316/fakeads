<?php

namespace App\Dto;

use App\Enums\CategoryEnum;

class FileResponseDto
{
    public string $url;
    public ?string $clickout;
    public ?string $description;
    public CategoryEnum $size;
}
