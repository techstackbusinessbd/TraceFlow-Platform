# TraceFlow-RMG: ডাটাবেজ আর্কিটেকচার, স্কিমা ডিজাইন ও ডিবিএ গাইড
**নথি কোড:** TFRMG-DATA-DB-001  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** ডাটাবেজ ইঞ্জিনিয়ারিং ও এন্টারপ্রাইজ স্কিমা স্ট্যান্ডার্ড  
**প্রযোজ্য ইঞ্জিন:** PostgreSQL 16+, Redis 7+ Alpine  
**অনুমোদনকারী:** লিড ডাটাবেজ আর্কিটেক্ট ও ডিবিএ সেল (Gate 3 Passed)

---

## ১. ভূমিকা ও ডাটাবেজ ইঞ্জিনিয়ারের মূল দায়িত্ব (Core Mission)

TraceFlow-RMG ডাটাবেজ ইঞ্জিনিয়ারের প্রধান লক্ষ্য হলো উচ্চ-কনকারেন্সি আরএমজি কারখানায় কোটি কোটি বারকোড স্ক্যান ডাটা নিরাপদে সংরক্ষণ করা, সাব-মিলিসেকেন্ড ইনডেক্সিং গতি বজায় রাখা এবং জিরো ডাটা লস নিশ্চিত করা।

### প্রধান টেকনিক্যাল নীতিসমূহ:
1. **১০০% ডায়নামিক সেন্ট্রালাইজড কনফিগ:** ডাটাবেজে কোনো ফিক্সড হার্ডকোডেড `ENUM` থাকবে না। সমস্ত স্ট্যাটাস ও ড্রপডাউন `lookup_categories` ও `lookup_values` টেবিল থেকে পরিচালিত হবে।
2. **মাসিক রেঞ্জ পার্টিশনিং:** হাই-ভলিউম টেবিলগুলো (`bundle_cards`, `sewing_production_logs`, `carton_items`) স্বয়ংক্রিয়ভাবে টাইমস্ট্যাম্প অনুযায়ী পার্টিশন হবে।
3. **কনকারেন্সি ও অ্যান্টি-রেস কন্ডিশন:** ইউনিক কম্পোজিট ইনডেক্স ও রো-লেভেল পেসিমিস্টিক লক (`FOR UPDATE`) দিয়ে ডুপ্লিকেট স্ক্যান সম্পূর্ণ প্রতিরোধ।
4. **অপরিবর্তনীয় অডিট ট্রেইল:** প্রতিটি টেবিলে `created_by`, `updated_by`, `created_at`, `updated_at` এবং সফট ডিলিটের জন্য `deleted_at` বাধ্যতামূলক।

---

## ২. কোর স্কিমা আর্কিটেকচার ও ডিডিএল (Production DDL Schema)

### ২.১ সেন্ট্রালাইজড ডায়নামিক কনফিগারেশন স্কিমা (`lookup_categories` & `lookup_values`)
```sql
-- ১. ক্যাটাগরি মাস্টার
CREATE TABLE lookup_categories (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(), -- UUID v7
    category_code VARCHAR(50) UNIQUE NOT NULL, -- e.g., 'BUNDLE_STATUSES', 'SEWING_DEFECT_CODES'
    category_name VARCHAR(100) NOT NULL,
    is_system_locked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ২. কনফিগারেবল অপশনস (অ্যাডমিন প্যানেল থেকে নিয়ন্ত্রিত)
CREATE TABLE lookup_values (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(), -- UUID v7
    category_id UUID NOT NULL REFERENCES lookup_categories(id) ON DELETE RESTRICT,
    item_code VARCHAR(50) NOT NULL,            -- e.g., 'IN_SEWING', 'QC_ALTER', 'SEALED'
    item_label VARCHAR(100) NOT NULL,           -- Concise English label
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    metadata JSONB NULL,                        -- Additional dynamic attributes
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uidx_category_item UNIQUE (category_id, item_code)
);
CREATE INDEX idx_lookup_cat_active ON lookup_values (category_id, is_active, sort_order);
```

