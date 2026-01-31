<?php

namespace App\Entity;

use Doctrine\ORM\Mapping;

#[Mapping\Entity]
class Token
{
    public function __construct(
        /** @phpstan-ignore-next-line $token_hash is read by the ORM*/
        #[Mapping\Id]
        #[Mapping\Column(type: 'string', length: 255)]
        private string $token_hash,
        #[Mapping\Column(type: 'string', length: 255)]
        private string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
