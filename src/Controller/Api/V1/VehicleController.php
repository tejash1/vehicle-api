<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AbstractApiController;
use App\DTO\Request\VehicleCreateRequest;
use App\DTO\Request\VehicleFilterRequest;
use App\DTO\Request\VehicleUpdateRequest;
use App\DTO\Response\VehicleResponse;
use App\Entity\Vehicle;
use App\Service\VehicleService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/vehicles', name: 'api_v1_vehicle_')]
final class VehicleController extends AbstractApiController
{
    public function __construct(
        private readonly VehicleService $vehicleService,
    ) {}

    /**
     * GET /api/v1/vehicles
     *
     * Supported query params (all optional):
     *   ?page=1&limit=20
     *   &brand_id=1&model_id=5
     *   &status=available
     *   &year_from=2018&year_to=2024
     *   &price_from=10000&price_to=50000
     *   &sort_by=year&sort_dir=DESC
     */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        #[MapQueryString] VehicleFilterRequest $filters = new VehicleFilterRequest(),
    ): JsonResponse {
        $result = $this->vehicleService->findWithFilters($filters);

        return $this->paginated(
            $result,
            static fn(Vehicle $v): array => VehicleResponse::fromEntity($v)->toArray(),
        );
    }

    /** POST /api/v1/vehicles */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload] VehicleCreateRequest $dto): JsonResponse
    {
        $vehicle = $this->vehicleService->create($dto);

        return $this->created(VehicleResponse::fromEntity($vehicle, withImages: true)->toArray());
    }

    /** GET /api/v1/vehicles/{id} */
    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $vehicle = $this->vehicleService->findWithRelations($id);

        return $this->success(VehicleResponse::fromEntity($vehicle, withImages: true)->toArray());
    }

    /** PUT /api/v1/vehicles/{id} */
    #[Route('/{id}', name: 'update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(int $id, #[MapRequestPayload] VehicleUpdateRequest $dto): JsonResponse
    {
        $vehicle = $this->vehicleService->findOrFail($id);
        $vehicle = $this->vehicleService->update($vehicle, $dto);

        return $this->success(VehicleResponse::fromEntity($vehicle, withImages: true)->toArray());
    }

    /** DELETE /api/v1/vehicles/{id} */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        $vehicle = $this->vehicleService->findOrFail($id);
        $this->vehicleService->delete($vehicle);

        return $this->noContent();
    }
}
