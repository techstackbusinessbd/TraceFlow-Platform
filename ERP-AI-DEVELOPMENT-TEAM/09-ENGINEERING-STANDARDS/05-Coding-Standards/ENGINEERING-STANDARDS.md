# TraceFlow-RMG: সফটওয়্যার ইঞ্জিনিয়ারিং ও কোডিং স্ট্যান্ডার্ডস
**ডকুমেন্ট ভার্সন:** 1.0.0  
**সর্বশেষ আপডেট:** 2026-09-24  
**স্ট্যাটাস:** MANDATORY / PRODUCTION-READY  
**প্রযোজ্য স্ট্যাক:** Laravel 13 (Backend API), React (Frontend SPA/PWA), PostgreSQL 16+ (Database)

---

## ১. ব্যাকএন্ড ইঞ্জিনিয়ারিং স্ট্যান্ডার্ড (Laravel 13 Architecture)

### ১.১ লেয়ার্ড আর্কিটেকচার ও দায়িত্ব বণ্টন (Layered Architecture)
TraceFlow-RMG ব্যাকএন্ডে কঠোরভাবে **Controller → FormRequest → Service → Repository/Model → Resource/DTO** প্যাটার্ন মান্য করতে হবে।

```
[HTTP Request]
       │
       ▼
[FormRequest (Validation & Authorization)]
       │
       ▼
[API Controller (Orchestrator only, Max 15-20 lines per method)]
       │
       ▼
[Domain Service (Pure Business Logic & Transactions)]
       │
       ▼
[Repository / Eloquent Model (Database Operations & Scopes)]
       │
       ▼
[API Resource / DTO (Structured JSON Response)]
```

### ১.২ ব্যাকএন্ডের প্রধান নিয়মনীতি (Core Backend Rules)
1. **কন্ট্রোলারে কোনো বিজনেস লজিক নয় (Skinny Controller):** কন্ট্রোলার কেবল রিকোয়েস্ট গ্রহণ করবে, সার্ভিসের মেথড কল করবে এবং রিসোর্স রিটার্ন করবে।
2. **ডাটাবেজ ট্রানজ্যাকশন সেফটি:** মাল্টি-টেবিল এন্ট্রি বা প্রোডাকশন স্ক্যানের ক্ষেত্রে বাধ্যতামূলকভাবে `DB::transaction()` ব্লক ব্যবহার করতে হবে।
3. **কাস্টম এক্সেপশন ও স্ট্যান্ডার্ড এরর রেসপন্স:**
   - এরর রেসপন্সের ফরম্যাট সবসময় এক হবে:
     ```json
     {
       "success": false,
       "message": "Specific error explanation",
       "errors": [],
       "error_code": "ERR_BUNDLE_ALREADY_SCANNED"
     }
     ```
