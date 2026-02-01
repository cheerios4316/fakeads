<?php

namespace App\Service;

use App\Constants\Defaults;
use App\Dto\FiltersDto;
use App\Entity\Content;
use App\Enums\SizeEnum;
use App\Exception\FileNotFoundException;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Uid\Uuid;

class ContentService
{
    public function __construct(
        private readonly ContentRepository $repository,
        private readonly UploadService $uploadService,
        private readonly RequestStack $requestStack,
        private readonly KernelInterface $kernel,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array<Content>
     */
    public function getRandomList(?FiltersDto $filters = null): array
    {
        $limit = $filters->limit ?? Defaults::LIMIT;
        $size = $filters?->size ? SizeEnum::from($filters?->size) : null;

        return $this->repository->listRandom($limit, $size);
    }

    public function getRandomElement(): ?Content
    {
        return $this->repository->getRandomElement();
    }

    public function getRandomBanner(): ?Content
    {
        return $this->repository->getRandomElement(SizeEnum::BANNER);
    }

    public function getRandomPopup(): ?Content
    {
        return $this->repository->getRandomElement(SizeEnum::POPUP);
    }

    public function upload(
        UploadedFile $file,
        ?string $clickout = null,
        SizeEnum $size = SizeEnum::BANNER,
        ?string $description = null,
    ): Content {
        $saved = $this->uploadService->saveToFilesystem($file);
        $uuid = $saved->uuid;

        $baseUrl = $this->requestStack->getCurrentRequest()?->getSchemeAndHttpHost() ?? '';

        $entity = new Content(
            id: $uuid,
            url: $baseUrl . '/file/' . $uuid,
            fileName: $saved->name,
            clickout: $clickout,
            size: $size,
            description: $description
        );

        $this->repository->save($entity);

        return $entity;
    }

    public function getFilePathByUuid(string $uuid): ?string
    {
        $repository = $this->entityManager->getRepository(Content::class);
        $file = $repository->find(Uuid::fromString($uuid));

        if (!$file) {
            throw new FileNotFoundException($uuid);
        }

        return $this->getFilePathByEntity($file);
    }

    public function getFilePathByEntity(Content $file): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            rtrim($this->kernel->getProjectDir(), DIRECTORY_SEPARATOR),
            'public/uploads',
            $file->getFileName(),
        ]);
    }
}
