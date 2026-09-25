<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Permission;
use App\Domain\Organization\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Multi-Company RBAC Test (ADR-08 & ADR-11)
 *
 * Verifies UUID v7 permissions/roles, company-scoped permissions,
 * and Superadmin Gate::before auto-bypass.
 */
class MultiCompanyRBACTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Test that permissions and roles are created with UUID v7.
     */
    public function test_roles_and_permissions_use_uuid_v7(): void
    {
        $permission = Permission::create([
            'name' => 'orders.create',
            'guard_name' => 'web',
        ]);

        $role = Role::create([
            'name' => 'merchandiser',
            'guard_name' => 'web',
        ]);

        $this->assertIsString($permission->id);
        $this->assertEquals(36, strlen($permission->id));
        $this->assertEquals(7, (int) $permission->id[14]);

        $this->assertIsString($role->id);
        $this->assertEquals(36, strlen($role->id));
        $this->assertEquals(7, (int) $role->id[14]);
    }

    /**
     * Test multi-company role isolation (User has role in Company A, but not in Company B).
     */
    public function test_multi_company_role_isolation(): void
    {
        $companyA = Company::create([
            'company_name' => 'Company Alpha',
            'company_code' => 'ALPHA-001',
            'company_type' => 'CLIENT_TENANT',
        ]);

        $companyB = Company::create([
            'company_name' => 'Company Beta',
            'company_code' => 'BETA-001',
            'company_type' => 'CLIENT_TENANT',
        ]);

        $user = User::create([
            'name' => 'Tariq Merchandiser',
            'email' => 'tariq@alpha.com',
            'password' => 'secret123',
            'company_id' => $companyA->id,
            'is_platform_admin' => false,
        ]);

        // Create roles scoped to companies
        $roleAlpha = Role::create([
            'company_id' => $companyA->id,
            'name' => 'factory_manager',
            'guard_name' => 'web',
        ]);

        $roleBeta = Role::create([
            'company_id' => $companyB->id,
            'name' => 'factory_manager',
            'guard_name' => 'web',
        ]);

        // Assign role in Company A scope
        setPermissionsTeamId($companyA->id);
        $user->assignRole($roleAlpha);

        // Verify user has role in Company A
        $this->assertTrue($user->hasRole('factory_manager'));

        // Switch to Company B scope and verify user does NOT have role
        setPermissionsTeamId($companyB->id);
        $user->unsetRelation('roles');
        $this->assertFalse($user->hasRole('factory_manager'));
    }

    /**
     * Test superadmin automatic permission bypass (ADR-11 Decision 02).
     */
    public function test_superadmin_automatically_bypasses_all_permission_gates(): void
    {
        $superadmin = User::create([
            'name' => 'System Superadmin',
            'email' => 'superadmin@traceflow.internal',
            'password' => 'secret123',
            'is_platform_admin' => true,
        ]);

        $regularUser = User::create([
            'name' => 'Regular Operator',
            'email' => 'operator@factory.com',
            'password' => 'secret123',
            'is_platform_admin' => false,
        ]);

        // Gate check for unseeded, arbitrary permissions
        $this->assertTrue(Gate::forUser($superadmin)->allows('any.future.permission.cutting.approve'));
        $this->assertTrue(Gate::forUser($superadmin)->allows('finance.audit.nuclear_override'));

        // Regular user should be denied
        $this->assertFalse(Gate::forUser($regularUser)->allows('any.future.permission.cutting.approve'));
    }
}
