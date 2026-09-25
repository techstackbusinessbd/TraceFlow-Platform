# SRS: মাল্টি-কোম্পানি অর্গানাইজেশন ও সেন্ট্রাল সিস্টেম সেটিংস
**মডিউল কোড:** MOD-00-SETTINGS  
**নথি কোড:** TFRMG-SRS-001  
**স্ট্যাটাস:** APPROVED / PRODUCTION-READY  
**প্রাসঙ্গিক এসওপি:** `docs/modules/00-core-and-iam/SOP.md`  

---

## ১. মডিউলের পরিচিতি ও ব্যবসায়িক প্রয়োজনীয়তা

এই মডিউলটি পুরো TraceFlow-RMG প্ল্যাটফর্মের ভিত্তিপ্রস্তর। এর মাধ্যমে পুরো গ্রুপের যাবতীয় প্রাতিষ্ঠানিক কাঠামো এবং কারখানার মেঝের জন্য প্রয়োজনীয় ডায়নামিক সেটিংস পরিচালিত হবে।

### মূল লক্ষ্যসমূহ:
1. **মাল্টি-কোম্পানি ব্যবস্থাপনা:** একই গ্রুপের অধীনে একাধিক সিস্টার কনসার্ন (যেমন: ওভেন, নিট, ডেনিম, ওয়াশিং) এবং তাদের নিজস্ব ফিজিক্যাল ফ্যাক্টরি প্ল্যান্টসমূহ পরিচালনা করা।
2. **ডায়নামিক ড্রপডাউন ও মাস্টার সেটিংস:** কোডের ভেতর কোনো অপশন ফিক্সড না রেখে সরাসরি অ্যাডমিন স্ক্রিন থেকে সমস্ত ড্রপডাউন (যেমন: অর্ডার স্টেজ, ফেব্রিক টাইপ, ওয়াশ টাইপ, ডিফেক্ট কোড) পরিবর্তন ও যুক্ত করার সুবিধা।
3. **আন্তঃকোম্পানি প্রোডাকশন ডেলিগেশন:** বায়ারের সাথে কমার্শিয়াল ডিল কোম্পানি 'A' করলেও একই গ্রুপের কোম্পানি 'B' এর ফ্যাক্টরি ফ্লোরে কাটিং বা সুইং করানোর পূর্ণাঙ্গ সক্ষমতা।
4. **স্বয়ংক্রিয় কোড জেনারেশন ও অ্যাডমিন কনফিগারেশন (Auto-Generated Codes):** সিস্টেমে সমস্ত কোড (Company Code, Plant Code, Order PO, Cut Number, Bundle Barcode) সিস্টেম কর্তৃক স্বয়ংক্রিয়ভাবে জেনারেট (Auto-Generated) হবে এবং এর ফরম্যাট (Prefix, Suffix, Sequence Padding) অ্যাডমিন প্যানেল থেকে কনফিগারেবল থাকবে (যেমন: `ITSL-PO-2026-0001`)।

---

## ২. স্ক্রিন লেআউট ও ইউজার এক্সপেরিয়েন্স

### ২.১ স্ক্রিন ফিল্ড ও লেবেল (Standard English Labels)
#### কোম্পানি ও ফ্যাক্টরি সেটআপ ফর্ম:
- `Group Name` (যেমন: Standard Group)
- `Company Name` (যেমন: International Trading Services Ltd.)
- `Company Code` (যেমন: ITSL)
- `Business Type` (Woven / Knit / Washing)
- `Factory Plant` (যেমন: Building 1, Unit 2)
- `Default Currency` (USD / BDT / EUR)
- `Status` (Active / Inactive)

#### ড্রপডাউন ও সিস্টেম সেটিংস ফর্ম:
- `Setting Category` (যেমন: Order Stages, Fabric Types, Defect Codes)
- `Code` (যেমন: CUT_DONE, STITCH_DROP)
- `Name` (যেমন: Cutting Completed, Broken Stitch)
- `Display Order` (১, ২, ৩...)
- `Status` (Active / Inactive)

### ২.২ ডাটা এন্ট্রি ও স্লাইড-ওভার ড্রয়ার
- কোনো পপআপ মোডাল থাকবে না। **+ New Company** অথবা **+ Add Option** বাটনে ক্লিক করলে স্ক্রিনের ডানপাশ থেকে মসৃণ **Slide-over Drawer (Width: 640px)** ওপেন হবে।
- ফাস্ট কিবোর্ড নেভিগেশন: ইউজার কিবোর্ডের `Esc` চাপলে ড্রয়ার বন্ধ হবে এবং `Ctrl + Enter` চাপলে সরাসরি সেভ হবে।

