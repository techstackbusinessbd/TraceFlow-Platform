# TraceFlow-RMG: সেন্ট্রালাইজড কনফিগারেশন ও ডাটাবেজ লুকআপ স্ট্যান্ডার্ড
**নথি কোড:** TFRMG-ENG-DBS-009  
**সংস্করণ:** ২.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** ডাটাবেজ অ্যাডমিনিস্ট্রেশন ও ডাইনামিক স্কিমা গাইডলাইন  
**অনুমোদনকারী:** ট্রেসফ্লো-আরএমজি ডাটাবেজ গভর্ন্যান্স সেল

---

## ১. ভূমিকা (Dynamic Schema Governance)

TraceFlow-RMG সিস্টেমে কোনো ব্যবসায়িক স্ট্যাটাস, ড্রপডাউন ভ্যালু, ওয়ার্কফ্লো পর্যায় বা প্যারামিটার ডাটাবেজ এনাম (PostgreSQL `ENUM`) বা হার্ডকোডেড ভ্যালু হিসেবে থাকবে না। 

সবকিছু সেন্ট্রালাইজড লুকআপ টেবিল এবং অ্যাডমিন প্যানেল থেকে গতিশীলভাবে (Dynamically) নিয়ন্ত্রিত হতে হবে।

---

## ২. সেন্ট্রালাইজড লুকআপ স্কিমা আর্কিটেকচার (Lookup & Dynamic Parameter Engine)

```sql
-- ১. লুকআপ ক্যাটাগরি মাস্টার টেবিল
CREATE TABLE lookup_categories (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(), -- UUID v7
    category_code VARCHAR(50) UNIQUE NOT NULL, -- e.g., 'DEFECT_TYPES', 'BUNDLE_STATUSES', 'PACKING_TYPES'
    category_name VARCHAR(100) NOT NULL,
    is_system_locked BOOLEAN DEFAULT FALSE,     -- Protects core framework categories
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ২. লুকআপ অপশন টেবিল (অ্যাডমিন প্যানেল থেকে কনফিগারযোগ্য)
CREATE TABLE lookup_values (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(), -- UUID v7
    category_id UUID NOT NULL REFERENCES lookup_categories(id) ON DELETE RESTRICT,
    item_code VARCHAR(50) NOT NULL,            -- e.g., 'IN_SEWING', 'QC_ALTER', 'SHIPPED'
    item_label VARCHAR(100) NOT NULL,           -- Concise English label, e.g., 'In Sewing'
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,             -- Toggle visibility without deleting
    metadata JSONB NULL,                        -- Additional dynamic attributes
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uidx_category_item UNIQUE (category_id, item_code)
);

-- দ্রুত লোড করার জন্য ইনডেক্স
CREATE INDEX idx_lookup_cat_active ON lookup_values (category_id, is_active, sort_order);
```

---

## ৩. ডাইনামিক সিস্টেম কনফিগারেশন রুলস

১. **জিরো হার্ডকোড:** কোনো মডিউলের জন্য নতুন স্ট্যাটাস প্রয়োজন হলে নতুন মাইগ্রেশনের প্রয়োজন নেই; অ্যাডমিন প্যানেলে লুকআপ অপশন যুক্ত করলেই ড্রপডাউন ও সিস্টেমে তা তাৎক্ষণিকভাবে কার্যকর হবে।
২. **রেডিস ক্যাশিং ও অটো-ইনভ্যালিডেশন:**
   - ফ্রিকোয়েন্টলি ব্যবহৃত লুকআপ ডাটা রেডিসে ক্যাশ থাকবে (`lookup_categories_cache`).
   - অ্যাডমিন প্যানেল থেকে কোনো অপশন পরিবর্তন বা সেভ হলে সংশ্লিষ্ট রেডিস ট্যাগ অটোমেটিক ইনভ্যালিডেট হবে, যাতে ডাটাবেজের ওপর অপ্রয়োজনীয় রিড প্রেসার না পড়ে।

---

## ৪. কুয়েরি অপ্টিমাইজেশন ও সেফটি রুলস

1. **ইগার লোডিং:** N+1 কুয়েরি প্রতিরোধে অবশ্যই রিলেশনশিপ ইগার লোড করতে হবে (`with('statusLookup')`).
2. **সিলেক্ট স্পেসিফিকেশন:** প্রোডাকশন কুয়েরিতে `SELECT *` সম্পূর্ণ নিষিদ্ধ। সুনির্দিষ্ট কলাম সিলেক্ট করতে হবে।
3. **শর্ট ট্রানজ্যাকশন:** ডাটাবেজ ট্রানজ্যাকশন মিলিসেকেন্ড দীর্ঘ হবে এবং কোনো এক্সটার্নাল এপিআই কল ট্রানজ্যাকশনের ভেতর থাকবে না।

---
**বাধ্যবাধকতা:** কোনো ডেভেলপার যদি নতুন কোনো ফিচার বা স্ট্যাটাস হার্ডকোড হিসেবে টেবিলে ইনসার্ট করার চেষ্টা করে, ডিবিএ অডিটে তা বাতিল হবে।
