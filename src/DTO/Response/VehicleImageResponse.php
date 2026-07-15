<?php

declare(strict_types=1);

namespace App\DTO\Response;

use App\Entity\VehicleImage;

final readonly class VehicleImageResponse
{
    private function __construct(
        public int $id,
        public string $filename,
        public ?string $originalName,
        public ?string $mimeType,
        public ?int $size,
        public bool $isPrimary,
        public int $sortOrder,
        public string $createdAt,
    ) {}

    public static function fromEntity(VehicleImage $image): self
    {
        return new self(
            id:           $image->getId() ?? 0,
            filename:     $image->getFilename(),
            originalName: $image->getOriginalName(),
            mimeType:     $image->getMimeType(),
            size:         $image->getSize(),
            isPrimary:    $image->isPrimary(),
            sortOrder:    $image->getSortOrder(),
            createdAt:    $image->getCreatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'filename'      => $this->filename,
            'original_name' => $this->originalName,
            'mime_type'     => $this->mimeType,
            'size'          => $this->size,
            'is_primary'    => $this->isPrimary,
            'sort_order'    => $this->sortOrder,
            'created_at'    => $this->createdAt,
        ];
    }
}
