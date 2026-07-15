<?php

declare(strict_types=1);

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Query-string parameters for GET /api/v1/vehicles.
 *
 * The camelCase_to_snake_case serializer name converter maps:
 *   ?brand_id=1      -> $brandId
 *   ?year_from=2020  -> $yearFrom
 *   ?sort_by=year    -> $sortBy
 */
final readonly class VehicleFilterRequest
{
    public function __construct(
        #[Assert\Positive]
        public int $page = 1,

        #[Assert\Range(min: 1, max: 100)]
        public int $limit = 20,

        public ?int $brandId = null,

        public ?int $modelId = null,

        public ?string $status = null,

        #[Assert\Range(min: 1886, max: 2030)]
        public ?int $yearFrom = null,

        #[Assert\Range(min: 1886, max: 2030)]
        public ?int $yearTo = null,

        public ?string $priceFrom = null,

        public ?string $priceTo = null,

        public string $sortBy = 'createdAt',

        public string $sortDir = 'DESC',
    ) {}
}
