# TraceFlow-RMG: সেন্ট্রালাইজড স্ট্যাটিক ডাটা ও কনস্ট্যান্টস ম্যানেজমেন্ট আর্কিটেকচার
**Document Code:** TFRMG-ARCH-STA-001  
**Version:** 5.0.0  
**Effective Date:** 2026-09-24  
**Classification:** Enterprise Static Data Management, Enums, Zero Ad-hoc Strings  
**Approved By:** Principal Software Architect & Lead Backend Engineer  

---

## ১. সমস্যা ও প্রেক্ষাপট (The Problem & Context)

তৈরি পোশাক কারখানায় কিছু ডাটা থাকে যা কখনোই ঘন ঘন পরিবর্তিত হয় না এবং যার জন্য ডাটাবেজে আলাদা টেবিল তৈরি করা অপচয় ও পারফরম্যান্স ওভারহেড। 
যেমন:
- **ফ্যাক্টরি শিফট (Shifts):** `DAY_SHIFT` (General), `NIGHT_SHIFT` (Overtime/OT), `EVENING_SHIFT` (সর্বমোট ২ বা ৩টি)।
- **জেন্ডার (Gender / Buyer Division):** `MEN`, `WOMEN`, `BOYS`, `GIRLS`, `UNISEX`।
- **শিপমেন্ট টার্মস (Incoterms):** `FOB` (Free on Board), `CIF` (Cost, Insurance & Freight), `C&F`।
- **পেমেন্ট টাইপ:** `AT_SIGHT_LC`, `DEFERRED_LC`, `TT`।
- **সাইজ স্কেল টাইপ:** `ALPHA` (S, M, L, XL), `NUMERIC` (28, 30, 32, 34)।

### কেন আলাদা ডাটাবেজ টেবিল অপ্রয়োজনীয়?
1. শিফট বা ইনকোটার্মসের জন্য ডাটাবেজে টেবিল বানিয়ে প্রতি রিকোয়েস্টে ডাটাবেজ `JOIN` বা কোয়েরি চালানো অপচয়।
2. যদি বিভিন্ন ফাইলে বা বিভিন্ন কম্পোনেন্টে ডেভেলপাররা মুখস্থ স্ট্রিং (`'day'`, `'night'`, `'Morning'`) টাইপ করে, তবে টাইপো হয়ে ডাটা করাপ্ট হয়।
3. **সমাধান:** **একক সেন্ট্রালাইজড সোর্স অব ট্রুথ (Single Centralized Static Enum & Dictionary Engine)**।

---

## ২. সেন্ট্রালাইজড আর্কিটেকচার (Single Source of Truth)

সিস্টেমের সমস্ত অপরিবর্তনশীল স্ট্যাটিক ডাটা ব্যাকএন্ড এবং ফ্রন্টএন্ডে কীভাবে এক জায়গায় সংজ্ঞায়িত ও ব্যবহৃত হবে, তার দ্বিমুখী লেআউট:

```
[Backend: PHP 8.3 / Laravel 13]                   [Frontend: React 19 / TypeScript]
backend/app/Shared/Constants/                      frontend/src/shared/constants/
├── Shifts.php (Enum with label & hours)  ◄───►    ├── shifts.ts (Strict Types & Options)
├── Incoterms.php (FOB, CIF, C&F)                  ├── incoterms.ts
├── Genders.php (Men, Women, Kids)                 ├── genders.ts
└── PaymentTerms.php                               └── payment-terms.ts
```

---

## ৩. ব্যাকএন্ড ইমপ্লিমেন্টেশন স্ট্যান্ডার্ড (PHP 8.3 Backed Enums)

ব্যাকএন্ডে সমস্ত স্ট্যাটিক ডাটা পিএইচপি ৮.৩ এর **Typed Backed Enum** হিসেবে ডিফাইন থাকবে, যেখানে শুধু কোড নয়, সাথে হিউম্যান-রিডেবল লেবেল ও মেটাডাটা থাকবে:

### উদাহরণ: শিফট কনস্ট্যান্ট (`app/Shared/Constants/FactoryShift.php`)
```php
<?php

namespace App\Shared\Constants;

enum FactoryShift: string
{
    case GENERAL = 'GENERAL';
    case DAY = 'DAY';
    case NIGHT = 'NIGHT';

    /**
     * UI প্রদর্শনের জন্য স্ট্যান্ডার্ড ইংরেজি লেবেল
     */
    public function label(): string
    {
        return match($this) {
            self::GENERAL => 'General Shift (08:00 AM - 05:00 PM)',
            self::DAY => 'Day Shift (08:00 AM - 08:00 PM)',
            self::NIGHT => 'Night Shift (08:00 PM - 08:00 AM)',
        };
    }

    /**
     * স্ট্যান্ডার্ড কাজের সময়সীমা (ঘণ্টা)
     */
    public function standardHours(): int
    {
        return match($this) {
            self::GENERAL => 8,
            self::DAY => 10,
            self::NIGHT => 10,
        };
    }

    /**
     * ফ্রন্টএন্ড সিলেক্ট ড্রপডাউনের জন্য স্বয়ংক্রিয় অ্যারে
     */
    public static function toSelectOptions(): array
    {
        return array_map(fn(self $shift) => [
            'value' => $shift->value,
            'label' => $shift->label(),
            'hours' => $shift->standardHours(),
        ], self::cases());
    }
}
```

