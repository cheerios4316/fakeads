<?php

namespace App\Entity;

use App\Enums\CategoryEnum;
use Doctrine\ORM\Mapping;
use Symfony\Component\Uid\Uuid;

#[Mapping\Entity]
final class Content implements \JsonSerializable
{
    public function __construct(
        #[Mapping\Id]
        #[Mapping\Column(type: 'uuid')]
        private Uuid $id,
        #[Mapping\Column(type: 'string', length: 255)]
        private string $url,
        #[Mapping\Column(type: 'string', length: 255)]
        private string $fileName,
        #[Mapping\Column(type: 'string', length: 255, nullable: true)]
        private ?string $description = null,
        #[Mapping\Column(type: 'string', length: 255, nullable: true)]
        private ?string $clickout = null,
        #[Mapping\Column(type: 'string', length: 10, enumType: CategoryEnum::class)]
        private CategoryEnum $size = CategoryEnum::BANNER,
    ) {
    }

    /**
     * @return array{clickout: string|null, size: CategoryEnum, url: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'url' => $this->url,
            'clickout' => $this->clickout,
            'size' => $this->size,
            'description' => $this->description,
        ];
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
