# TraceFlow-RMG: পোস্টগ্রেসকিউএল ডাটাবেজ আর্কিটেকচার ও মাইগ্রেশন স্ট্যান্ডার্ড
**নথি কোড:** TFRMG-ENG-PG-002  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** ডাটাবেজ ইঞ্জিনিয়ারিং ও পারফরম্যান্স গাইডলাইন  
**অনুমোদনকারী:** ট্রেসফ্লো-আরএমজি ডাটাবেজ আর্কিটেকচার বোর্ড

---

## ১. ভূমিকা ও ডাটাবেজ দর্শন (Database Philosophy)

গার্মেন্টস ইআরপি সিস্টেমে ডাটাবেজ হলো কারখানার মেরুদণ্ড। একটি বড় প্রতিষ্ঠানে প্রতি মাসে মিলিয়ন মিলিয়ন বারকোড ট্র্যাকিং রেকর্ড তৈরি হয়। ভুল মডেলিং বা ত্রুটিপূর্ণ ইনডেক্সিং পুরো সফটওয়্যারকে ধীরগতির করে ফেলতে পারে। 

TraceFlow-RMG এর মূল ডাটাবেজ হিসেবে **PostgreSQL 16+** ব্যবহৃত হবে। এর শক্তিশালী ট্রানজ্যাকশন আইসোলেশন, টেবিল পার্টিশনিং এবং JSONB সাপোর্ট আমাদের হাই-কনকারেন্সি চাহিদা মেটাতে সক্ষম।

---

## ২. স্কিমা ডিজাইন ও নামকরণ রীতি (Schema Conventions)

### ২.১ নামকরণের আদর্শ নিয়ম
* **টেবিল নাম:** বহুবচনে এবং ছোট হাতের স্নেক কেসে (`snake_case`) হবে (যেমন: `buyer_orders`, `cutting_plans`, `bundle_cards`, `sewing_outputs`)।
* **প্রাইমারি কি:** বাধ্যতামূলকভাবে সময়ভিত্তিক অনুক্রমিক **`id UUID PRIMARY KEY DEFAULT gen_random_uuid()`** (বাস্তবে **UUID v7**)। কোনো ক্রমেই সাধারণ `BIGSERIAL` ব্যবহার করা যাবে না।
* **ফরেন কি:** একক টেবিল নাম + `_id` (যেমন: `cutting_plan_id`, `bundle_card_id`, `operator_id`)।
* **স্ট্যাটাস ও স্টেট কলাম:** স্ট্রিং টাইপ এবং ক্যাপিটাল স্নেক কেসে ভ্যালু থাকবে (যেমন: `PENDING`, `IN_PROGRESS`, `QC_PASSED`, `REJECTED`, `SHIPPED`)।

### ২.২ প্রতিটি টেবিলে আবশ্যিক ফিল্ডসমূহ (Audit Trail Fields)
সিস্টেমের যেকোনো গুরুত্বপূর্ণ টেবিলে নিচের কলামগুলো থাকা বাধ্যতামূলক:
```sql
created_at    TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at    TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
created_by    BIGINT REFERENCES users(id),
updated_by    BIGINT REFERENCES users(id),
deleted_at    TIMESTAMPTZ NULL -- সফট ডিলিটের জন্য
```

---

## ৩. ইনডেক্সিং ও পারফরম্যান্স টিউনিং (Indexing Strategy)

ডাটাবেজে এলোমেলো ইনডেক্স দিলে রাইট (Write) পারফরম্যান্স কমে যায়, আবার ইনডেক্স না দিলে রিড (Read) অপারেশন স্লো হয়ে পড়ে। তাই নিচের নিয়মাবলি চূড়ান্ত করা হলো:

1. **ইউনিক বারকোড ইনডেক্স:** যে ফিল্ডগুলো বারকোড রিডারে স্ক্যান হবে (`barcode`, `qr_code`, `serial_number`), সেগুলোতে `UNIQUE B-Tree` ইনডেক্স বাধ্যতামূলক।
2. **কম্পোজিট ইনডেক্স (Composite Indexes):** যেসব কোয়েরিতে একাধিক কলাম দিয়ে ফিল্টার হয় (যেমন: নির্দিষ্ট লাইনে নির্দিষ্ট তারিখের আউটপুট):
   ```sql
   CREATE INDEX idx_sewing_line_date ON sewing_production_logs (sewing_line_id, production_date, status);
   ```
3. **ফরেন কি ইনডেক্সিং:** পোস্টগ্রেস নিজে থেকে ফরেন কি-তে ইনডেক্স তৈরি করে না। তাই প্রতিটি ফরেন কি কলামে ইনডেক্স তৈরি নিশ্চিত করতে হবে, যাতে ক্যাসকেড বা জয়েন কোয়েরি দ্রুত হয়।

---

## ৪. হাই-ভলিউম ডাটাবেজ পার্টিশনিং (Table Partitioning for Production Logs)

পোশাক কারখানায় প্রতিদিন যে বিপুল পরিমাণ বারকোড স্ক্যান রেকর্ড তৈরি হয় (`bundle_scans`, `sewing_production_logs`), সেগুলোকে একক টেবিলে রাখলে ১-২ বছরের মধ্যে কুয়েরি স্লো হয়ে যাবে।

* **পার্টিশনিং কৌশল:** রেঞ্জ পার্টিশনিং (Range Partitioning by Month or Year)।
* **বাস্তবায়ন:**
  ```sql
  CREATE TABLE sewing_production_logs (
      id UUID NOT NULL,
      sewing_line_id UUID NOT NULL,
      bundle_id UUID NOT NULL,
      scanned_at TIMESTAMPTZ NOT NULL,
      status VARCHAR(50) NOT NULL,
      PRIMARY KEY (id, scanned_at)
  ) PARTITION BY RANGE (scanned_at);

  -- ২০২৬ সালের মাসিক পার্টিশনের উদাহরণ
  CREATE TABLE sewing_logs_2026_09 PARTITION OF sewing_production_logs
      FOR VALUES FROM ('2026-09-01') TO ('2026-10-01');
  ```

---

## ৫. নিরাপদ মাইগ্রেশন অনুশাসন (Zero-Downtime Migration Rules)

১. **কোনো ড্রপ স্টেটমেন্ট নয়:** চলমান ডাটাবেজে `DROP TABLE`, `DROP COLUMN` সরাসরি চালানো যাবে না।
২. **রিভার্সিবল মাইগ্রেশন:** প্রতিটি মাইগ্রেশন ফাইলে একটি কার্যকরী `down()` মেথড থাকতে হবে, যাতে কোনো জটিলতা দেখা দিলে কমান্ড দিয়ে পূর্বের অবস্থায় ফিরে যাওয়া যায়।
3. **লকিং সেফটি:** বড় টেবিলে নতুন কলাম যোগ করার সময় ডিফল্ট ভ্যালু সাবধানে সেট করতে হবে, যেন তা পুরো টেবিলে রিড-রাইট লক ফেলে প্রোডাকশন ডাউন না করে।

---
**প্রয়োগ ক্ষেত্র:** ডাটাবেজ মাইগ্রেশন স্ক্রিপ্ট তৈরি এবং পিআর রিভিউর সময় চিফ ডিবিএ এই নথিটি অনুযায়ী অডিট সম্পন্ন করবেন।
