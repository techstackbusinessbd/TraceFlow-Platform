# TraceFlow-RMG: নামকরণ রীতি ও এন্টারপ্রাইজ কনভেনশন
**নথি কোড:** TFRMG-ENG-NC-006  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** কোডিং কনভেনশন ও স্টাইল গাইড  
**অনুমোদনকারী:** ট্রেসফ্লো-আরএমজি কোয়ালিটি রিভিউ সেল

---

## ১. ভূমিকা (Overview)

বহুজাতিক বা বড় সফটওয়্যার প্রজেক্টে ডেভেলপারদের নিজস্ব ইচ্ছামাফিক নাম ব্যবহারের কারণে কোডবেস দ্রুত জটিল ও অপরিচ্ছন্ন হয়ে পড়ে। TraceFlow-RMG প্রজেক্টে ব্যাকএন্ড (PHP/Laravel), ফ্রন্টএন্ড (React/TS), ডাটাবেজ (PostgreSQL) এবং এপিআই এন্ডপয়েন্টে একক ও অভিন্ন **নামকরণ রীতি (Naming Conventions)** কার্যকর থাকবে।

---

## ২. প্রযুক্তিভিত্তিক সুনির্দিষ্ট নামকরণের নিয়মাবলী

### ২.১ লারাভেল / পিএইচপি ব্যাকএন্ড (Laravel / PHP)
| উপাদানের ধরন | কেস কনভেনশন | উদাহরণ |
|---|---|---|
| **মডেল ক্লাস (Model)** | PascalCase (একবচন) | `BundleCard`, `SewingOutput`, `BuyerOrder` |
| **কন্ট্রোলার (Controller)** | PascalCase + Controller | `BundleScanController`, `CuttingPlanController` |
| **সার্ভিস ক্লাস (Service)** | PascalCase + Service | `BundleTrackingService`, `EfficiencyCalculationService` |
| **ফর্ম রিকোয়েস্ট (Request)**| PascalCase + Request | `StoreBundleScanRequest`, `UpdateCutStatusRequest` |
| **মাইগ্রেশন ফাইল (Migration)**| snake_case (তারিখসহ) | `2026_09_24_000001_create_bundle_cards_table.php` |
| **মেথড ও ফাংশন** | camelCase | `processBundleScan()`, `calculateLineEfficiency()` |
| **ভেরিয়েবল** | camelCase | `$bundleCard`, `$totalHourlyOutput` |
| **কনস্ট্যান্ট (Constants)** | UPPER_SNAKE_CASE | `STATUS_IN_SEWING`, `MAX_HOURLY_TARGET` |

### ২.২ রিঅ্যাক্ট ফ্রন্টএন্ড (React / TypeScript / JS)
| উপাদানের ধরন | কেস কনভেনশন | উদাহরণ |
|---|---|---|
| **কম্পোনেন্ট ফাইল** | PascalCase | `BundleScanner.tsx`, `AndonDisplayBoard.tsx` |
| **কাস্টম হুকস (Custom Hook)** | camelCase (use দিয়ে শুরু) | `useBarcodeScanner.ts`, `useSoundAlert.ts` |
| **ইউটিলিটি ও হেল্পার** | camelCase | `formatProductionTime.ts`, `calculateEfficiency.ts` |
| **ইভেন্ট হ্যান্ডলার মেথড** | handle দিয়ে শুরু (camelCase)| `handleBarcodeScan()`, `handleStatusChange()` |
| **গ্লোবাল স্টেট / স্লাইস** | camelCase + Store/Slice | `useCuttingStore.ts`, `authSlice.ts` |

### ২.৩ পোস্টগ্রেস ডাটাবেজ (PostgreSQL)
| উপাদানের ধরন | কেস কনভেনশন | উদাহরণ |
|---|---|---|
| **টেবিল নাম (Table)** | snake_case (বহুবচন) | `cutting_plans`, `bundle_cards`, `sewing_lines` |
| **কলাম নাম (Column)** | snake_case | `bundle_number`, `scanned_at`, `hourly_target` |
| **প্রাইমারি কি (PK)** | snake_case | `id` (বিগ-ইন্ট অথবা ইউইউআইডি) |
| **ফরেন কি (FK)** | snake_case (একবচন + `_id`) | `bundle_card_id`, `sewing_line_id` |
| **ইনডেক্স নাম (Index)** | idx / uidx + টেবিল + কলাম | `idx_bundle_cards_barcode`, `uidx_bundle_card_serial` |

---

## ৩. ডোমেইন টার্মিনোলজির সুসংগত ব্যবহার (Consistency Guardrails)

গার্মেন্টস ডোমেইনের ক্ষেত্রে একই জিনিসকে বিভিন্ন নামে ডাকা যাবে না। পুরো কোডবেসে নিচে উল্লেখিত প্রমিত শব্দগুলোই ব্যবহার করতে হবে:

* ❌ `cut_piece`, `garment_unit`, `piece_item` $\rightarrow$ ✅ **`piece`** অথবা **`bundle`**
* ❌ `sewing_speed`, `line_speed` $\rightarrow$ ✅ **`smv`** (Standard Minute Value)
* ❌ `defect_cloth`, `alter_piece` $\rightarrow$ ✅ **`defect`** অথবা **`alteration`**
* ❌ `order_number`, `job_no` $\rightarrow$ ✅ **`po_number`** (Purchase Order)

---
**বাধ্যবাধকতা:** স্ট্যাটিক কোড অ্যানালাইসিস এবং পিআর রিভিউর সময় এই কনভেনশনের বিচ্যুতি ঘটলে পিআর অটো-ব্লক হবে।