### ২.৩ ভ্যালিডেশন নীতি (Server-side Validation Only & Zero HTML5 Tooltips)
- **HTML5 ভ্যালিডেশন সম্পূর্ণ নিষিদ্ধ:** ব্রাউজারের নেটিভ HTML5 ভ্যালিডেশন এট্রিবিউট (`required`, `pattern` ইত্যাদি) ফর্ম ইনপুটে ব্যবহার করা সম্পূর্ণ নিষিদ্ধ, যাতে ব্রাউজারের ডিফল্ট পপআপ বাবল ("Please fill out this field") কখনো না আসে। ফর্মের `<form>` ট্যাগে অবশ্যই `noValidate` ব্যবহার করতে হবে।
- **১০০% সার্ভার-সাইড ভ্যালিডেশন:** সমস্ত ভ্যালিডেশন ও বিজনেস রুলস লারাভেল `FormRequest` ক্লাসে পরিচালিত হবে। ইনভ্যালিড ডাটা সাবমিট হলে ব্যাকএন্ড থেকে `422 Unprocessable Content` রেসপন্স আসবে এবং ফ্রন্টএন্ড তা ক্যাচ করে সংশ্লিষ্ট ইনপুটের নিচে পরিষ্কার লাল টেক্সট আকারে মেসেজ প্রদর্শন করবে।

### ২.৪ কালার প্যালেট ও থিম সামঞ্জস্য (Unified Cross-Page Color System)
- **ভিন্ন ভিন্ন পেজে ভিন্ন কালার সম্পূর্ণ নিষিদ্ধ:** কোনো পেজে পার্পল, কোনো পেজে গ্রিন বা টিল ইত্যাদি র্যান্ডম কালার ব্যবহার সম্পূর্ণ নিষিদ্ধ। পুরো ইআরপির সকল স্ক্রিনে অভিন্ন ডিজাইন সিস্টেম টোকেন প্রযোজ্য হবে:
  - **প্রাইমারি অ্যাকশন / বাটন:** ইউনিফাইড ব্লু (`--color-primary-600` / `#2563eb`)।
  - **ক্যানভাস ব্যাকগ্রাউন্ড:** অ্যান্টি-ফ্যাটিগ সফট স্লেট (`--color-bg-base` / `#f8fafc`)।
  - **সারফেস ও কার্ড:** পিওর হোয়াইট (`--color-bg-surface` / `#ffffff`) সাথে সূক্ষ্ম বর্ডার (`--color-border-subtle` / `#e2e8f0`)।
  - **টাইপোগ্রাফি:** মূল টেক্সট `#0f172a`, সেকেন্ডারি/মিউটেড টেক্সট `#64748b`।
  - **স্ট্যাটাস ব্যাজ:** প্যাস্টেল ব্যাকগ্রাউন্ডসহ সুনির্দিষ্ট সেমান্টিক টোকেন (`success`, `warning`, `danger`, `info`)।
  - **কার্ড ও কন্টেইনার কর্নার রেডিয়াস:** কার্ডের কর্নার রেডিয়াস সবসময় সূক্ষ্ম ও ছোট হবে (`rounded-sm` / ২-৪ পিক্সেল)। বড় বা বাবলি কার্ভ (`rounded-xl` বা তদূর্ধ্ব) সম্পূর্ণ নিষিদ্ধ।

### ২.৫ ইউজার নোটিফিকেশন মেসেজ (UI Alerts)
- `Company saved successfully.`
- `Configuration settings updated successfully.`
- `The specified Company already exists.`
- `Cannot delete item linked to active production orders. Deactivate the item instead.`

