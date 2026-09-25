<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Services\PlatformBootstrapService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Platform Bootstrap Test (ADR-11)
 *
 * Verifies idempotent provisioning of root platform host company and engineering accounts.
 */
class PlatformBootstrapTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that platform bootstrap creates root company and exactly 7 engineering accounts.
     */
    public function test_platform_bootstrap_provisions_root_company_and_accounts(): void
    {
        $service = new PlatformBootstrapService();
        $result = $service->bootstrap();

        $this->assertEquals(PlatformBootstrapService::ROOT_COMPANY_CODE, $result['root_company']->company_code);
        $this->assertEquals('PLATFORM_HOST', $result['root_company']->company_type);
        $this->assertNull($result['root_company']->tenant_id);
        $this->assertEquals(7, $result['seeded_accounts']);

        // Verify accounts in database
        $this->assertDatabaseCount('companies', 1);
        $this->assertDatabaseCount('users', 7);

        $superadmin = User::where('email', 'superadmin@traceflow.internal')->first();
        $this->assertNotNull($superadmin);
        $this->assertEquals('superadmin', $superadmin->username);
        $this->assertTrue($superadmin->is_platform_admin);
        $this->assertEquals($result['root_company']->id, $superadmin->company_id);
        $this->assertTrue(Hash::check('TraceFlow@2026!Root', $superadmin->password));
    }

    /**
     * Test that platform bootstrap is strictly idempotent (running multiple times produces identical state).
     */
    public function test_platform_bootstrap_is_strictly_idempotent(): void
    {
        $service = new PlatformBootstrapService();

        // Run once
        $service->bootstrap();
        $this->assertDatabaseCount('companies', 1);
        $this->assertDatabaseCount('users', 7);

        // Run second time
        $service->bootstrap();
        $this->assertDatabaseCount('companies', 1);
        $this->assertDatabaseCount('users', 7);

        // Run third time
        $service->bootstrap();
        $this->assertDatabaseCount('companies', 1);
        $this->assertDatabaseCount('users', 7);
    }
}
