# TraceFlow-RMG: এআই এজেন্ট আউটপুট স্কিমাস ও ভ্যালিডেশন
**নথি কোড:** TFRMG-AI-SCH-007  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** এআই ডাটা কন্ট্রাক্ট ও আউটপুট স্কিমা  
**অনুমোদনকারী:** এআই কোয়ালিটি বোর্ড

---

## ১. ভূমিকা (Structured JSON Output Standard)

বিভিন্ন এজেন্টের মধ্যে ডাটা হস্তান্তরের সময় কোনো অনির্দিষ্ট টেক্সটের বদলে সুনির্দিষ্ট ও টাইপ-সেফ **জেসন স্কিমা (JSON Schema)** মেনে ডাটা হ্যান্ডঅফ নিশ্চিত করতে হবে।

---

## ২. কোর হ্যান্ডঅফ জেসন স্কিমাস (JSON Schemas)

### ২.১ `AI-BA` $\rightarrow$ `AI-Architect` হ্যান্ডঅফ স্কিমা
```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "title": "BA_To_Architect_Handoff",
  "type": "object",
  "properties": {
    "module_id": { "type": "string" },
    "srs_filepath": { "type": "string" },
    "ui_pattern": { "type": "string", "enum": ["SLIDE_OVER_DRAWER_AND_INLINE_GRID", "STANDARD_DATA_TABLE"] },
    "ui_language": { "type": "string", "const": "EN" },
    "dynamic_lookups_required": { "type": "array", "items": { "type": "string" } },
    "is_gate_1_signed_off": { "type": "boolean", "const": true }
  },
  "required": ["module_id", "srs_filepath", "ui_pattern", "ui_language", "is_gate_1_signed_off"]
}
```

### ২.২ `AI-Architect` $\rightarrow$ `AI-Database` & `AI-Dev` হ্যান্ডঅফ স্কিমা
```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "title": "Architect_To_Dev_Handoff",
  "type": "object",
  "properties": {
    "module_id": { "type": "string" },
    "primary_key_type": { "type": "string", "const": "UUID_V7" },
    "partitioning_required": { "type": "boolean" },
    "inline_css_allowed": { "type": "boolean", "const": false },
    "concurrency_lock": { "type": "string", "enum": ["REDIS_DISTRIBUTED_LOCK_AND_PESSIMISTIC_ROW_LOCK"] },
    "is_gate_2_signed_off": { "type": "boolean", "const": true }
  },
  "required": ["module_id", "primary_key_type", "inline_css_allowed", "concurrency_lock", "is_gate_2_signed_off"]
}
```

---
**প্রয়োগ ক্ষেত্র:** এজেন্ট ট্রানজিশনের সময় জেসন স্কিমা ভ্যালিডেটর এই ফিল্ডগুলো কঠোরভাবে যাচাই করবে।
