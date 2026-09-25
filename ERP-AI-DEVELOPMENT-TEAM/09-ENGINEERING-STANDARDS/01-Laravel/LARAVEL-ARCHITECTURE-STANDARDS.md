# TraceFlow-RMG: সেন্ট্রালাইজড কনফিগারেশন, এপিআই মেসেজিং ও ডায়নামিক কন্ট্রোল স্ট্যান্ডার্ড
**নথি কোড:** TFRMG-ENG-LAR-001  
**সংস্করণ:** ২.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** সফটওয়্যার ইঞ্জিনিয়ারিং ও কোডিং নির্দেশিকা  
**প্রযোজ্য লেটেস্ট স্ট্যাক:** Laravel 13 (PHP 8.3+ FPM), PostgreSQL 16+, Redis 7+, Laravel Horizon, Laravel Reverb
**অনুমোদনকারী:** ট্রেসফ্লো-আরএমজি ব্যাকএন্ড ইঞ্জিনিয়ারিং বোর্ড

---

## ১. ভূমিকা ও আর্কিটেকচারাল মূলনীতি (Architectural Philosophy)

TraceFlow-RMG ব্যাকএন্ডকে সম্পূর্ণরূপে **১০০% ডায়নামিক, সেন্ট্রালাইজড কন্ট্রোলড এবং নো-হার্ডকোড (Zero Hardcoding)** নীতিতে পরিচালনা করতে হবে। কারখানার ফ্লোরের কোনো ড্রপডাউন অপশন, স্ট্যাটাস, বারকোড প্রিফিক্স বা ব্যবসায়িক প্যারামিটার কোডে ফিক্সড থাকবে না—সবকিছু **অ্যাডমিন প্যানেল (Admin Panel)** থেকে কনফিগারযোগ্য হবে।

একই সাথে সমস্ত এপিআই এরর মেসেজ, সাকসেস মেসেজ এবং অডিট নোটিফিকেশন আন্তর্জাতিক প্রমিত **ইংরেজিতে (Professional English)** এবং অপ্রয়োজনীয় বাক্য ছাড়া অত্যন্ত সংক্ষিপ্ত ও ব্যবহারকারী-বান্ধব (Concise & User-Friendly) হতে হবে।

---

## ২. সেন্ট্রালাইজড ডায়নামিক কনফিগারেশন আর্কিটেকচার (Centralized Dynamic Config Engine)

```
[ Super Admin Panel / Configuration Dashboard ]
                     │
                     ▼
[ DB Schema: `system_parameters` & `lookup_definitions` ]
                     │
                     ▼
[ Redis Cache with Tagging: `system_configs` (Instant Invalidation on Admin Update) ]
                     │
                     ▼
[ Config Service: `AppConfig::get('CUTTING_SHRINKAGE_LIMIT')` ]
                     │
                     ▼
[ API / Business Logic Layer ] ──► Dynamic Execution (Zero Hardcoded Constants)
```

### ২.১ কনফিগারেশন ডাটাবেজ মডেলিং (`system_parameters`)
```sql
CREATE TABLE system_parameters (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(), -- UUID v7
    module_code VARCHAR(50) NOT NULL,       -- e.g., 'CUTTING', 'SEWING', 'PACKING'
    param_key VARCHAR(100) UNIQUE NOT NULL,  -- e.g., 'BARCODE_PREFIX_BUNDLE', 'DEFAULT_HOURLY_TARGET'
    param_value JSONB NOT NULL,             -- Supports String, Number, Array or Object
    data_type VARCHAR(20) NOT NULL,          -- 'string', 'number', 'boolean', 'json'
    is_admin_editable BOOLEAN DEFAULT TRUE,
    description TEXT,
    updated_by UUID REFERENCES users(id),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

### ২.২ লারাভেল কনফিগ হেল্পার সার্ভিস (`AppConfig`)
সিস্টেমের যেকোনো জায়গায় প্যারামিটার পড়তে ডায়নামিক ক্যাশড সার্ভিস ব্যবহার করতে হবে:
```php
// ❌ সম্পূর্ণ নিষিদ্ধ (হার্ডকোড কনস্ট্যান্ট):
const BUNDLE_PREFIX = 'TF-BND-';

// ✅ আদর্শ সেন্ট্রালাইজড ডায়নামিক প্র্যাকটিস:
$prefix = AppConfig::get('CUTTING.BUNDLE_BARCODE_PREFIX', 'TF-BND-');
$allowedStatuses = AppConfig::getLookupOptions('SEWING_LINE_STATUSES');
```

---

## ৩. স্ট্যান্ডার্ড এপিআই রেসপন্স ও ইংরেজি মেসেজিং কনভেনশন (English API Messages)

এপিআই রেসপন্সের সমস্ত `message` অবশ্যই প্রমিত ইংরেজি এবং সংক্ষিপ্ত হবে:

### ৩.১ সফল রেসপন্স ফরম্যাট (Success Response - 200/201)
```json
{
  "success": true,
  "message": "Bundle scanned successfully.",
  "data": {
    "bundle_id": "BND-2026-0891",
    "barcode": "TF-CUT-004-B042",
    "size": "L",
    "quantity": 20,
    "status": "IN_SEWING"
  }
}
```

### ৩.২ ভ্যালিডেশন ও ত্রুটি রেসপন্স (Error Response - 422/400/403/500)
```json
{
  "success": false,
  "message": "Duplicate bundle scan.",
  "error_code": "ERR_DUPLICATE_SCAN",
  "errors": {
    "barcode": ["Bundle has already been scanned in Line 04."]
  }
}
```

---

## ৪. ব্যাকএন্ড লেয়ার্ড আর্কিটেকচার (Layered Architecture Rules)

1. **FormRequest:** শুধুমাত্র ইনপুট ভ্যালিডেশন ও পারমিশন।
2. **API Controller:** সর্বোচ্চ ১৫-২০ লাইন কোড। কোনো ব্যবসায়িক লজিক বা সরাসরি কুয়েরি নয়।
3. **Domain Service:** ডায়নামিক প্যারামিটার লোড করে সম্পূর্ণ ব্যবসায়িক হিসাব ও `DB::transaction()` পরিচালনা।
4. **Repository:** অপ্টিমাইজড ইনডেক্সড ডাটাবেজ কোয়েরি।
5. **JsonResource / DTO:** ফ্রন্টএন্ডের জন্য ক্লিন ও অপ্রয়োজনীয় ফিল্ডমুক্ত ডাটা অবজেক্ট তৈরি।

---
**বাধ্যবাধকতা:** কোনো সার্ভিসে হার্ডকোডেড তালিকা বা বাংলা এপিআই মেসেজ রাখা যাবে না; এটি পিআর চেকলিস্টের আবশ্যিক শর্ত।
