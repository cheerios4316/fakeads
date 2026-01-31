<?php

namespace App\Dto;

use Symfony\Component\Uid\Uuid;

class SavedDto
{
    public function __construct(
        public readonly Uuid $uuid,
        public readonly string $name,
    ) {
    }
}
