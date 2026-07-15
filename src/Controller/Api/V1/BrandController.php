<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AbstractApiController;
use App\DTO\Request\BrandRequest;
use App\DTO\Response\BrandResponse;
use App\DTO\Response\VehicleModelResponse;
use App\Entity\Brand;
use App\Entity\VehicleModel;
use App\Service\BrandService;
use App\Service\VehicleModelService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/brands', name: 'api_v1_brand_')]
final class BrandController extends AbstractApiController
{
    public function __construct(
        private readonly BrandService $brandService,
    ) {}

    /** GET /api/v1/brands */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $brands = $this->brandService->findAll();

        return $this->success(array_map(
            static fn(Brand $b): array => BrandResponse::fromEntity($b)->toArray(),
            $brands,
        ));
    }

    /** POST /api/v1/brands */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload] BrandRequest $dto): JsonResponse
    {
        $brand = $this->brandService->create($dto);

        return $this->created(BrandResponse::fromEntity($brand)->toArray());
    }

    /** GET /api/v1/brands/{id} */
    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $brand = $this->brandService->findOrFail($id);

        return $this->success(BrandResponse::fromEntity($brand)->toArray());
    }

    /** PUT /api/v1/brands/{id} */
    #[Route('/{id}', name: 'update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(int $id, #[MapRequestPayload] BrandRequest $dto): JsonResponse
    {
        $brand = $this->brandService->findOrFail($id);
        $brand = $this->brandService->update($brand, $dto);

        return $this->success(BrandResponse::fromEntity($brand)->toArray());
    }

    /** DELETE /api/v1/brands/{id} */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        $brand = $this->brandService->findOrFail($id);
        $this->brandService->delete($brand);

        return $this->noContent();
    }

    /** GET /api/v1/brands/{id}/models */
    #[Route('/{id}/models', name: 'models', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function models(int $id, VehicleModelService $modelService): JsonResponse
    {
        $brand  = $this->brandService->findOrFail($id);
        $models = $modelService->findByBrand($brand);

        return $this->success(array_map(
            static fn(VehicleModel $m): array => VehicleModelResponse::fromEntity($m)->toArray(),
            $models,
        ));
    }
}
