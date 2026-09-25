# Module 00: কোর, আইএএম ও ডুয়াল ড্যাশবোর্ড চেকলিস্ট
> **রেফারেন্স এসআরএস/এসওপি:** `docs/modules/00-core-and-iam/`  
> **আর্কিটেকচারাল পলিসি:** ADR-01, ADR-08, ADR-10, ADR-11, ADR-12, ADR-16

---

### ১. ব্যাকএন্ড ডাটাবেজ ও মডেলিং (Laravel 13 + PostgreSQL 16)
- [x] **1.1 UUID v7 বেস আর্কিটেকচার (Invariant-01)**
  - [x] `App\Shared\Traits\HasUuidV7` ট্রেইট তৈরি
  - [x] ডিফল্ট `users` ও `sessions` টেবিলে UUID v7 মাইগ্রেশন
- [x] **1.2 কোম্পানি ও মাল্টি-টেন্যান্ট স্কিমা (Tenant Base - ADR-11, ADR-13)**
  - *কেন এই টাস্ক রেকমেন্ড করা হয়েছে (Rationale):* এটি মাল্টি-টেন্যান্সি এবং আরএমজি ইআরপির কোর রুট। সিস্টেমে ইউজারদের নির্দিষ্ট কোম্পানির অধীনে রোল ও পারমিশন দিতে হলে ডাটাবেজে সবার আগে একটি ভ্যালিড `companies` টেবিল এবং ইউজার টেবিলে `company_id` ফরেন কি থাকা কারিগরি পূর্বশর্ত।
  - [x] `companies` টেবিল মাইগ্রেশন (`id` UUID v7, `tenant_id` FK to `tenants` nullable, `company_name`, `company_code`, `company_type: PLATFORM_HOST/CLIENT_TENANT`, `business_type`, `currency`, `is_active`)
  - [x] `users` টেবিলে `username` (nullable, unique), `company_id` (UUID v7 Tenant FK) এবং `is_platform_admin` ফিল্ড মাইগ্রেশন
  - [x] `App\Domain\Organization\Models\Company`, `App\Domain\Tenant\Models\Tenant` এবং `App\Models\User` টেন্যান্ট রিলেশনশিপ (`tenant()`, `companies()`, `company()`, `users()`)
- [x] **1.3 সেন্ট্রাল টেন্যান্টস স্কিমা ও কন্ট্রোল প্লেইন (Central Tenants - ADR-13, MOD-01-APPLIANCE)**
  - *কেন এই টাস্ক রেকমেন্ড করা হয়েছে (Rationale):* TraceFlow-Platform একটি মাল্টি-টেন্যান্ট SaaS ও Appliance প্ল্যাটফর্ম। সেন্ট্রাল হোস্ট ডাটাবেজে প্রতিটি ক্লায়েন্টের ডেডিকেটেড ডাটাবেজ ইনফো (`db_name`, `db_host`, `db_user`, `db_password`), সাবডোমেন (`{slug}.traceflow.app`), কাস্টম ডোমেন এবং ডেপ্লয়মেন্ট টাইপ (`CLOUD_SAAS` বনাম `ON_PREMISES_APPLIANCE`) সংরক্ষিত থাকতে হবে, যা ক্লায়েন্ট অনবোর্ডিং ও ডাইনামিক ডিবি রাউটিংয়ের মূল ভিত্তি।
  - [x] `tenants` টেবিল মাইগ্রেশন (`id` UUID v7, `client_name`, `client_slug`, `subdomain`, `custom_domain`, `deployment_type`, `db_host`, `db_port`, `db_name`, `db_username`, `db_password`, `server_ip`, `hardware_fingerprint`, `is_active`)
  - [x] `App\Domain\Tenant\Models\Tenant` মডেল তৈরি ও ফিল্ড এনক্রিপশন
- [x] **1.4 প্ল্যাটফর্ম ওনার ও সিস্টেম বুটস্ট্র্যাপ ইঞ্জিন (ADR-11)**
  - *কেন এই টাস্ক রেকমেন্ড করা হয়েছে (Rationale):* [ADR-11] অনুযায়ী রুট প্ল্যাটফর্ম ওনার কোম্পানি (`ROOT-PLATFORM`, `PLATFORM_HOST`) এবং ৭টি অভ্যন্তরীণ ইঞ্জিনিয়ারিং টিম অ্যাকাউন্ট (`superadmin`, `backend.team`, `frontend.team`, `database.team`, `devops.team`, `qa.team`, `ba.team`) সাধারণ কোনো সিডারে রাখা যাবে না। এটি একটি ডেডিকেটেড ইডেমপোটেন্ট সার্ভিস এবং আর্টিসান কমান্ডের মাধ্যমে নিশ্চিত হতে হবে, যা যে কোনো এনভায়রনমেন্টে শতবার রান করলেও ডুপ্লিকেট হবে না এবং ডেমো পার্জে সুরক্ষিত থাকবে।
  - [x] `App\Domain\Organization\Services\PlatformBootstrapService` তৈরি (ইডেমপোটেন্ট রুট ও ৭ টিম অ্যাকাউন্ট তৈরি)
  - [x] `traceflow:bootstrap-platform` আর্টিসান কমান্ড তৈরি
  - [x] `PlatformBootstrapTest` ইউনিট টেস্ট তৈরি ও ইডেমপোটেন্সি ভেরিফিকেশন (100% Pass)
