<?php

namespace App\Dto;

use App\Enums\SizeEnum;

class FileResponseDto
{
    public string $url;
    public ?string $clickout;
    public ?string $description;
    public SizeEnum $size;
}