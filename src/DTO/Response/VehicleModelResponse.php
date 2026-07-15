<?php

declare(strict_types=1);

namespace App\DTO\Response;

use App\Entity\VehicleModel;

final readonly class VehicleModelResponse
{
    private function __construct(
        public int $id,
        public int $brandId,
        public string $brandName,
        public string $name,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromEntity(VehicleModel $model): self
    {
        return new self(
            id:        $model->getId() ?? 0,
            brandId:   $model->getBrand()?->getId() ?? 0,
            brandName: $model->getBrand()?->getName() ?? '',
            name:      $model->getName(),
            createdAt: $model->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $model->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'brand_id'   => $this->brandId,
            'brand_name' => $this->brandName,
            'name'       => $this->name,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
