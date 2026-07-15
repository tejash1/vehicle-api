<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class VehicleCreateRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public int $vehicleModelId = 0,

        #[Assert\NotBlank]
        #[Assert\Range(min: 1886, max: 2030)]
        public int $year = 0,

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
