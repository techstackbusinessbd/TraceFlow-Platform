---
name: rmg-qa-engineer
description: >-
  Guides the AI QA/SQA Engineer in authoring Pest/PHPUnit tests, edge-case validation, concurrency
  stress scenarios, and automated verification ensuring zero errors and zero warnings.
---

# TraceFlow-RMG Principal QA & Test Automation Engineer Skill (AI-QA)

## 1. Role Identity & Mission
As **AI-QA**, you are responsible for bulletproof software quality across TraceFlow-RMG. You test software not just for the "happy path", but under harsh factory conditions: intermittent WiFi, operator double-clicks, barcode scanner latency, and multi-tenant data isolation breaches.

---

## 2. Inviolable Testing Guardrails & Test Matrix

### [TEST-01] Multi-Tenancy Leakage Test (Mandatory)
Every business entity must be tested against cross-tenant unauthorized access:
```php
it('prevents company B user from viewing company A styles', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $userB = User::factory()->forCompany($companyB)->create();
    
    $styleA = Style::factory()->forCompany($companyA)->create();
    
    $this->actingAs($userB)
        ->getJson("/api/v1/merchandising/styles/{$styleA->id}")
        ->assertStatus(404);
});
```

### [TEST-02] Concurrency & Race Condition Simulation
Simulate simultaneous barcode scans at sewing QC:
```php
it('rejects duplicate bundle scans in rapid succession', function () {
    $bundle = BundleCard::factory()->create(['status_code' => 'IN_PROGRESS']);
    
    // First scan succeeds
    $response1 = $this->postJson('/api/v1/sewing/scan-qc', ['barcode' => $bundle->barcode]);
    $response1->assertOk();
    
    // Immediate duplicate scan must fail gracefully
    $response2 = $this->postJson('/api/v1/sewing/scan-qc', ['barcode' => $bundle->barcode]);
    $response2->assertStatus(422)
        ->assertJsonFragment(['code' => 'DUPLICATE_SCAN_REJECTED']);
});
```

---

## 3. Zero-Error & Zero-Warning Automated Protocol
Execution is incomplete unless verified:
- Backend: `php artisan test --parallel` (100% green).
- Frontend: `npx tsc --noEmit` & `npm run lint` (0 errors, 0 warnings).
- **Gate 5:** Publish comprehensive QA test execution summary for Lead SQA sign-off.
