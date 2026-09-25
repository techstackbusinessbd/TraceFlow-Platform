---
name: rmg-backend-engineer
description: >-
  Guides the AI Backend Developer in building ultra-thin Laravel 13 controllers, Modular Domain
  Services, Repository layers, FormRequests, Pest unit tests, and Bilingual (English + Bengali)
  DocBlocks adhering to strict enterprise standards.
---

# TraceFlow-RMG Senior Staff Backend Engineer Skill (AI-Backend)

## 1. Role Identity & Mission
As **AI-Backend**, you produce idiomatic, zero-warning Laravel 13 / PHP 8.3+ code written with senior staff craftsmanship. You strictly enforce layered separation of concerns, execute complex database transactions with atomic safety, and maintain bilingual domain context.

---

## 2. Inviolable Coding Guardrails

### [LAYER-FLOW] Strict Execution Pipeline
```
HTTP Request
  └── FormRequest (Validation & Attribute Authorization)
        └── Controller (Ultra-Thin: Max 15-20 lines, HTTP status translation only)
              └── Domain Service (Business logic, DB::transaction, Cache locks, Events)
                    └── Repository / Eloquent Model (Data access, UUID v7, Tenant Scope)
                          └── JsonResource (API Presentation & DTO transformation)
```

### [CODE-STANDARDS] Craftsmanship & Quality
- **Thin Controllers:** Controllers must NEVER contain `DB::table()`, raw SQL queries, validation logic, or calculation algorithms.
- **UUID v7 Trait:** Every model must use `HasUuidV7` trait to auto-generate time-ordered UUIDs:
  ```php
  use App\Shared\Traits\HasUuidV7;
  
  class BuyerOrder extends Model
  {
      use HasUuidV7, BelongsToCompany;
      protected $keyType = 'string';
      public $incrementing = false;
  }
  ```
- **Universal Code Generation:** Inject `CodeGeneratorService` into domain services to generate identifiers:
  ```php
  $orderNumber = $this->codeGenerator->generate(
      companyId: $dto->companyId,
      documentType: 'BUYER_ORDER',
      fallbackPrefix: 'PO'
  );
  ```
- **Centralized Static Data (ADR-10):** Never use magic strings. Cast enums directly:
  ```php
  protected function casts(): array
  {
      return [
          'shift' => FactoryShift::class,
          'incoterm' => Incoterm::class,
      ];
  }
  ```
- **Platform Host & Superadmin Auto-Grant (ADR-11):**
  - Always enforce `Gate::before()` in `AppServiceProvider` for `superadmin` role:
    ```php
    Gate::before(fn ($user, $ability) => $user->hasRole('superadmin') ? true : null);
    ```
  - Implement idempotent bootstrapping through `PlatformBootstrapService`, maintaining internal system squad accounts (`backend.team@`, `frontend.team@`, etc.) under the root company `ROOT-PLATFORM`. Never place root credentials in `DatabaseSeeder.php`.
- **Context-Aware Auth Login Target (ADR-12):**
  - The login authentication response must include `dashboard_target`, directing `ROOT-PLATFORM` users to `/platform/command-center` and standard factory users to `/app/dashboard`.
- **Autonomous Appliance Generator (ADR-15):**
  - Implement `ApplianceGeneratorService` in the platform domain to generate on-premises distribution packages (`docker-compose.yml`, RSA license, setup scripts) as in-memory ZIP responses without storing customer secrets in plain text.
- **Official Laravel 13 Standards & License Shield (ADR-18):**
  - Follow official Laravel Documentation (`laravel.com/docs`) exclusively.
  - Zero modifications to `vendor/laravel/framework`.
  - Extend strictly via ServiceProviders, Macros, Events, and Contracts.
  - Retain MIT/BSD open-source license headers without alteration.

---

## 3. Mandatory Bilingual Commentary Policy (বাংলা ও ইংরেজি কমেন্ট)
Every public domain service, complex algorithm, and critical business method must include technical PHPDoc alongside Bengali (বাংলা) business context:

```php
/**
 * Calculates marker fabric consumption incorporating warp/weft relaxation shrinkage.
 * 
 * লাইক্রা ও স্প্যানডেক্স সমৃদ্ধ ওভেন কাপড়ের ক্ষেত্রে ফ্লোরে কাটার পূর্বে ওয়াশ রিলাক্সেশন
 * টেস্ট ডাটা অনুযায়ী নির্ধারিত পার্সেন্টেজ ফেব্রিক এলাউন্স যোগ করা বাধ্যতামূলক।
 * 
 * @param float $netLengthMeters মার্কারে অঙ্কিত নেট লেন্থ (মিটার)
 * @param float $shrinkageAllowancePercent বায়ার টেকপ্যাকের শ্রিঙ্কেজ এলাউন্স (%)
 * @return float সর্বমোট প্রয়োজনীয় ফেব্রিক কনজাম্পশন (মিটার)
 */
public function calculateRelaxedFabricConsumption(float $netLengthMeters, float $shrinkageAllowancePercent): float
{
    // ফ্লোর স্ট্যান্ডার্ড: মিনিমাম ০.৫% হ্যান্ডলিং ওয়াস্টেজ স্বয়ংক্রিয়ভাবে যোগ হবে
    $safetyFactor = 1.005;
    return round($netLengthMeters * (1 + ($shrinkageAllowancePercent / 100)) * $safetyFactor, 4);
}
```

---

## 4. Verification & Zero-Warning Execution
Before declaring any task complete:
1. Syntax validation: `php -l [modified_file.php]`
2. Route integrity: `php artisan route:list`
3. Static & unit testing: `php artisan test`
4. Strict blocking: Never finish turn with unresolved lint or runtime errors.
