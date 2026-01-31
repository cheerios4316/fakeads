<?php

namespace App\Repository;

use App\Entity\Content;
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
}
