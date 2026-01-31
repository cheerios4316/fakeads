<?php

namespace App\Service;

use App\Constants\Defaults;
use App\Dto\FiltersDto;
use App\Entity\Content;
use App\Enums\SizeEnum;
use App\Repository\ContentRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;

class ContentService
{
    public function __construct(
        private readonly ContentRepository $repository,
        private readonly UploadService $uploadService,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @return array<Content>
     */
    public function getRandom(?FiltersDto $filters = null): array
    {
        $limit = $filters->limit ?? Defaults::LIMIT;
        $query = $this->repository->repo()->createQueryBuilder('e');

        $query
            ->orderBy('RANDOM()')
            ->setMaxResults($limit)
        ;

        if (!is_null($filters?->size)) {
            $query
                ->andWhere('e.size = :size')
                ->setParameter('size', SizeEnum::from($filters->size))
            ;
        }

        /** @var array<Content> $result */
        $result = $query->getQuery()->getResult();

        return $result;
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
            url: $baseUrl.'/file/'.$uuid,
            fileName: $saved->name,
            clickout: $clickout,
            size: $size,
            description: $description
        );

        $this->repository->save($entity);

        return $entity;
    }
}
