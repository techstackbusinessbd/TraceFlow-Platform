# TraceFlow-RMG: এসকিউএ টেস্ট কেস স্পেসিফিকেশন টেমপ্লেট
**টেস্ট কেস আইডি:** `TC-[MODULE]-[XXX]`  
**টেস্টের নাম:** `[e.g., Prevent Concurrent Duplicate Scan on Same Bundle]`  
**মডিউল:** `[MOD-01-CUTTING / MOD-02-SEWING / MOD-03-PACKING]`  
**টেস্ট লেভেল:** UNIT / FEATURE / E2E / LOAD TEST  
**প্রাধান্য (Priority):** P0 (Blocker) / P1 (Critical) / P2 (Normal)

---

## ১. প্রি-কন্ডিশন ও টেস্ট ডেটা
* **প্রি-কন্ডিশন:** সিস্টেমে একটি সক্রিয় বান্ডেল কার্ড (`READY_FOR_SEWING`) তৈরি থাকতে হবে।
* **টেস্ট ইনপুট ডাটা:**
  - `barcode`: `TF-CUT-004-B042`
  - `sewing_line_id`: `01923e4b-7a12-7000-8000-000000000001`

---

## ২. টেস্ট স্টেপস ও প্রত্যাশিত ফলাফল (Test Execution Steps)

| ধাপ | টেস্ট অ্যাকশন | প্রত্যাশিত ফলাফল | ফলাফল (Pass/Fail) |
|:---:|---|---|:---:|
| ১ | সঠিক টোকেনসহ প্রথম স্ক্যান রিকোয়েস্ট প্রেরণ | HTTP 201 Created রেসপন্স ও স্ট্যাটাস `IN_SEWING` | PASS |
| ২ | সাথে সাথে একই বারকোড দিয়ে দ্বিতীয় স্ক্যান প্রেরণ | HTTP 422 Unprocessable ও `ERR_DUPLICATE_SCAN` | PASS |
| ৩ | ভুল লাইনের বান্ডেল আইডি দিয়ে স্ক্যান প্রেরণ | লাল ভিজ্যুয়াল ফ্ল্যাশ ও `Line mismatch` এরর | PASS |

---

## ৩. অটোমেটেড টেস্ট স্ক্রিপ্ট রেফারেন্স
* **ফাইল অবস্থান:** `tests/Feature/[Module]/[TestName]Test.php`
* **রান কমান্ড (Docker-Only):** `docker compose exec backend php artisan test --filter=[TestName]` (হোস্ট লোকাল পিসিতে সরাসরি রান করা নিষিদ্ধ)
