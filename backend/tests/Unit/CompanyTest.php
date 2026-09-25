<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Organization\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a company generates a valid UUID v7 primary key.
     */
    public function test_company_can_be_created_with_uuid_v7(): void
    {
        $company = Company::create([
            'company_name' => 'Apex Apparels Ltd.',
            'company_code' => 'APEX-001',
            'business_type' => 'Woven',
            'currency' => 'USD',
            'is_active' => true,
        ]);

        $this->assertNotEmpty($company->id);
        $this->assertTrue(Str::isUuid($company->id));
        $this->assertEquals(7, (int) substr($company->id, 14, 1)); // UUID v7 check
        $this->assertEquals('Apex Apparels Ltd.', $company->company_name);
        $this->assertEquals('APEX-001', $company->company_code);
    }

    /**
     * Test company code must be unique.
     */
    public function test_company_code_must_be_unique(): void
    {
        Company::create([
            'company_name' => 'First Garments Ltd.',
            'company_code' => 'FGL-001',
            'business_type' => 'Knit',
            'currency' => 'USD',
            'is_active' => true,
        ]);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        Company::create([
            'company_name' => 'Duplicate Garments Ltd.',
            'company_code' => 'FGL-001',
            'business_type' => 'Knit',
            'currency' => 'USD',
            'is_active' => true,
        ]);
    }
}
