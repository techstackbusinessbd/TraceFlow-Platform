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

    /**
     * Test that user supports username for login (SRS_LOGIN Section 2.3).
     */
    public function test_user_supports_username(): void
    {
        $user = User::create([
            'name' => 'TraceFlow Admin',
            'username' => 'tfadmin',
            'email' => 'admin@traceflow.internal',
            'password' => 'secret123',
            'is_platform_admin' => true,
        ]);

        $this->assertEquals('tfadmin', $user->username);
        $this->assertDatabaseHas('users', [
            'username' => 'tfadmin',
            'email' => 'admin@traceflow.internal',
        ]);
    }

    /**
     * Test that company can belong to a parent tenant control plane record.
     */
    public function test_company_belongs_to_tenant(): void
    {
        $tenant = \App\Domain\Tenant\Models\Tenant::create([
            'client_name' => 'Apex Holdings Group',
            'client_slug' => 'apex-holdings',
            'subdomain' => 'apex',
            'deployment_type' => 'CLOUD_SAAS',
            'db_name' => 'traceflow_client_apex',
            'db_username' => 'tf_apex',
            'db_password' => 'secret_pass_123',
        ]);

        $company = Company::create([
            'tenant_id' => $tenant->id,
            'company_name' => 'Apex Spinning Mills Ltd.',
            'company_code' => 'ASML-001',
            'company_type' => 'CLIENT_TENANT',
            'business_type' => 'Spinning',
            'currency' => 'BDT',
            'is_active' => true,
        ]);

        $this->assertEquals($tenant->id, $company->tenant->id);
        $this->assertCount(1, $tenant->companies);
        $this->assertEquals('ASML-001', $tenant->companies->first()->company_code);
    }
}
