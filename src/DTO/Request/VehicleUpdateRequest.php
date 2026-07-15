<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * All fields are optional — only non-null values are applied to the entity.
 * This gives PUT the ergonomics of PATCH for partial updates.
 */
final readonly class VehicleUpdateRequest
{
    public function __construct(
        #[Assert\Positive]
        public ?int $vehicleModelId = null,

        #[Assert\Range(min: 1886, max: 2030)]
        public ?int $year = null,

        #[Assert\Length(max: 50)]
        public ?string $color = null,

        #[Assert\PositiveOrZero]
        public ?string $price = null,

        #[Assert\PositiveOrZero]
        public ?int $mileage = null,

        public ?string $status = null,

        #[Assert\Length(exactly: 17)]
        public ?string $vin = null,

        public ?string $description = null,
    ) {}
}
