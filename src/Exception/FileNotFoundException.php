<?php

namespace App\Exception;

class FileNotFoundException extends \InvalidArgumentException
{
    public function __construct(string $uuid)
    {
        parent::__construct('No file found for UUID '.$uuid, 404);
    }
}
