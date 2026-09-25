# TraceFlow-RMG: রেস্টফুল এপিআই স্পেসিফিকেশন ও কন্ট্রাক্ট টেমপ্লেট
**এপিআই কোড:** `API-[MODULE]-[XXX]`  
**এন্ডপয়েন্ট নাম:** `[e.g., Scan Bundle Card at Sewing Line Input]`  
**ভার্সন:** `v1`  
**এইচটিটিপি মেথড:** `POST`  
**ইউআরএল পাথ:** `/api/v1/{module}/{resource}`  
**অথেনটিকেশন গার্ড:** `Bearer <Sanctum_Token>` (Strict Multi-Guard)  
**প্রযোজ্য আরবিএসি পারমিশন:** `[e.g., sewing.scan_bundle]`  
**রেট লিমিট (Throttling):** `120 requests/minute per device`  

---

## ১. ওভারভিউ ও বিজনেস লজিক (API Overview & Intent)
* **উদ্দেশ্য:** কাটিং সেকশন থেকে সম্পন্ন হয়ে আসা বান্ডেল কার্ড সুইং লাইনে লোড করার সময় প্রথম স্ক্যানে সিস্টেমে রেকর্ড করা।
* **ইভেন্ট ট্রিগার:** রিয়েল-টাইম অ্যান্ডন বোর্ডে WebSockets ইভেন্ট ডিসপ্যাচ (`BundleLoadedEvent`)।
* **আইডেমপোটেন্সি (Idempotency):** একই বান্ডেল বারকোড পরপর একাধিকবার স্ক্যান করলে রেস-কন্ডিশন গার্ডরেইল (Redis Atomic Lock) দ্বারা ডুপ্লিকেট স্ক্যান প্রতিরোধ।

---

## ২. রিকোয়েস্ট স্পেসিফিকেশন (Request Specification)

### হেডার্স (Mandatory Request Headers)
* `Accept: application/json`
* `Content-Type: application/json`
* `Authorization: Bearer <Sanctum_Token>`
* `X-Factory-Unit-ID: <UUID_v7>` (Tenant & Scoping Header)
* `X-Device-UUID: <String>` (Hardware Terminal Tracking)

### রিকোয়েস্ট বডি (JSON Payload Definition)
```json
{
  "barcode": "string (required, regex: ^TF-BND-[A-Z0-9]{8,16}$)",
  "sewing_line_id": "UUID v7 (required, exists:factory_lines,id)",
  "scanned_by_operator_id": "UUID v7 (required, exists:users,id)",
  "metadata": {
    "device_battery_level": 88,
    "scanner_mode": "ZEBRA_DATAWEDGE"
  }
}
```

---

## ৩. রেসপন্স স্পেসিফিকেশন (Standard Response Contract)

সমস্ত এপিআই রেসপন্স মেসেজ প্রমিত ইংরেজিতে এবং বাহুল্যবর্জিত সংক্ষিপ্ত (Concise) হতে হবে।

### ৩.১ সফল রেসপন্স (201 Created / 200 OK)
```json
{
  "success": true,
  "message": "Bundle accepted in Line 04.",
  "data": {
    "id": "01923e4b-7a12-7000-8000-000000000042",
    "barcode": "TF-BND-2026-0042",
    "status": "IN_SEWING",
    "garment_qty": 20,
    "size": "L",
    "color": "Navy Blue",
    "style_no": "TF-POLO-88",
    "line_name": "Line 04",
    "scanned_at": "2026-09-24T15:00:00Z"
  }
}
```

### ৩.২ ভ্যালিডেশন এরর রেসপন্স (422 Unprocessable Entity)
```json
{
  "success": false,
  "message": "Duplicate scan detected.",
  "error_code": "ERR_DUPLICATE_SCAN",
  "errors": {
    "barcode": [
      "Bundle has already been scanned in Line 04 at 14:52:10."
    ]
  }
}
```

### ৩.৩ অননুমোদিত অ্যাক্সেস এরর (403 Forbidden)
```json
{
  "success": false,
  "message": "Unauthorized line scan permission.",
  "error_code": "ERR_ACCESS_DENIED",
  "errors": {}
}
```

### ৩.৪ নট ফাউন্ড এরর (404 Not Found)
```json
{
  "success": false,
  "message": "Bundle barcode not recognized.",
  "error_code": "ERR_BUNDLE_NOT_FOUND",
  "errors": {}
}
```

---

## ৪. পারফরম্যান্স ও কনকারেন্সি বেঞ্চমার্ক
* **টার্গেট লেটেন্সি (p95):** $< 150 \text{ ms}$
* **লকিং মেকানিজম:** `Cache::lock('scan:bundle:'.$barcode, 5)->block(2)`
* **অডিট ট্রেইল:** স্বয়ংক্রিয়ভাবে `created_by` (UUID v7) ও `user_agent` ব্যাকগ্রাউন্ড কিউতে অডিট টেবিলে ইনসার্ট হবে।
