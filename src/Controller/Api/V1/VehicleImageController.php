<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AbstractApiController;
use App\Service\VehicleService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Vehicle image management.
 *
 * Phase X will implement:
 *   - symfony/http-foundation file upload handling
 *   - Storage abstraction (local filesystem → S3-compatible via Phase X)
 *   - Image resizing (imagine/imagine or league/glide)
 */
#[Route('/api/v1/vehicles/{vehicleId}/images', name: 'api_v1_vehicle_image_', requirements: ['vehicleId' => '\d+'])]
final class VehicleImageController extends AbstractApiController
{
    public function __construct(
        private readonly VehicleService $vehicleService,
    ) {}

    /**
     * POST /api/v1/vehicles/{vehicleId}/images
     * Upload an image for a vehicle (multipart/form-data).
     */
    #[Route('', name: 'upload', methods: ['POST'])]
    public function upload(int $vehicleId): JsonResponse
    {
        $this->vehicleService->findOrFail($vehicleId);

        return $this->json([
            'message' => 'Image upload not yet implemented.',
        ], 501);
    }

    /**
     * DELETE /api/v1/vehicles/{vehicleId}/images/{imageId}
     * Remove a specific image.
     */
    #[Route('/{imageId}', name: 'delete', methods: ['DELETE'], requirements: ['imageId' => '\d+'])]
    public function delete(int $vehicleId, int $imageId): JsonResponse
    {
        $this->vehicleService->findOrFail($vehicleId);

        return $this->json([
            'message' => 'Image deletion not yet implemented.',
        ], 501);
    }

    /**
     * PUT /api/v1/vehicles/{vehicleId}/images/{imageId}/primary
     * Mark an image as the primary thumbnail.
     */
    #[Route('/{imageId}/primary', name: 'set_primary', methods: ['PUT'], requirements: ['imageId' => '\d+'])]
    public function setPrimary(int $vehicleId, int $imageId): JsonResponse
    {
        $this->vehicleService->findOrFail($vehicleId);

        return $this->json([
            'message' => 'Set primary image not yet implemented.',
        ], 501);
    }
}
