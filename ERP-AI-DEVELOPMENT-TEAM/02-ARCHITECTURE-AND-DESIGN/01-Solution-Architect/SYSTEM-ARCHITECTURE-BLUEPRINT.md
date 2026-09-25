# TraceFlow-RMG: হাই-লেভেল সিস্টেম আর্কিটেকচার ব্লুপ্রিন্ট (DDD & Modular Enterprise)
**নথি কোড:** TFRMG-ARCH-HLD-001  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** সিস্টেম আর্কিটেকচার ও এন্টারপ্রাইজ ব্লুপ্রিন্ট  
**অনুমোদনকারী:** প্রিন্সিপাল সফটওয়্যার আর্কিটেক্ট (Gate 2 Passed)

---

## ১. আর্কিটেকচারাল দর্শন ও মূল স্তম্ভ (System Architecture Philosophy)

TraceFlow-RMG একটি হাই-কনকারেন্সি রিয়েল-টাইম প্রোডাকশন ইআরপি। কারখানার ফ্লোরে একসাথে শত শত টার্মিনাল থেকে বারকোড স্ক্যানিং, অ্যান্ডন বোর্ডে লাইভ ডেটা ব্রডকাস্টিং এবং ব্যাক-অফিসে কোটি কোটি ডেটা প্রসেসিংয়ের চাপ সামলাতে সিস্টেমটিকে **মডুলার ডোমেইন-ড্রিভেন ডিজাইন (Modular DDD) ও ইভেন্ট-ড্রিভেন আর্কিটেকচার (EDA)** কাঠামোয় রূপ দেওয়া হয়েছে।

### আর্কিটেকচারের প্রধান ৪টি স্তম্ভ:
1. **ডোমেইন বাউন্ডেড কনটেক্সট (Domain Separation):** কাটিং, সুইং, ফিনিশিং ও মাস্টার ডেটার মতো ডোমেইনগুলো সম্পূর্ণ স্বাধীন ও মডুলার থাকবে।
2. **জিরো-হার্ডকোড ডায়নামিক কনফিগ ইঞ্জিন:** ব্যাকএন্ড ও ফ্রন্টএন্ডের সমস্ত ড্রপডাউন, স্ট্যাটাস ফ্লো ও সিস্টেম রুলস ডাটাবেজের `system_parameters` ও `lookup_values` থেকে ক্যাশড হয়ে লোড হবে।
3. **সাব-২০০ মিলিসেকেন্ড স্ক্যানিং রেসপন্স:** প্রোডাকশন ফ্লোরে স্ক্যান করার সাথে সাথে মিলি-সেকেন্ড ব্যবধানে প্রসেস হবে এবং রেস কন্ডিশন মুক্ত থাকবে।
4. **প্রিমিয়াম মাইক্রো-ইন্টারঅ্যাক্টিভ ফ্রন্টএন্ড:** রিঅ্যাক্ট ভিত্তিক ইন্টারফেস যা রিয়েল-টাইম ওয়েবসকেট (Reverb) সিঙ্কে স্বয়ংক্রিয়ভাবে লাইভ ডেটা রিফ্রেশ করবে।

---

## ২. সামগ্রিক সিস্টেম ট্রানজ্যাকশন ও ডেটা ফ্লো (End-to-End Topology)

```
                       ┌────────────────────────────────────────────────────────┐
                       │   React Enterprise SPA & Floor Scanner Terminals (UI)  │
                       └───────────────────────────┬────────────────────────────┘
                                                   │ HTTPS / WSS (Sanctum Tokens)
                                                   ▼
                       ┌────────────────────────────────────────────────────────┐
                       │          Nginx Reverse Proxy & SSL Termination         │
                       └───────────────────────────┬────────────────────────────┘
                                                   │
                                                   ▼
                       ┌────────────────────────────────────────────────────────┐
                       │         Laravel 13 API Engine (PHP 8.3 FPM)            │
                       │   FormRequest ──► Controller ──► Domain Service Layer   │
                       └─────────────┬────────────────────────────┬─────────────┘
                                     │                            │
             ┌───────────────────────┴────────┐          ┌────────┴────────────────────────┐
             ▼                                ▼          ▼                                 ▼
   [ PostgreSQL 16+ Engine ]           [ Redis 7+ Cache ]  [ Laravel Horizon Queue ]   [ Laravel Reverb ]
   • Range Partitioned Logs            • Dynamic Lookups   • Barcode Sheet PDF Gen     • Real-time WebSockets
   • B-Tree Unique Barcode Indexes     • Distributed Locks • Nightly Audits & Sync     • Live Andon Display
   • Immutable Audit Trails            • Floor Session     • Cloud Backups             • Push Notifications
```

