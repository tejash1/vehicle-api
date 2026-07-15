<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Controller\Api\AbstractApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/auth', name: 'api_v1_auth_')]
final class AuthController extends AbstractApiController
{
    /**
     * Login endpoint — placeholder for JWT authentication.
     *
     * Phase X will implement:
     *   - User entity + password hashing
     *   - lexik/jwt-authentication-bundle
     *   - Return {"token": "...", "expires_at": "..."}
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        return $this->json([
            'message' => 'JWT authentication not yet implemented.',
        ], 501);
    }
}