### ২.৬ ডকুমেন্ট কোড সিকোয়েন্স কনফিগারেশন স্ক্রিন (Document Sequences Screen)
- **উদ্দেশ্য:** অ্যাডমিন প্যানেল থেকে যেকোনো ডকুমেন্টের (Company Code, Plant Code, Style/Order PO, Cutting Number, Bundle Barcode) স্বয়ংক্রিয় নাম্বার ফরম্যাট ও সিকোয়েন্স কনফিগার করা।
- **স্ক্রিন লেবেলসমূহ:**
  - `Document Type` (ড্রপডাউন: Company Code, Plant Code, Order PO, Cut Number, Bundle Barcode ইত্যাদি)
  - `Prefix` (যেমন: `CMP`, `UNT`, `PO`, `CUT`, `BND`)
  - `Format Pattern` (যেমন: `{PREFIX}-{SEQUENCE:3}` অথবা `{COMPANY}-{YEAR}-{PREFIX}-{SEQUENCE:5}`)
  - `Current Sequence` (বর্তমান চলমান ক্রমিক সংখ্যা, যেমন: ১২০)
  - `Padding Length` (যেমন: ৩ বা ৫ ডিজিট, যা `001` বা `00001` ফরম্যাটে দেখাবে)
  - `Reset Frequency` (None / Yearly / Monthly)
- **UI কার্যপদ্ধতি:** কোম্পানি বা প্ল্যান্ট ক্রিয়েট করার সময় `Company Code` ও `Plant Code` ইনপুট ফিল্ডটি ড্রয়ারে সম্পূর্ণ **Disabled / Read-only (Auto-generated)** থাকবে এবং সেভ হওয়ার সময় সিস্টেম সিকোয়েন্স টেবিল থেকে রুল অনুযায়ী কোড অ্যাসাইন করবে।
- **ডিফল্ট সিস্টেম পলিসি (Zero-Config Fallback):** অ্যাডমিন প্যানেল থেকে কোনো ডকুমেন্টের নির্দিষ্ট ফরম্যাট কনফিগার না করা থাকলেও সিস্টেম অপারেশন বন্ধ থাকবে না; সিস্টেমের স্ট্যান্ডার্ড বিল্ট-ইন ডিফল্ট ফরম্যাট কার্যকর থাকবে:
  - `COMPANY_CODE`: `CMP-{SEQUENCE:3}` (যেমন: `CMP-001`, `CMP-002`)
  - `PLANT_CODE`: `UNT-{SEQUENCE:3}` (যেমন: `UNT-001`, `UNT-002`)
  - `BUYER_ORDER`: `{COMPANY}-{YEAR}-PO-{SEQUENCE:5}` (যেমন: `ITSL-2026-PO-00001`)
  - `CUT_PLAN`: `{COMPANY}-{YEAR}{MONTH}-CUT-{SEQUENCE:4}` (যেমন: `ITSL-202609-CUT-0001`)
  - `BUNDLE_QR`: `BND-{YY}{MONTH}-{SEQUENCE:6}` (যেমন: `BND-2609-000001`)

---

## ৩. ডাটাবেজ স্কিমা ডিজাইন (PostgreSQL 16 - UUID v7)

### ৩.১ কোম্পানি ও ফ্যাক্টরি ইউনিট টেবিল
```sql
-- মূল কর্পোরেট গ্রুপ
CREATE TABLE corporate_groups (
    id UUID PRIMARY KEY,                      -- UUID v7
    group_name VARCHAR(150) NOT NULL,
    group_code VARCHAR(50) UNIQUE NOT NULL,
    headquarters_address TEXT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- সিস্টার কনসার্ন / কোম্পানিজ
CREATE TABLE companies (
    id UUID PRIMARY KEY,                      -- UUID v7
    group_id UUID NOT NULL REFERENCES corporate_groups(id) ON DELETE CASCADE,
    company_name VARCHAR(150) NOT NULL,
    company_code VARCHAR(50) UNIQUE NOT NULL,
    business_type VARCHAR(50) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'USD',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ফিজিক্যাল ফ্যাক্টরি প্ল্যান্ট / ইউনিট
CREATE TABLE factory_units (
    id UUID PRIMARY KEY,                      -- UUID v7
    company_id UUID NOT NULL REFERENCES companies(id) ON DELETE CASCADE,
    unit_name VARCHAR(100) NOT NULL,
    unit_code VARCHAR(50) NOT NULL,
    address TEXT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uidx_company_unit UNIQUE (company_id, unit_code)
);
```

