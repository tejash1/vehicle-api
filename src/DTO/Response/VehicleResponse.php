<?php

declare(strict_types=1);

namespace App\DTO\Response;

use App\Entity\Vehicle;

final readonly class VehicleResponse
{
    private function __construct(
        public int $id,
        public int $vehicleModelId,
        public string $modelName,
        public int $brandId,
        public string $brandName,
        public int $year,
        public ?string $color,
        public ?string $price,
        public ?int $mileage,
        public string $status,
        public ?string $vin,
        public ?string $description,
        /** @var array<int, array<string, mixed>> */
        public array $images,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromEntity(Vehicle $vehicle, bool $withImages = false): self
    {
        $images = [];

        if ($withImages) {
            foreach ($vehicle->getImages() as $image) {
                $images[] = VehicleImageResponse::fromEntity($image)->toArray();
            }
        }

        return new self(
            id:             $vehicle->getId() ?? 0,
            vehicleModelId: $vehicle->getVehicleModel()?->getId() ?? 0,
            modelName:      $vehicle->getVehicleModel()?->getName() ?? '',
            brandId:        $vehicle->getVehicleModel()?->getBrand()?->getId() ?? 0,
            brandName:      $vehicle->getVehicleModel()?->getBrand()?->getName() ?? '',
            year:           $vehicle->getYear(),
            color:          $vehicle->getColor(),
            price:          $vehicle->getPrice(),
            mileage:        $vehicle->getMileage(),
            status:         $vehicle->getStatus()->value,
            vin:            $vehicle->getVin(),
            description:    $vehicle->getDescription(),
            images:         $images,
            createdAt:      $vehicle->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:      $vehicle->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'vehicle_model_id' => $this->vehicleModelId,
            'model_name'       => $this->modelName,
            'brand_id'         => $this->brandId,
            'brand_name'       => $this->brandName,
            'year'             => $this->year,
            'color'            => $this->color,
            'price'            => $this->price,
            'mileage'          => $this->mileage,
            'status'           => $this->status,
            'vin'              => $this->vin,
            'description'      => $this->description,
            'images'           => $this->images,
            'created_at'       => $this->createdAt,
            'updated_at'       => $this->updatedAt,
        ];
    }
}
