<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\DocumentSequence;
use App\Domain\Organization\Services\CodeGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Universal Code Generator Test (ADR-09 & Invariant-11)
 *
 * Verifies atomic sequence incrementation, token substitution,
 * and company-scoped document code generation.
 */
class CodeGeneratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test basic sequence generation with default format.
     */
    public function test_generates_sequential_codes(): void
    {
        $service = new CodeGeneratorService();

        $code1 = $service->generate('CUTTING', null, 'CUT');
        $code2 = $service->generate('CUTTING', null, 'CUT');
        $code3 = $service->generate('CUTTING', null, 'CUT');

        $this->assertEquals('CUT-00001', $code1);
        $this->assertEquals('CUT-00002', $code2);
        $this->assertEquals('CUT-00003', $code3);

        $this->assertDatabaseHas('document_sequences', [
            'document_type' => 'CUTTING',
            'current_sequence' => 3,
        ]);
    }

    /**
     * Test token substitution with {COMPANY}, {YEAR}, and {SEQUENCE:4}.
     */
    public function test_token_substitution_with_company_and_year(): void
    {
        $company = Company::create([
            'company_name' => 'Apex Spinning Mills Ltd.',
            'company_code' => 'ASML',
            'company_type' => 'CLIENT_TENANT',
        ]);

        DocumentSequence::create([
            'company_id' => $company->id,
            'document_type' => 'BUYER_ORDER',
            'prefix' => 'PO',
            'format_pattern' => '{COMPANY}/{YEAR}/{PREFIX}-{SEQUENCE:4}',
            'current_sequence' => 41,
            'padding_length' => 4,
        ]);

        $service = new CodeGeneratorService();
        $code = $service->generate('BUYER_ORDER', $company);

        $expectedYear = date('Y');
        $this->assertEquals("ASML/{$expectedYear}/PO-0042", $code);
    }

    /**
     * Test company isolation in sequences (Company A sequence does not affect Company B).
     */
    public function test_company_sequence_isolation(): void
    {
        $companyA = Company::create([
            'company_name' => 'Company A',
            'company_code' => 'COA',
            'company_type' => 'CLIENT_TENANT',
        ]);

        $companyB = Company::create([
            'company_name' => 'Company B',
            'company_code' => 'COB',
            'company_type' => 'CLIENT_TENANT',
        ]);

        $service = new CodeGeneratorService();

        $codeA1 = $service->generate('UNIT', $companyA, 'UNIT');
        $codeA2 = $service->generate('UNIT', $companyA, 'UNIT');

        $codeB1 = $service->generate('UNIT', $companyB, 'UNIT');

        $this->assertEquals('UNIT-00001', $codeA1);
        $this->assertEquals('UNIT-00002', $codeA2);
        $this->assertEquals('UNIT-00001', $codeB1);
    }
}
