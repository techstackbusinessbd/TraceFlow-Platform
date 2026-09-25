<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Organization\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantUserRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that user can belong to a company/tenant.
     */
    public function test_user_belongs_to_company_tenant(): void
    {
        $company = Company::create([
            'company_name' => 'TraceFlow Knitwear Ltd.',
            'company_code' => 'TFKL-001',
            'company_type' => 'CLIENT_TENANT',
            'business_type' => 'Knit',
            'currency' => 'USD',
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => 'Karim Factory Admin',
            'email' => 'karim@tfkl.com',
            'password' => 'secret123',
            'company_id' => $company->id,
            'is_platform_admin' => false,
            'is_active' => true,
        ]);

        $this->assertEquals($company->id, $user->company->id);
        $this->assertEquals('TFKL-001', $user->company->company_code);
        $this->assertFalse($user->is_platform_admin);
        $this->assertCount(1, $company->users);
    }

    /**
     * Test that platform superadmin can have null company_id.
     */
    public function test_platform_superadmin_can_have_null_company(): void
    {
        $superadmin = User::create([
            'name' => 'Platform Superadmin',
            'email' => 'superadmin@traceflow.io',
            'password' => 'rootpass123',
            'company_id' => null,
            'is_platform_admin' => true,
            'is_active' => true,
        ]);

        $this->assertNull($superadmin->company_id);
        $this->assertNull($superadmin->company);
        $this->assertTrue($superadmin->is_platform_admin);
    }
}
