# Module 00: কোর, আইএএম ও ডুয়াল ড্যাশবোর্ড চেকলিস্ট
> **রেফারেন্স এসআরএস/এসওপি:** `docs/modules/00-core-and-iam/`  
> **আর্কিটেকচারাল পলিসি:** ADR-01, ADR-08, ADR-10, ADR-11, ADR-12, ADR-16

---

### ১. ব্যাকএন্ড ডাটাবেজ ও মডেলিং (Laravel 13 + PostgreSQL 16)
- [x] **1.1 UUID v7 বেস আর্কিটেকচার (Invariant-01)**
  - [x] `App\Shared\Traits\HasUuidV7` ট্রেইট তৈরি
  - [x] ডিফল্ট `users` ও `sessions` টেবিলে UUID v7 মাইগ্রেশন
- [x] **1.2 কোম্পানি টেবিল ও মডেল (Tenant Base)**
  - *কেন এই টাস্ক রেকমেন্ড করা হয়েছে (Rationale):* এটি মাল্টি-টেন্যান্সি এবং আরএমজি ইআরপির কোর রুট। সিস্টেমে ইউজারদের নির্দিষ্ট কোম্পানির অধীনে রোল ও পারমিশন দিতে হলে ডাটাবেজে সবার আগে একটি ভ্যালিড `companies` টেবিল থাকা কারিগরি পূর্বশর্ত।
  - [x] `companies` টেবিল মাইগ্রেশন (`id` UUID v7, `company_name`, `company_code`, `business_type`, `currency`, `is_active`)
  - [x] `App\Domain\Organization\Models\Company` মডেল তৈরি ও রিলেশনশিপ
  - [x] `CompanyTest` ইউনিট টেস্ট তৈরি ও ভেরিফিকেশন
- [ ] **1.3 Spatie RBAC ইন্টিগ্রেশন (মাল্টি-কোম্পানি মোড - ADR-08)**
  - *কেন এই টাস্ক রেকমেন্ড করা হয়েছে (Rationale):* আমাদের ডাটাবেজে `users` এবং `companies` টেবিল তৈরি হয়ে গেছে। এখন ইউজারদের সুরক্ষিতভাবে লগইন করাতে এবং কোম্পানিভেদে সুনির্দিষ্ট রোল (যেমন: Factory Admin, Merchandiser, Cutting Master, QC Inspector) প্রদান করতে মাল্টি-টেন্যান্ট পারমিশন গার্ড সক্রিয় করা আবশ্যকীয় পরবর্তী ধাপ।
  - [ ] Spatie Laravel-Permission প্যাকেজ ইনস্টল
  - [ ] `teams => true` এবং `team_foreign_key => company_id` কনফিগারেশন
  - [ ] Spatie পারমিশন টেবিলসমূহ UUID v7 ফরম্যাটে মাইগ্রেট করা
  - [ ] পারমিশন সিঙ্ক কমান্ড (`traceflow:sync-permissions`) তৈরি
- [ ] **1.4 সেন্ট্রালাইজড ডায়নামিক অপশনস ইঞ্জিন (ADR-10)**
  - [ ] `system_categories` ও `system_options` টেবিল মাইগ্রেশন
  - [ ] Redis ক্যাশ ভিত্তিক `OptionService` তৈরি
- [ ] **1.5 অটো ডকুমেন্ট কোড জেনারেটর ইঞ্জিন (Invariant-11)**
  - [ ] `document_sequences` টেবিল মাইগ্রেশন
  - [ ] কনফিগারেবল প্রিফিক্স সহ `CodeGeneratorService` তৈরি

---

### ২. ব্যাকএন্ড সার্ভিস ও এপিআই লেয়ার (Modular DDD)
- [ ] **2.1 অথেনটিকেশন ও ডুয়াল ড্যাশবোর্ড লজিক (SRS_LOGIN)**
  - [ ] Sanctum টোকেন অথেনটিকেশন
  - [ ] ডুয়াল রোল ডিটেকশন (Platform Owner vs Factory Tenant User)
  - [ ] রেট লিমিটিং ও লগইন লকআউট গার্ড
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
- [ ] **3.1 সেন্ট্রালাইজড ডিজাইন সিস্টেম ও টোকেন (Zero Inline CSS)**
  - [ ] CSS ভেরিয়েবল ও আরগোনোমিক কালার প্যালেট (`variables.css`)
  - [ ] কোর UI অ্যাটমস: `Button`, `Input`, `Badge`, `Card`, `SlideOverDrawer`
  - [ ] গ্র্যানুলার পারমিশন গার্ড কম্পোনেন্ট (`<Can />`)
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