### ২.২ মাস্টার অর্ডার ও কালার-সাইজ ম্যাট্রিক্স স্কিমা (`orders` & `order_matrix_items`)
```sql
CREATE TABLE buyer_orders (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(), -- UUID v7
    po_number VARCHAR(100) UNIQUE NOT NULL,
    buyer_id UUID NOT NULL,
    style_id UUID NOT NULL,
    season VARCHAR(50) NOT NULL,
    target_quantity INT NOT NULL CHECK (target_quantity > 0),
    delivery_date DATE NOT NULL,
    status_id UUID NOT NULL REFERENCES lookup_values(id),
    created_by UUID NOT NULL,
    updated_by UUID NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMPTZ NULL
);

-- এক্সেল-লাইক ইন-লাইন ম্যাট্রিক্স ডাটা টেবিল
CREATE TABLE order_matrix_items (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(), -- UUID v7
    order_id UUID NOT NULL REFERENCES buyer_orders(id) ON DELETE CASCADE,
    color_code VARCHAR(50) NOT NULL,
    size_name VARCHAR(20) NOT NULL,
    allocated_quantity INT NOT NULL DEFAULT 0 CHECK (allocated_quantity >= 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uidx_order_color_size UNIQUE (order_id, color_code, size_name)
);
CREATE INDEX idx_matrix_order_id ON order_matrix_items(order_id);
```

### ২.৩ কাটিং ও পার্টিশনড প্রোডাকশন লগ স্কিমা (`sewing_production_logs`)
```sql
-- মাসিক রেঞ্জ পার্টিশনিং টেবিল (মিলিয়ন মিলিয়ন স্ক্যান ডাটার জন্য)
CREATE TABLE sewing_production_logs (
    id UUID NOT NULL,                          -- UUID v7
    sewing_line_id UUID NOT NULL,
    bundle_id UUID NOT NULL,
    piece_serial INT NOT NULL,
    operator_id UUID NOT NULL,
    scanned_at TIMESTAMPTZ NOT NULL,
    status_id UUID NOT NULL REFERENCES lookup_values(id),
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id, scanned_at)
) PARTITION BY RANGE (scanned_at);

-- ইউনিক এন্টি-রেস ইনডেক্স
CREATE UNIQUE INDEX uidx_prod_bundle_piece_time 
ON sewing_production_logs (bundle_id, piece_serial, scanned_at);

-- ২০২৬ সালের মাসিক পার্টিশন উদাহরণ
CREATE TABLE sewing_logs_2026_09 PARTITION OF sewing_production_logs
    FOR VALUES FROM ('2026-09-01') TO ('2026-10-01');
CREATE TABLE sewing_logs_2026_10 PARTITION OF sewing_production_logs
    FOR VALUES FROM ('2026-10-01') TO ('2026-11-01');
```

---

## ৩. কুয়েরি এক্সিকিউশন ও পারফরম্যান্স টিউনিং (PostgreSQL 16+ Performance)

1. **ইন্ডেক্স স্ক্যানিং গ্যারান্টি:** ফ্লোরে বারকোড দিয়ে যে কুয়েরিগুলো হবে, সেগুলোতে বাধ্যতামূলক B-Tree ইন্ডেক্স স্ক্যান থাকতে হবে (`EXPLAIN ANALYZE` দিয়ে ভেরিফাইড)।
2. **N+1 কুয়েরি প্রতিরোধ:** কোনো ফরেন কি কলাম ইনডেক্স ছাড়া রাখা যাবে না।
3. **কানেকশন পুলিং:** হাই-কনকারেন্সি ট্রানজ্যাকশন নিশ্চিত করতে PgBouncer বা লারাভেলের কানেকশন পুল ব্যবহৃত হবে।

---

## ৪. ব্যাকআপ, ডিজাস্টার রিকভারি ও PITR পলিসি

* **পয়েন্ট-ইন-টাইম রিকভারি (PITR):** প্রতি মিনিটের WAL (Write-Ahead Logging) আর্কাইভ ব্যাকআপ ক্লাউড স্টোরেজে (MinIO/S3) পুশ হবে।
* **জিরো ডাটা লস:** কোনো সার্ভার ফেইলিয়র হলেও সর্বোচ্চ ৬০ সেকেন্ডের পূর্ববর্তী অবস্থায় ডাটাবেজ অবিকল রিস্টোর করা সম্ভব।

---
**গেট ৩ অনুমোদন:** ডাটাবেজ আর্কিটেকচার সেল কর্তৃক অনুমোদিত এবং মাইগ্রেশন স্ক্রিপ্টে অন্তর্ভুক্তির জন্য প্রস্তুত।
