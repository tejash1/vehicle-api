<?php

declare(strict_types=1);

namespace App\DTO\Response;

use App\Entity\Brand;

final readonly class BrandResponse
{
    private function __construct(
        public int $id,
        public string $name,
        public ?string $country,
        public ?string $logoFilename,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromEntity(Brand $brand): self
    {
        return new self(
            id:            $brand->getId() ?? 0,
            name:          $brand->getName(),
            country:       $brand->getCountry(),
            logoFilename:  $brand->getLogoFilename(),
            createdAt:     $brand->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:     $brand->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'country'       => $this->country,
            'logo_filename' => $this->logoFilename,
            'created_at'    => $this->createdAt,
            'updated_at'    => $this->updatedAt,
        ];
    }
}