---

## ৩. ডোমেইন-ড্রিভেন বাউন্ডেড কনটেক্সট (Bounded Contexts)

```
┌────────────────────────────────────────────────────────────────────────┐
│                   TRACEFLOW-RMG BOUNDED CONTEXTS                       │
├────────────────────────────────────────────────────────────────────────┤
│ 1. Master Data & Admin Context   : Buyer, Style, Lines, Lookups, Config│
│ 2. Cutting & Marker Context      : Fabric, Lay, Plies, Bundle Cards   │
│ 3. Sewing Production Context     : Line Input, Operations, Endline QC │
│ 4. Quality & Defect Context      : Defect Library, Alteration Routing │
│ 5. Finishing & Packing Context   : Ironing, Ratio Packing, Carton Seal│
│ 6. Observability & Audit Context : Audit Trails, Andon Engine, Logs    │
└────────────────────────────────────────────────────────────────────────┘
```

---

## ৪. রিয়েল-টাইম কনকারেন্সি ও ডিস্ট্রিবিউটেড লকিং (Concurrency & Anti-Race Guardrails)

ফ্লোরে শত শত সুইং লাইনে একই সময়ে একই বান্ডেল বা পিস কার্ড স্ক্যান করার চেষ্টা করলে ডাটা ডুপ্লিকেশন রোধে ৩-স্তর বিশিষ্ট নিরাপত্তা ব্যবস্থা থাকবে:

1. **ডাটাবেজ ইউনিক কনস্ট্রেইন্ট:**
   ```sql
   ALTER TABLE sewing_production_logs ADD CONSTRAINT uidx_bundle_operation_pass 
   UNIQUE (bundle_id, operation_id);
   ```
2. **রেডিস ডিস্ট্রিবিউটেড লক (Pessimistic Floor Lock):**
   ```php
   $lock = Cache::lock("bundle_scan_{$barcode}", 5); // ৫ সেকেন্ডের তাৎক্ষণিক লক
   if ($lock->get()) {
       try {
           return $this->processScan($barcode);
       } finally {
           $lock->release();
       }
   }
   throw new DuplicateScanException("Scan in progress by another terminal.");
   ```

---

## ৫. সেন্ট্রালাইজড কনফিগারেশন ও ক্যাশিং আর্কিটেকচার

* **ট্যাগড ক্যাশ মেকানিজম:**
  - সমস্ত লুকআপ ক্যাটাগরি ও সিস্টেম রুলস `Cache::tags(['system_configs'])->rememberForever(...)` দিয়ে রেডিসে সেভ থাকবে।
  - যখনই অ্যাডমিন প্যানেল থেকে কোনো ড্রপডাউন অপশন বা প্যারামিটার এডিট হবে, ব্যাকএন্ড স্বয়ংক্রিয়ভাবে `Cache::tags(['system_configs'])->flush()` করবে।
  - ফলে সার্ভার রিস্টার্ট বা কোড ডেপ্লয়মেন্ট ছাড়াই পুরো সিস্টেম রিয়েল-টাইমে নতুন নিয়ম মেনে চলতে শুরু করবে।

---

## ৬. ডিরেক্টরি ও ফাইল আর্কিটেকচার (Modular DDD Structure)

সিস্টেমের ব্যাকএন্ড ও ফ্রন্টএন্ডের বাউন্ডেড কনটেক্সট ডিরেক্টরি, সার্ভিস লেয়ার ও ফিচার-স্লাইসড ফাইল স্ট্রাকচারের পূর্ণ বিবরণ দেখতে দেখুন:  
👉 **[PROJECT-DIRECTORY-AND-FILE-STRUCTURE.md](file:///d:/ERP/TraceFlow-RMG/ERP-AI-DEVELOPMENT-TEAM/02-ARCHITECTURE-AND-DESIGN/01-Solution-Architect/PROJECT-DIRECTORY-AND-FILE-STRUCTURE.md)**

---
**বাধ্যবাধকতা:** সিস্টেম আর্কিটেকচার বোর্ডের অনুমোদন ব্যতীত কোনো নতুন থার্ড-পার্টি সার্ভিস বা আর্কিটেকচারাল লেয়ার কোডবেসে যুক্ত করা যাবে না।
