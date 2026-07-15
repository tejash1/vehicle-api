<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractApiController extends AbstractController
{
    protected function success(mixed $data, int $status = 200): JsonResponse
    {
        return $this->json($data, $status);
    }

    protected function created(mixed $data): JsonResponse
    {
        return $this->json($data, 201);
    }

    protected function noContent(): JsonResponse
    {
        return $this->json(null, 204);
    }

    /**
     * Wraps a paginated repository result in a standard envelope.
     *
     * @param array{items: array<mixed>, total: int, page: int, limit: int, pages: int} $result
     * @param callable(mixed): array<string, mixed> $transformer Maps each item to an array
     */
    protected function paginated(array $result, callable $transformer): JsonResponse
    {
        return $this->json([
            'data' => array_map($transformer, $result['items']),
            'meta' => [
                'total' => $result['total'],
                'page'  => $result['page'],
                'limit' => $result['limit'],
                'pages' => $result['pages'],
            ],
        ]);
    }
}
