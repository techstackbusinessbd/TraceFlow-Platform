<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TenantProvisioningTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a cloud SaaS tenant with encrypted DB credentials and UUID v7.
     */
    public function test_can_create_cloud_saas_tenant_with_database_details(): void
    {
        $tenant = Tenant::create([
            'client_name' => 'Ha-Meem Composite Holdings Ltd.',
            'client_slug' => 'hameem',
            'subdomain' => 'hameem.traceflow.app',
            'custom_domain' => 'erp.hameemgroup.com',
            'deployment_type' => 'CLOUD_SAAS',
            'db_host' => 'postgres',
            'db_port' => 5432,
            'db_name' => 'tf_tenant_hameem',
            'db_username' => 'tf_user',
            'db_password' => 'secret_db_pass_123',
            'is_active' => true,
        ]);

        $this->assertNotEmpty($tenant->id);
        $this->assertTrue(Str::isUuid($tenant->id));
        $this->assertEquals(7, (int) substr($tenant->id, 14, 1)); // UUID v7 check
        $this->assertEquals('hameem.traceflow.app', $tenant->subdomain);
        $this->assertEquals('tf_tenant_hameem', $tenant->db_name);

        // Verify encrypted casting works: Raw DB password should not be plain text
        $this->assertEquals('secret_db_pass_123', $tenant->db_password);
        $rawPassword = \DB::table('tenants')->where('id', $tenant->id)->value('db_password');
        $this->assertNotEquals('secret_db_pass_123', $rawPassword);
    }

    /**
     * Test creating an on-premises appliance tenant with hardware fingerprint.
     */
    public function test_can_create_on_premises_appliance_tenant(): void
    {
        $tenant = Tenant::create([
            'client_name' => 'Beximco Apparels Industrial Park',
            'client_slug' => 'beximco',
            'subdomain' => 'beximco.traceflow.app',
            'custom_domain' => null,
            'deployment_type' => 'ON_PREMISES_APPLIANCE',
            'db_host' => '192.168.10.50',
            'db_port' => 5432,
            'db_name' => 'tf_tenant_beximco',
            'db_username' => 'bex_dba',
            'db_password' => 'appliance_pass_456',
            'server_ip' => '192.168.10.50',
            'hardware_fingerprint' => 'BFEBFBFF000906EA-4C4C4544-001A2B3C4D5E',
            'is_active' => true,
        ]);

        $this->assertEquals('ON_PREMISES_APPLIANCE', $tenant->deployment_type);
        $this->assertEquals('192.168.10.50', $tenant->server_ip);
        $this->assertNotNull($tenant->hardware_fingerprint);
    }
}
