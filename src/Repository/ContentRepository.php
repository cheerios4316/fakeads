<?php

namespace App\Repository;

use App\Entity\Content;
use App\Enums\CategoryEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ContentRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Content $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @return EntityRepository<Content>
     */
    public function repo(): EntityRepository
    {
        return $this->entityManager->getRepository(Content::class);
    }

    public function list(int $limit, CategoryEnum $category): array
    {
        $query = $this->repo()->createQueryBuilder('e');

        $query
            ->andWhere('e.size = :size')
            ->setParameter('size', $category)
        ;

        return $query->getQuery()->getResult();
    }

    public function listRandom(int $limit, ?CategoryEnum $size): array
    {
        $query = $this->repo()->createQueryBuilder('e');

        $query
            ->orderBy('RANDOM()')
            ->setMaxResults($limit);

        if (null !== $size) {
            $query
                ->andWhere('e.size = :size')
                ->setParameter('size', $size);
        }

        /** @var array<Content> $result */
        $result = $query->getQuery()->getResult();

        return $result;
    }

    public function getRandomElement(?CategoryEnum $size = null): ?Content
    {
        return $this->listRandom(1, $size)[0] ?? null;
    }
}
