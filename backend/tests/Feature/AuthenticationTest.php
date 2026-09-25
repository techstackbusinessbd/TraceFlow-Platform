<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Services\PlatformBootstrapService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Authentication and Dual Dashboard Feature Test (SRS_LOGIN, ADR-11, ADR-12)
 *
 * Verifies email/username authentication, Sanctum token issuance,
 * and Dual Dashboard routing determination.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test platform superadmin login using username with /platform/command-center routing.
     */
    public function test_superadmin_can_login_via_username_and_routes_to_command_center(): void
    {
        // Bootstrap root company and internal accounts
        $bootstrap = new PlatformBootstrapService();
        $bootstrap->bootstrap();

        $response = $this->postJson('/api/v1/auth/login', [
            'login' => 'superadmin',
            'password' => 'TraceFlow@2026!Root',
            'device_name' => 'Automated-Test-Runner',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.username', 'superadmin')
            ->assertJsonPath('data.dashboard_target', '/platform/command-center')
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email', 'username', 'is_platform_admin'],
                    'company' => ['id', 'name', 'code', 'is_platform_host'],
                    'dashboard_target',
                ],
            ]);
    }

    /**
     * Test factory client user login using email with /app/dashboard routing.
     */
    public function test_factory_user_can_login_via_email_and_routes_to_app_dashboard(): void
    {
        $factory = Company::create([
            'company_name' => 'Fashion Tex Garments Ltd.',
            'company_code' => 'FTGL',
            'company_type' => 'CLIENT_TENANT',
        ]);

        $factoryUser = User::create([
            'name' => 'Tanvir Factory GM',
            'username' => 'tanvir.gm',
            'email' => 'tanvir.gm@fashiontex.com',
            'password' => Hash::make('Secret123!'),
            'company_id' => $factory->id,
            'is_platform_admin' => false,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'login' => 'tanvir.gm@fashiontex.com',
            'password' => 'Secret123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'tanvir.gm@fashiontex.com')
            ->assertJsonPath('data.company.code', 'FTGL')
            ->assertJsonPath('data.company.is_platform_host', false)
            ->assertJsonPath('data.dashboard_target', '/app/dashboard');
    }

    /**
     * Test login failure with invalid password returns 422 error.
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'login' => 'nonexistent.user@traceflow.internal',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['login']);
    }

    /**
     * Test /api/v1/auth/me returns current authenticated user profile.
     */
    public function test_authenticated_user_can_fetch_profile_via_me_endpoint(): void
    {
        $user = User::create([
            'name' => 'Operator User',
            'username' => 'operator01',
            'email' => 'operator@traceflow.internal',
            'password' => Hash::make('pass123'),
            'is_platform_admin' => true,
            'is_active' => true,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.username', 'operator01')
            ->assertJsonPath('data.dashboard_target', '/platform/command-center');
    }

    /**
     * Test logout revokes token.
     */
    public function test_authenticated_user_can_logout_and_revoke_token(): void
    {
        $user = User::create([
            'name' => 'Logout User',
            'username' => 'logout.user',
            'email' => 'logout@test.internal',
            'password' => Hash::make('pass123'),
            'is_active' => true,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $logoutResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Successfully logged out.');

        // Token record should be deleted from database
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);

        // Attempting to access /me with revoked token in fresh context should fail with 401
        $this->app['auth']->forgetGuards();
        $meResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(401);
    }
}
