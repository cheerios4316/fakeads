<?php

namespace App\Repository;

use App\Entity\Token;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TokenRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Token $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @return EntityRepository<Token>
     */
    public function repo(): EntityRepository
    {
        return $this->entityManager->getRepository(Token::class);
    }

    public function getByHash(string $hash): ?Token
    {
        return $this->repo()->find($hash);
    }
}
