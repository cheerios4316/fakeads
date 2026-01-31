<?php

namespace App\Service;

use App\Dto\SavedDto;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Uid\Uuid;

class UploadService
{
    public function __construct(protected readonly KernelInterface $kernel)
    {
    }

    public function saveToFilesystem(UploadedFile $file): SavedDto
    {
        $extension = $file->guessExtension();
        $uuid = Uuid::v7();

        $targetName = $uuid.'.'.$extension;

        $targetDir = $this->kernel->getProjectDir().'/public/uploads';
        $file->move($targetDir, $targetName);

        return new SavedDto(
            uuid: $uuid,
            name: $targetName,
        );
    }
}
