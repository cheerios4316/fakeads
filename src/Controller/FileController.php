<?php

namespace App\Controller;

use App\Entity\Content;
use App\Exception\FileNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

final class FileController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private KernelInterface $kernel,
    ) {
    }

    #[Route(path: '/file/{uuid}', methods: ['GET'])]
    public function file(
        string $uuid,
    ): BinaryFileResponse {
        $repository = $this->entityManager->getRepository(Content::class);
        $file = $repository->find(Uuid::fromString($uuid));

        if (!$file) {
            throw new FileNotFoundException($uuid);
        }

        $path = implode(DIRECTORY_SEPARATOR, [
            rtrim($this->kernel->getProjectDir(), DIRECTORY_SEPARATOR),
            'public/uploads',
            $file->getFileName(),
        ]);

        return new BinaryFileResponse(
            $path,
            200,
            ['Content-Type' => 'image/jpeg']
        );
    }
}
