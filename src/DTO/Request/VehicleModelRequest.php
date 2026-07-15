<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class VehicleModelRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Positive]
        public int $brandId = 0,

        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 100)]
        public string $name = '',
    ) {}
}
