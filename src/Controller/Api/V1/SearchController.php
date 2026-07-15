<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AbstractApiController;
use App\DTO\Response\VehicleResponse;
use App\Entity\Vehicle;
use App\Service\VehicleService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/search', name: 'api_v1_search_')]
final class SearchController extends AbstractApiController
{
    public function __construct(
        private readonly VehicleService $vehicleService,
    ) {}

    /**
     * GET /api/v1/search?q=toyota&page=1&limit=20
     *
     * Searches across vehicle VIN, description, color, model name, and brand name.
     * Phase X will replace this with OpenSearch / Meilisearch for full-text support.
     */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $term  = trim((string) $request->query->get('q', ''));
        $page  = max(1, (int) $request->query->get('page', 1));
        $limit = min(max(1, (int) $request->query->get('limit', 20)), 100);

        if ($term === '') {
            return $this->json([
                'data' => [],
                'meta' => ['total' => 0, 'page' => $page, 'limit' => $limit, 'pages' => 0],
            ]);
        }

        $result = $this->vehicleService->search($term, $page, $limit);

        return $this->paginated(
            $result,
            static fn(Vehicle $v): array => VehicleResponse::fromEntity($v)->toArray(),
        );
    }
}