4. **অডিট ট্রেইল (Audit Logging):** যেকোনো ইনসার্ট, আপডেট বা ডিলিট অপারেশনে স্বয়ংক্রিয়ভাবে `created_by`, `updated_by` এবং অডিট টেবিল ট্র্যাক করতে হবে।
5. **ফাইল সাইজ ও মেথড সীমা (Golden Limits):** কন্ট্রোলার মেথড সর্বোচ্চ ১৫-২০ লাইন, কন্ট্রোলার ফাইল সর্বোচ্চ ১২০ লাইন, সার্ভিস ফাইল সর্বোচ্চ ২৫০ লাইন। বিস্তারিত গাইডলাইন:
   👉 **[FILE-SIZE-LIMITS-AND-GOLDEN-RULES.md](file:///d:/ERP/TraceFlow-RMG/ERP-AI-DEVELOPMENT-TEAM/09-ENGINEERING-STANDARDS/05-Coding-Standards/FILE-SIZE-LIMITS-AND-GOLDEN-RULES.md)**।
6. **দ্বিভাষিক কোড কমেন্টিং (Bilingual Code Commentary):** প্রতিটি সার্ভিস ও ক্রিটিক্যাল মেথডে ইংরেজি টেকনিক্যাল সামারি এবং বাংলা আরএমজি ফ্লোর উদ্দেশ্য সহ দ্বৈত কমেন্টিং বাধ্যতামূলক। বিস্তারিত গাইডলাইন:
   👉 **[BILINGUAL-CODE-COMMENTING-POLICY.md](file:///d:/ERP/TraceFlow-RMG/ERP-AI-DEVELOPMENT-TEAM/09-ENGINEERING-STANDARDS/05-Coding-Standards/BILINGUAL-CODE-COMMENTING-POLICY.md)**।

---

## ২. ডাটাবেজ স্ট্যান্ডার্ড (PostgreSQL 16+ Standards)

### ২.১ নামকরণ ও স্কিমা কনভেনশন (Naming Conventions)
- টেবিলের নাম: বহুবচনে এবং স্নেক কেস (`snake_case`) হবে (যেমন: `bundle_cards`, `sewing_outputs`, `buyer_orders`)।
- প্রাইমারি কি: বাধ্যতামূলকভাবে টাইম-অর্ডার্ড **`id: UUID (UUID v7)`**। কোনো টেবিলে `BIGSERIAL` ব্যবহার করা যাবে না।
- ফরেন কি: সিঙ্গুলার টেবিল নাম + `_id` (যেমন: `bundle_card_id`, `style_id`).
- টাইমস্ট্যাম্প: প্রতিটি টেবিলে `created_at`, `updated_at` এবং প্রয়োজনবোধে `deleted_at` (Soft Delete) থাকতে হবে।

### ২.২ ইনডেক্সিং ও পারফরম্যান্স গাইডলাইন
- হাই-ফ্রিকোয়েন্সি সার্চ কলাম (যেমন: `barcode`, `qr_code`, `status`, `line_id`) এ ইউনিক অথবা কম্পোজিট B-Tree ইনডেক্স বাধ্যতামূলক।
- কাটিং ও সুয়িং স্ক্যান লগের মতো কোটি কোটি রেকর্ডের টেবিলে রেঞ্জ/লিস্ট পার্টিশনিং (Partitioning by Year/Month or Factory Unit) প্রয়োগ করতে হবে।

---

## ৩. ফ্রন্টএন্ড ইঞ্জিনিয়ারিং স্ট্যান্ডার্ড (React Architecture)

### ৩.১ কম্পোনেন্ট ও স্টেট ম্যানেজমেন্ট
1. **ফাংশনাল কম্পোনেন্ট ও হুকস:** ক্লাস কম্পোনেন্ট সম্পূর্ণরূপে নিষিদ্ধ।
2. **ডিরেক্টরি স্ট্রাকচার:**
   ```
   src/
   ├── components/      # রিইউজেবল বেস কম্পোনেন্টস (Button, Modal, Table, Badge)
   ├── features/        # ডোমেইন ভিত্তিক ফিচার মডিউল (Cutting, Sewing, Packing)
   │   ├── components/
   │   ├── hooks/
   │   ├── services/    # Axios API Calls
   │   └── types/
   ├── layouts/         # ড্যাশবোর্ড ও প্রোডাকশন স্ক্রিন লেআউট
   └── store/           # গ্লোবাল স্টেট (Zustand / Redux Toolkit)
   ```
3. **বারকোড স্ক্যান অপ্টিমাইজেশন:** প্রোডাকশন ফ্লোর স্ক্রিনে কি-বোর্ড ওয়েজ ও ইউএসবি বারকোড স্ক্যানার দিয়ে স্ক্যান করার জন্য ডেডিকেটেড `useBarcodeScanner` হুক ব্যবহার করতে হবে যা কোনো ইউজার ইন্টারাপশন ছাড়াই ব্যাকগ্রাউন্ডে ইনপুট হ্যান্ডল করবে।

---

## ৪. গিট ও ব্রাঞ্চিং স্ট্যান্ডার্ড (Git Workflow & Commit Rules)

### ৪.১ ব্রাঞ্চিং স্ট্র্যাটেজি
- `main` / `production`: লাইভ প্রোডাকশন কোড। সরাসরি কোনো কমিট নিষিদ্ধ।
- `staging`: কিউএ এবং ক্লায়েন্ট ইউএটি (UAT) টেস্টিং এনভায়রনমেন্ট।
- `develop`: অ্যাক্টিভ ডেভেলপমেন্ট ইন্টিগ্রেশন ব্রাঞ্চ।
- `feature/<module>-<task-id>`: যেকোনো নতুন ফিচার ব্রাঞ্চ (যেমন: `feature/cutting-bundle-card-TF-102`).
- `bugfix/<issue-id>`: বাগ সমাধানের জন্য নির্ধারিত ব্রাঞ্চ।

### ৪.২ কমিট মেসেজ কনভেনশন (Conventional Commits)
কমিট মেসেজ অবশ্যই অর্থবহ ও স্ট্রাকচারড হতে হবে:
- `feat(cutting): add bundle card barcode generation service`
- `fix(sewing): prevent duplicate piece card scan at end line`
- `refactor(db): add composite index to sewing_production_logs`
- `docs(governance): update approval gates sign-off checklist`

---

## ৫. লোকাল ডেভেলপমেন্ট এনভায়রনমেন্ট ও রানটাইম স্ট্যান্ডার্ড (Strict Docker-Only Environment)

TraceFlow-RMG প্রজেক্টে কোনো ডেভেলপারের লোকাল কম্পিউটারে সরাসরি রানটাইম ইনস্টল করে কোড চালানো সম্পূর্ণ নিষিদ্ধ।

1. **লোকাল পিসিতে ইনস্টলেশন নিষিদ্ধ:**
   - লোকাল অপারেটিং সিস্টেমে সরাসরি PHP, Composer, Node.js, PostgreSQL বা Redis ইনস্টল করা যাবে না।
   - "আমার পিসিতে কাজ করছে কিন্তু সার্ভারে করছে না"—এই সমস্যা নির্মূল করতে শতভাগ কাজ ডকার কনটেইনারে সম্পন্ন হবে।
2. **ডকার কম্পোজ অর্কেস্ট্রেশন (`docker-compose.yml`):**
   - ব্যাকএন্ড (PHP 8.3 FPM), ডাটাবেজ (Postgres 16), ক্যাশ (Redis 7), কিউ (Horizon), ওয়েবসকেট (Reverb), অবজেক্ট স্টোরেজ (MinIO) এবং ফ্রন্টএন্ড (Vite HMR) ডকার নেটওয়ার্কে চলবে।
3. **কমান্ড এক্সিকিউশন রুলস:**
   - **Backend Artisan:** `docker compose exec backend php artisan [command]`
   - **Composer:** `docker compose exec backend composer [command]`
   - **Frontend NPM:** `docker compose exec frontend npm [command]`
   - **Automated Tests:** `docker compose exec backend php artisan test`
   - **Linter (Pint):** `docker compose exec backend ./vendor/bin/pint`

---
**প্রয়োগ ও কঠোরতা:** কোনো পুল রিকুয়েস্ট (PR) বা কোড এই স্ট্যান্ডার্ড শতভাগ পূরণ না করলে স্বয়ংক্রিয় সিআই পাইপলাইনে রিজেক্ট হবে।