### ৩.২ সেন্ট্রালাইজড সিস্টেম ড্রপডাউন ও সেটিংস স্কিমা
```sql
-- সেটিংস ক্যাটাগরি মাস্টার
CREATE TABLE system_categories (
    id UUID PRIMARY KEY,                      -- UUID v7
    category_code VARCHAR(50) UNIQUE NOT NULL, -- e.g., 'ORDER_STAGES', 'DEFECT_TYPES'
    category_name VARCHAR(100) NOT NULL,
    is_system_locked BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ড্রপডাউন অপশনস
CREATE TABLE system_options (
    id UUID PRIMARY KEY,                      -- UUID v7
    category_id UUID NOT NULL REFERENCES system_categories(id) ON DELETE RESTRICT,
    option_code VARCHAR(50) NOT NULL,
    option_name VARCHAR(100) NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    metadata JSONB NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uidx_category_option UNIQUE (category_id, option_code)
);

CREATE INDEX idx_system_options_query ON system_options (category_id, is_active, display_order);
```

### ৩.৩ ইউনিভার্সাল ডকুমেন্ট কোড ও সিকোয়েন্স স্কিমা
```sql
-- সেন্ট্রাল সিকোয়েন্স ও নাম্বারিং রুলস
CREATE TABLE document_sequences (
    id UUID PRIMARY KEY,                      -- UUID v7
    company_id UUID NULL REFERENCES companies(id) ON DELETE CASCADE, -- NULL হলে গ্লোবাল ডিফল্ট
    document_type VARCHAR(50) NOT NULL,       -- e.g., 'COMPANY_CODE', 'PLANT_CODE', 'BUYER_ORDER', 'CUT_PLAN', 'BUNDLE_QR'
    prefix VARCHAR(20) NOT NULL,              -- e.g., 'CMP', 'UNT', 'PO', 'CUT'
    format_pattern VARCHAR(100) NOT NULL,     -- e.g., '{PREFIX}-{SEQUENCE:3}', '{COMPANY}-{YEAR}-{PREFIX}-{SEQUENCE:5}'
    current_number BIGINT NOT NULL DEFAULT 0,
    padding_length INT NOT NULL DEFAULT 4,
    reset_frequency VARCHAR(20) NOT NULL DEFAULT 'NEVER', -- 'NEVER', 'YEARLY', 'MONTHLY'
    last_reset_date DATE NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uidx_company_doc_type UNIQUE (company_id, document_type)
);

CREATE INDEX idx_document_sequences_lookup ON document_sequences (document_type, company_id);
```

---

## ৪. এপিআই এন্ডপয়েন্ট ও ক্যাশিং

| মেথড   | এন্ডপয়েন্ট                        | বিবরণ                                | ক্যাশ স্ট্র্যাটেজি  |
| ------ | ---------------------------------- | ------------------------------------ | ------------------- |
| `GET`  | `/api/v1/companies`                | সক্রিয় কোম্পানিগুলোর তালিকা          | Redis Tagged Cache  |
| `POST` | `/api/v1/companies`                | নতুন কোম্পানি তৈরি (Auto Code)       | Cache Purged        |
| `GET`  | `/api/v1/companies/{id}/units`     | কোম্পানির ফ্যাক্টরি প্ল্যান্ট তালিকা | Redis Cached        |
| `POST` | `/api/v1/companies/{id}/units`     | নতুন ফ্যাক্টরি ইউনিট তৈরি (Auto Code)| Cache Purged        |
| `GET`  | `/api/v1/settings/{category_code}` | নির্দিষ্ট ড্রপডাউনের অপশনস লোড       | Redis Cached (<৫ms) |
| `POST` | `/api/v1/settings`                 | নতুন ড্রপডাউন অপশন যুক্ত বা আপডেট    | Cache Purged        |
| `GET`  | `/api/v1/settings/sequences`       | সকল ডকুমেন্ট সিকোয়েন্স রুলসের তালিকা | Redis Cached        |
| `PUT`  | `/api/v1/settings/sequences/{id}`  | ডকুমেন্ট সিকোয়েন্স ফরম্যাট আপডেট     | Cache Purged        |

---

## ৫. সিকিউরিটি ও পারফরম্যান্স
1. **তাত্ক্ষণিক রেসপন্স (<৫ms):** সমস্ত ড্রপডাউন সরাসরি রেডিস ক্যাশ থেকে মিলিসেকেন্ডে লোড হবে।
2. **ডাটা আইসোলেশন:** বিশেষ অনুমতি ছাড়া এক কোম্পানির ইউজার অন্য কোম্পানির ডাটা দেখতে পারবে না।
3. **অডিট ট্রেইল:** প্রতিটি পরিবর্তন Spatie Activity Log দিয়ে স্বয়ংক্রিয়ভাবে রেকর্ড থাকবে।