- [x] **1.5 Spatie RBAC ইন্টিগ্রেশন (মাল্টি-কোম্পানি মোড ও সুপারঅ্যাডমিন গেট - ADR-08, ADR-11)**
  - *কেন এই টাস্ক রেকমেন্ড করা হয়েছে (Rationale):* ডাটাবেজে ইউজার এবং কোম্পানি স্ট্রাকচার প্রস্তুত হওয়ার পর কোম্পানিভেদে সুনির্দিষ্ট রোল ও পারমিশন প্রয়োগের জন্য Spatie RBAC কনফিগার করা আবশ্যক।
  - [x] Spatie Laravel-Permission প্যাকেজ ইনস্টল
  - [x] `teams => true` এবং `team_foreign_key => company_id` কনফিগারেশন
  - [x] Spatie পারমিশন টেবিলসমূহ UUID v7 ফরম্যাটে মাইগ্রেট করা (`App\Domain\Organization\Models\Permission`, `App\Domain\Organization\Models\Role`)
  - [x] সুপারঅ্যাডমিন গেট বাইপাস (`Gate::before`) পলিসি কনফিগারেশন (ADR-11 Decision 02)
  - [x] `MultiCompanyRBACTest` ইউনিট টেস্ট তৈরি ও ভেরিফিকেশন (UUID v7, Company Scoping, Gate Bypass - 100% Pass)
- [ ] **1.6 সেন্ট্রালাইজড ডায়নামিক অপশনস ইঞ্জিন (ADR-10)**
  - [ ] `system_categories` ও `system_options` টেবিল মাইগ্রেশন
  - [ ] Redis ক্যাশ ভিত্তিক `OptionService` তৈরি
- [x] **1.6 অটো ডকুমেন্ট কোড জেনারেটর ইঞ্জিন (ADR-09 & Invariant-11)**
  - *কেন এই টাস্ক রেকমেন্ড করা হয়েছে (Rationale):* তৈরি পোশাক কারখানায় হাজার হাজার ট্র্যাকিং কোড (Company Code, Plant Code, Order PO, Cutting Number, Bundle Barcode) তৈরি হয়। ইউজারকে ম্যানুয়ালি টাইপ করতে দিলে টাইপো ও ডুপ্লিকেট সৃষ্টি হয়। সেন্ট্রালাইজড কনফিগারেশন ড্রাইভেন অটো-নাম্বারিং ইঞ্জিন পেসিমিস্টিক রো লক (`lockForUpdate()`) সহকারে কাজ করায় একাধিক ফ্লোর টার্মিনাল একই মিলিসেকেন্ডে হিট করলেও জিরো ডুপ্লিকেট নিশ্চিত হয়।
  - [x] `document_sequences` টেবিল মাইগ্রেশন (`id` UUID v7, `company_id`, `document_type`, `prefix`, `format_pattern`, `current_sequence`, `padding_length`)
  - [x] `App\Domain\Organization\Models\DocumentSequence` এলোকুয়েন্ট মডেল তৈরি
  - [x] `App\Domain\Organization\Services\CodeGeneratorService` তৈরি (পেসিমিস্টিক লক ও টোকেন সাবস্টিটিউশন সহ)
  - [x] `CodeGeneratorTest` ইউনিট টেস্ট তৈরি ও ভেরিফিকেশন (100% Pass)

---

### ২. ব্যাকএন্ড সার্ভিস ও এপিআই লেয়ার (Modular DDD)
- [x] **2.1 অথেনটিকেশন ও ডুয়াল ড্যাশবোর্ড লজিক (SRS_LOGIN, ADR-11, ADR-12)**
  - *কেন এই টাস্ক রেকমেন্ড করা হয়েছে (Rationale):* ডাটাবেজ মডেলিং ও বুটস্ট্র্যাপ প্রস্তুত হওয়ার পর প্ল্যাটফর্মের পরবর্তী ধাপ হলো নিরাপদ লগইন এবং রোল ভিত্তিক ডুয়াল ড্যাশবোর্ড রাউটিং ডিসিশন ইঞ্জিন (`/platform/command-center` বনাম `/app/dashboard`)। ইউজারনেম ও ইমেইল উভয় মাধ্যমে লগইন নিশ্চিত করে সেন্ট্রাল Sanctum টোকেন জেনারেট করা।
  - [x] Sanctum টোকেন অথেনটিকেশন প্যাকেজ ও UUID-কম্প্যাটিবল `personal_access_tokens` টেবিল মাইগ্রেশন
  - [x] `App\Http\Requests\Auth\LoginRequest` তৈরি (Email or Username সমর্থন)
  - [x] `App\Domain\IAM\Services\AuthService` তৈরি (ডুয়াল ড্যাশবোর্ড রাউটিং ডিসিশন সহ)
  - [x] `App\Http\Controllers\Api\V1\Auth\AuthController` তৈরি (`/api/v1/auth/login`, `/api/v1/auth/me`, `/api/v1/auth/logout`)
  - [x] `AuthenticationTest` ফিচার টেস্ট তৈরি ও ভেরিফিকেশন (5 Tests, 100% Pass)
