<?php

declare(strict_types=1);

namespace App\Domain\IAM\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Authentication Service (SRS_LOGIN & ADR-12)
 *
 * Handles credential verification by email or username,
 * Sanctum token issuance, and Dual Dashboard routing determination.
 */
class AuthService
{
    /**
     * Authenticate user with login identifier (email or username) and password.
     *
     * @return array{token: string, user: array<string, mixed>, company: array<string, mixed>|null, dashboard_target: string}
     *
     * @throws ValidationException
     */
    public function authenticate(string $login, string $password, ?string $deviceName = null): array
    {
        // 1. Find user by email OR username
        $user = User::with('company')
            ->where('email', $login)
            ->orWhere('username', $login)
            ->first();

        // 2. Validate user existence, active status, and password
        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['These credentials do not match our records.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'login' => ['Your account has been deactivated. Please contact your system administrator.'],
            ]);
        }

        // 3. Dual Dashboard routing target (SRS_LOGIN & ADR-12)
        // Platform owner or superadmin goes to /platform/command-center
        // Factory / client tenant users go to /app/dashboard
        $isPlatformHost = $user->is_platform_admin
            || ($user->company && $user->company->company_type === 'PLATFORM_HOST')
            || $user->hasRole('superadmin');

        $dashboardTarget = $isPlatformHost ? '/platform/command-center' : '/app/dashboard';

        // 4. Create Sanctum Bearer Token
        $tokenName = $deviceName ?? 'TraceFlow-Enterprise-Browser';
        $token = $user->createToken($tokenName)->plainTextToken;

        // 5. Structure payload matching SRS_LOGIN Section 3.1
        $roles = $user->roles->pluck('name')->toArray();
        if ($user->is_platform_admin && ! in_array('superadmin', $roles, true)) {
            $roles[] = 'superadmin';
        }

        $companyData = null;
        if ($user->company) {
            $companyData = [
                'id' => $user->company->id,
                'name' => $user->company->company_name,
                'code' => $user->company->company_code,
                'is_platform_host' => $user->company->company_type === 'PLATFORM_HOST',
            ];
        }

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'is_platform_admin' => $user->is_platform_admin,
                'roles' => $roles,
            ],
            'company' => $companyData,
            'dashboard_target' => $dashboardTarget,
        ];
    }
}
