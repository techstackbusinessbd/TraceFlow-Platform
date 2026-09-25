<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Domain\IAM\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authentication Controller (SRS_LOGIN)
 *
 * Provides API endpoints for user login, current user session retrieval, and token logout.
 */
class AuthController extends Controller
{
    /**
     * Authenticate user and return token with dual dashboard target.
     *
     * POST /api/v1/auth/login
     */
    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $login = $request->getLoginIdentifier();
        $password = (string) $request->input('password');
        $deviceName = $request->input('device_name');

        $result = $authService->authenticate($login, $password, $deviceName);

        return response()->json([
            'success' => true,
            'message' => 'Authentication successful.',
            'data' => $result,
        ], Response::HTTP_OK);
    }

    /**
     * Get the authenticated user's profile and active context.
     *
     * GET /api/v1/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user()->load('company');

        $roles = $user->roles->pluck('name')->toArray();
        if ($user->is_platform_admin && ! in_array('superadmin', $roles, true)) {
            $roles[] = 'superadmin';
        }

        $isPlatformHost = $user->is_platform_admin
            || ($user->company && $user->company->company_type === 'PLATFORM_HOST')
            || in_array('superadmin', $roles, true);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'is_platform_admin' => $user->is_platform_admin,
                    'roles' => $roles,
                ],
                'company' => $user->company ? [
                    'id' => $user->company->id,
                    'name' => $user->company->company_name,
                    'code' => $user->company->company_code,
                    'is_platform_host' => $user->company->company_type === 'PLATFORM_HOST',
                ] : null,
                'dashboard_target' => $isPlatformHost ? '/platform/command-center' : '/app/dashboard',
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Revoke current access token (Logout).
     *
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Revoke the token that was used to authenticate the current request
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.',
        ], Response::HTTP_OK);
    }
}
