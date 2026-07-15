<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AbstractApiController;
use App\DTO\Request\VehicleModelRequest;
use App\DTO\Response\VehicleModelResponse;
use App\Entity\VehicleModel;
use App\Service\VehicleModelService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/models', name: 'api_v1_model_')]
final class VehicleModelController extends AbstractApiController
{
    public function __construct(
        private readonly VehicleModelService $modelService,
    ) {}

    /** GET /api/v1/models */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $models = $this->modelService->findAll();

        return $this->success(array_map(
            static fn(VehicleModel $m): array => VehicleModelResponse::fromEntity($m)->toArray(),
            $models,
        ));
    }

    /** POST /api/v1/models */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload] VehicleModelRequest $dto): JsonResponse
    {
        $model = $this->modelService->create($dto);

        return $this->created(VehicleModelResponse::fromEntity($model)->toArray());
    }

    /** GET /api/v1/models/{id} */
    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $model = $this->modelService->findOrFail($id);

        return $this->success(VehicleModelResponse::fromEntity($model)->toArray());
    }

    /** PUT /api/v1/models/{id} */
    #[Route('/{id}', name: 'update', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(int $id, #[MapRequestPayload] VehicleModelRequest $dto): JsonResponse
    {
        $model = $this->modelService->findOrFail($id);
        $model = $this->modelService->update($model, $dto);

        return $this->success(VehicleModelResponse::fromEntity($model)->toArray());
    }

    /** DELETE /api/v1/models/{id} */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        $model = $this->modelService->findOrFail($id);
        $this->modelService->delete($model);

        return $this->noContent();
    }
}