### ডাটাবেজ কলামে কীভাবে স্টোর হবে:
ডাটাবেজ কলামে বড় কোনো রিলেশন টেবিল লাগবে না; সরাসরি কমপ্যাক্ট `VARCHAR(20)` কলামে ভ্যালু স্টোর হবে এবং মডেলে কাস্টিং থাকবে:
```php
// Migration
$table->string('shift_code', 20)->default(FactoryShift::DAY->value);

// Eloquent Model Cast
protected function casts(): array
{
    return [
        'shift_code' => FactoryShift::class,
    ];
}
```

---

## ৪. ফ্রন্টএন্ড ইমপ্লিমেন্টেশন স্ট্যান্ডার্ড (TypeScript Constant Dictionary)

ফ্রন্টএন্ডের সমস্ত ড্রপডাউন ও গ্রিডে ব্যবহারের জন্য সেন্ট্রাল ডিরেক্টরি `frontend/src/shared/constants/` এ টাইপ-সেফ ডিকশনারি থাকবে:

### উদাহরণ: শিফট ডিকশনারি (`frontend/src/shared/constants/shifts.ts`)
```typescript
export const FACTORY_SHIFTS = {
  GENERAL: {
    code: 'GENERAL',
    label: 'General Shift',
    timeRange: '08:00 AM - 05:00 PM',
    standardHours: 8,
    badgeVariant: 'neutral',
  },
  DAY: {
    code: 'DAY',
    label: 'Day Shift (OT)',
    timeRange: '08:00 AM - 08:00 PM',
    standardHours: 10,
    badgeVariant: 'info',
  },
  NIGHT: {
    code: 'NIGHT',
    label: 'Night Shift',
    timeRange: '08:00 PM - 08:00 AM',
    standardHours: 10,
    badgeVariant: 'warning',
  },
} as const;

export type FactoryShiftCode = keyof typeof FACTORY_SHIFTS;

// ফ্রন্টএন্ড ড্রপডাউন সিলেক্টরের জন্য রেডি অপশনস
export const SHIFT_OPTIONS = Object.values(FACTORY_SHIFTS).map((shift) => ({
  value: shift.code,
  label: `${shift.label} (${shift.timeRange})`,
}));
```

---

## ৫. সেন্ট্রালাইজড মেটাডাটা এপিআই (Zero Drift Policy)

যদি কখনো কোনো নতুন ক্লায়েন্ট বা মোবাইল অ্যাপের জন্য এই স্ট্যাটিক ডাটার তালিকা এপিআই থেকে পাওয়ার প্রয়োজন হয়, তবে সেন্ট্রাল এপিআই থেকে সরাসরি ইন-মেমোরি লোড হবে (০ ডাটাবেজ কোয়েরি):

```
GET /api/v1/system/static-dictionaries
```
**রেসপন্স:**
```json
{
  "success": true,
  "data": {
    "shifts": [
      { "code": "GENERAL", "label": "General Shift (08:00 AM - 05:00 PM)", "hours": 8 },
      { "code": "DAY", "label": "Day Shift (08:00 AM - 08:00 PM)", "hours": 10 },
      { "code": "NIGHT", "label": "Night Shift (08:00 PM - 08:00 AM)", "hours": 10 }
    ],
    "incoterms": [
      { "code": "FOB", "label": "Free on Board" },
      { "code": "CIF", "label": "Cost, Insurance and Freight" }
    ]
  }
}
```

---

## ৬. কঠোর কোডিং ইনভ্যারিয়েন্টস (Strict Invariants)

1. **জিরো র্যান্ডম স্ট্রিংস (No Magic Strings):** কন্ট্রোলার, সার্ভিস, বা ফ্রন্টএন্ড ফাইলে কখনো কাঁচা স্ট্রিং `'DAY'`, `'NIGHT'`, `'FOB'` লেখা সম্পূর্ণ নিষিদ্ধ। সর্বদা `FactoryShift::DAY->value` (PHP) অথবা `FACTORY_SHIFTS.DAY.code` (TS) ব্যবহার করতে হবে।
2. **ডাটাবেজ ব্লট রোধ (No Table Bloat):** যে ডাটা বছরে বা দশ বছরেও পরিবর্তন হয় না (Shift, Gender, Unit of Measure), তার জন্য ডাটাবেজে আলাদা টেবিল বা মাইগ্রেশন তৈরি করা নিষিদ্ধ।
3. **টাইপ সেফটি ও অটো-কমপ্লিশন:** ব্যাকএন্ড ও ফ্রন্টএন্ড উভয়েই IDE অটো-কমপ্লিট এবং কম্পাইল-টাইম টাইপ চেকিং নিশ্চিত থাকতে হবে।