- [ ] **2.2 অর্গানাইজেশন ডোমেন সার্ভিসেস (`app/Domain/Organization/`)**
  - [ ] `CompanyService` (কোম্পানি অনবোর্ডিং ও কনফিগ)
  - [ ] `FactoryUnitService` (ইউনিট ও ফ্লোর ম্যানেজমেন্ট)
  - [ ] `ProductionLineService` (সুইং লাইন ও ক্যাপাসিটি ম্যানেজমেন্ট)
- [ ] **2.3 থিন কন্ট্রোলার ও রেসপন্স আর্কিটেকচার**
  - [ ] `AuthController`
  - [ ] `CompanyController`
  - [ ] `FactoryUnitController`
  - [ ] `ProductionLineController`

---

### ৩. ফ্রন্টএন্ড কোর ও ইউআই সিস্টেম (React 19 + TypeScript + Vite)
- [x] **3.1 সেন্ট্রালাইজড ডিজাইন সিস্টেম ও টোকেন (Zero Inline CSS - ADR-16, UI-UX-GUIDELINES)**
  - *কেন এই টাস্ক রেকমেন্ড করা হয়েছে (Rationale):* গার্মেন্টস ফ্লোর ও ম্যানেজমেন্টে একটানা ৮-১০ ঘণ্টা কাজের সুবিধার জন্য আই-কমফোর্ট সফট স্লেট ক্যানভাস (`#f8fafc`), ব্র্যান্ডেড ব্লু অ্যাকসেন্ট এবং জিরো ইনলাইন সিএসএস নিশ্চিত করতে সেন্ট্রালাইজড ম্যানেজেবল ডিজাইন টোকেন ও কোর ইউআই অ্যাটমস প্রস্তুত করা হয়েছে।
  - [x] সেন্ট্রালাইজড ডিজাইন টোকেন ও সিএসএস ভেরিয়েবলস (`frontend/src/styles/tokens.css` - Light & Ergonomic Dark Mode)
  - [x] গ্লোবাল রিসেট ও ট্যাবুলার নিউমারালস রুলস (`frontend/src/index.css`)
  - [x] কোর UI অ্যাটমস: `Button` (5 variants, 3 sizes, loading spinner), `Input` (error, helper, icons), `Card` (3 variants, 4 paddings), `Alert` (4 signal tints)
  - [x] TypeScript Strict Build & Oxlint ভেরিফিকেশন (0 errors, 0 warnings in `src`)
- [ ] **3.2 অথেনটিকেশন ও স্টেট ম্যানেজমেন্ট**
  - [ ] Zustand Auth Store (`authStore.ts`: টোকেন, কারেন্ট কোম্পানি, পারমিশনস)
  - [ ] Axios/Fetch সেন্ট্রালাইজড এপিআই ক্লায়েন্ট উইথ ইন্টারসেপ্টরস
- [ ] **3.3 স্ক্রিন ও ড্যাশবোর্ড পেজসমূহ**
  - [ ] এন্টারপ্রাইজ লগইন স্ক্রিন (Eye-Comfort ও রিমেম্বার মি সহ)
  - [ ] ডুয়াল ড্যাশবোর্ড সুইচিং ভিউ (Superadmin Panel vs Factory Floor Hub)
  - [ ] কোম্পানি ও ইউনিট ডিরেক্টরি পেজ (TanStack Table v8 ভার্চুয়ালাইজড গ্রিড)
  - [ ] স্লাইড-ওভার ড্রয়ার ফর্ম (অটো-কোড প্রিভিউ সহ)

---

### ৪. টেস্টিং ও কোয়ালিটি গেট ভেরিফিকেশন (Zero Error Mandate)
- [ ] Pest/PHPUnit ব্যাকএন্ড ইউনিট ও ফিচার টেস্ট (100% Pass)
- [ ] TypeScript strict compilation (`npm run build` -> 0 error)
- [ ] Linting ভেরিফিকেশন (`npm run lint` -> 0 error, 0 warning)
