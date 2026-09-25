# TraceFlow-RMG: কারিগরি ডকুমেন্টেশন আর্কিটেকচার ও এপিআই রেফারেন্স
**নথি কোড:** TFRMG-DOC-API-001  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** টেকনিক্যাল ডকুমেন্টেশন ও এপিআই ক্যাটালগ  
**প্রযোজ্য স্ট্যান্ডার্ড:** OpenAPI 3.0 / Swagger, Markdown Docs, Living Documentation  
**অনুমোদনকারী:** লিড টেকনিক্যাল রাইটার ও আর্কিটেকচার কাউন্সিল

---

## ১. ভূমিকা ও জীবন্ত ডকুমেন্টেশন নীতি (Living Documentation Policy)

TraceFlow-RMG প্ল্যাটফর্মে ডকুমেন্টেশন কোনো "কাজ শেষ হওয়ার পর তৈরি করা অতিরিক্ত আনুষ্ঠানিকতা" নয়। এটি সফটওয়্যার ডেভেলপমেন্টের সাথে সমান্তরালে হালনাগাদ হওয়া একটি **জীবন্ত সিস্টেম (Living Documentation)**। 

### মূল কারিগরি নীতিসমূহ:
1. **ওপেন-এপিআই ৩.০ (OpenAPI Specification):** সমস্ত এপিআই এন্ডপয়েন্ট সোয়াগার/ওপেন-এপিআই ফরম্যাটে স্বয়ংক্রিয়ভাবে ডকুমেন্টেড থাকবে।
2. **অল এপিআই মেসেজ ইন ইংলিশ:** সমস্ত রিকোয়েস্ট প্যারামিটার, রেসপন্স ফিল্ড এবং এরর ডেসক্রিপশন আন্তর্জাতিক প্রমিত ও সংক্ষিপ্ত ইংরেজিতে প্রকাশিত হবে।
3. **ইউআই/ইউএক্স কনসিস্টেন্সি:** ব্যবহারকারী সহায়িকা (User Manual) এবং অ্যাডমিন গাইডে প্রতিটি ইন্টারফেস স্ক্রিনশট ও ফিল্ডের নাম সিস্টেমের সংক্ষিপ্ত ইংরেজি লেবেলের (`Style No`, `Sewing Line`, `Bundle Qty`) সাথে হুবহু মিল থাকবে।

---

## ২. কোর এপিআই রেফারেন্স স্পেসিফিকেশন (Core API Reference Catalog)

### ২.১ কাটিং বান্ডেল স্ক্যান এপিআই (Cutting Bundle Scan API)
* **এন্ডপয়েন্ট:** `POST /api/v1/cutting/bundle-scan`
* **অথেনটিকেশন:** `Bearer <Sanctum_Token>`
* **রিকোয়েস্ট বডি (JSON):**
  ```json
  {
    "barcode": "TF-CUT-004-B042",
    "sewing_line_id": "01923e4b-7a12-7000-8000-000000000001"
  }
  ```
* **সফল রেসপন্স (201 Created):**
  ```json
  {
    "success": true,
    "message": "Bundle scanned successfully.",
    "data": {
      "id": "01923e4b-7a12-7000-8000-000000000042",
      "barcode": "TF-CUT-004-B042",
      "style_no": "TF-POLO-2026",
      "size": "L",
      "bundle_qty": 20,
      "status": "IN_SEWING",
      "scanned_at": "2026-09-24T14:45:00Z"
    }
  }
  ```
* **ভ্যালিডেশন এরর রেসপন্স (422 Unprocessable Content):**
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

## ৩. এন্টারপ্রাইজ রিলিজ নোটস ফ্রেমওয়ার্ক (Release Notes Template)

প্রতিটি স্প্রিন্ট রিলিজের পর টেকনিক্যাল রাইটার নিচের ফরম্যাটে রিলিজ নোট প্রকাশ করবেন:

```markdown
# Release v1.1.0 (2026-09-24)
### New Features
- [Cutting] Automated high-speed bundle card generation with dynamic prefixes.
- [Sewing] Real-time Andon display board connected via Laravel Reverb WebSockets.

### Performance & Security Optimizations
- Upgraded primary keys to sequential **UUID v7** for high-volume partition tables.
- Implemented Redis distributed lock to prevent concurrent scan race conditions.

### Breaking Changes / API Updates
- Deprecated legacy numeric ID endpoints; all resource URIs now accept UUID v7.
```

---
**প্রয়োগ ক্ষেত্র:** ডেভেলপার অনবোর্ডিং, ক্লায়েন্ট ইন্টিগ্রেশন এবং সিস্টেম ট্রাবলশুটিংয়ে এই টেকনিক্যাল ডকুমেন্টেশন একমাত্র রেফারেন্স হিসেবে গণ্য হবে।
