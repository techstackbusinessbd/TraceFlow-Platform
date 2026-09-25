# TraceFlow-RMG: প্ল্যাটফর্ম ওনার ও সিস্টেম বুটস্ট্র্যাপ আর্কিটেকচার (ADR-11)
**নথি কোড:** TFRMG-ARCH-ADR-011  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৫ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** প্ল্যাটফর্ম ওনারশিপ, মাল্টি-টেন্যান্ট রুট আইসোলেশন ও সিস্টেম বুটস্ট্র্যাপ  
**অনুমোদনকারী:** ট্রেসফ্লো-আরএমজি প্রিন্সিপাল আর্কিটেকচার কাউন্সিল  

---

## ১. প্রসঙ্গ ও সমস্যা বিবৃতি (Context & Problem Statement)
একটি এন্টারপ্রাইজ গ্রেড মাল্টি-কোম্পানি আরএমজি ইআরপি প্ল্যাটফর্মে গ্রাহকদের (গার্মেন্টস ফ্যাক্টরি বা সিস্টার কনসার্ন) ডাটাবেজ এবং একাউন্ট ছাড়াও পুরো প্ল্যাটফর্মের কেন্দ্রীয় নিয়ন্ত্রণ, স্বয়ংক্রিয় এআই টিম ম্যানেজমেন্ট এবং সুপারঅ্যাডমিন সুবিধার প্রয়োজন হয়।

সাধারণ বা সাদামাটা সিডারের (`DatabaseSeeder.php`) মাধ্যমে এডমিন বা ইউজার তৈরি করলে যেসব সমস্যা তৈরি হয়:
1. ডেমো ডাটা পার্জ (`traceflow:purge-demo`) বা রি-সিড করার সময় সুপারঅ্যাডমিন ও সিস্টেম অ্যাকাউন্ট মুছে যাওয়ার ঝুঁকি থাকে।
2. নতুন ফিচার বা মডিউল যুক্ত হওয়ার পর নতুন পারমিশনগুলো সুপারঅ্যাডমিনকে ম্যানুয়ালি প্রদান করতে হয়, যা ফ্লোর অপারেশনে বাধার সৃষ্টি করে।
3. সিস্টেমের নিজস্ব অভ্যন্তরীণ ইউজার (Backend Team, Frontend Team, DBA) এবং ফ্যাক্টরি ক্লায়েন্ট ইউজারদের মধ্যে সীমারেখা থাকে না।

---

## ২. গৃহীত সিদ্ধান্ত (Architectural Decisions)

```
┌────────────────────────────────────────────────────────────────────────┐
│                   TRACEFLOW PLATFORM HOST ENTITY                       │
│                     (Root Company: ROOT-PLATFORM)                      │
│                                                                        │
│  [Superadmin]          [Backend Team]        [Frontend Team]           │
│  Full System Bypass    API & Core Ops        UI & Component Ops        │
│                                                                        │
│  [Database Team]       [DevOps Team]         [QA Team]      [BA Team]  │
│  Postgres & Schema     CI/CD & Docker        Test & SQA     SOP & SRS  │
└────────────────────────────────────┬───────────────────────────────────┘
                                     │ Tenant Isolation Barrier
                                     ▼
┌────────────────────────────────────────────────────────────────────────┐
│               CLIENT / FACTORY SISTER CONCERNS (Tenants)               │
│  ├── Ha-Meem Group / Beximco / Standard Group (Corporate Groups)       │
│        └── Apparel Unit 1, Washing Plant, Cutting Lines                │
└────────────────────────────────────────────────────────────────────────┘
```

### [DECISION-01] ডেডিকেটেড রুট কোম্পানি (`ROOT-PLATFORM`)
- প্ল্যাটফর্মের শীর্ষ ওনার হিসেবে ডাটাবেজে একটি একক ও সংরক্ষিত কোম্পানি থাকবে:
  - **Company Name:** `TraceFlow Platform Engine`
  - **Company Code:** `ROOT-PLATFORM`
  - **Company Type:** `PLATFORM_HOST`
  - **System Protected:** এই কোম্পানি কোনো গার্মেন্টস ফ্যাক্টরি, বায়ার অর্ডার, সুইং লাইন বা কাটিং অপারেশনের জন্য ব্যবহার করা যাবে না।

### [DECISION-02] সুপারঅ্যাডমিন স্বয়ংক্রিয় পারমিশন বাইপাস (Superadmin Auto-Sync)
- সুপারঅ্যাডমিনের জন্য কোনো নির্দিষ্ট পারমিশন ডাটাবেজে রো-বাই-রো ইনসার্ট করার অপেক্ষা থাকবে না।
- Laravel Authorization Gate-এ `Gate::before()` ইন্টারসেপশন পলিসি কার্যকর থাকবে:
  ```php
  Gate::before(function ($user, $ability) {
      if ($user->hasRole('superadmin')) {
          return true; // যেকোনো নতুন বা পুরাতন পারমিশন স্বয়ংক্রিয়ভাবে অনুমোদিত
      }
  });
  ```

### [DECISION-03] সিস্টেম টিম ইউজার একাউন্টস (Internal Engineering Accounts)
প্ল্যাটফর্ম রুট কোম্পানির অধীনে প্রতিটি দায়িত্বশীল টিমের জন্য অফিসিয়াল সিস্টেম একাউন্ট বুটস্ট্র্যাপ হবে:
1. `superadmin@traceflow.internal` (Platform Owner / Superadmin)
2. `backend.team@traceflow.internal` (Backend Engineering Squad)
3. `frontend.team@traceflow.internal` (Frontend UI/UX Squad)
4. `database.team@traceflow.internal` (Database & Schema Squad)
5. `devops.team@traceflow.internal` (DevOps & SRE Squad)
6. `qa.team@traceflow.internal` (Quality Assurance Squad)
7. `ba.team@traceflow.internal` (Business Analyst Squad)

### [DECISION-04] ইডেমপোটেন্ট সিস্টেম বুটস্ট্র্যাপ সার্ভিস (Non-Seeder Initialization)
- এই ডাটা কোনো সাধারণ সিডার ফাইলে থাকবে না।
- এটি বাস্তবায়িত হবে `app/Domain/Organization/Services/PlatformBootstrapService.php` এর মাধ্যমে।
- এটি শতবার রান করলেও ডাটা ডুপ্লিকেট হবে না (Idempotent)।
- প্রতিটি ইনস্টলেশন বা ডেপ্লয়মেন্টের সময় এটি স্বয়ংক্রিয়ভাবে নিশ্চিত করবে যে রুট প্ল্যাটফর্ম ও সিস্টেম টিম অ্যাকাউন্ট প্রস্তুত আছে।
- পাসওয়ার্ড এনভায়রনমেন্ট কনফিগারেশন (`.env`) থেকে ডাইনামিকভাবে সেট হবে।

---

## ৩. পরিণতি ও লাভজনকতা (Consequences & Benefits)
1. **১০০% জিরো পারমিশন মেইনটেন্যান্স হেডেক:** সিস্টেমে ভবিষ্যতে শত শত নতুন মডিউল ও পারমিশন যোগ হলেও সুপারঅ্যাডমিন অ্যাকাউন্ট স্বয়ংক্রিয়ভাবে সম্পূর্ণ অ্যাক্সেস পাবে।
2. **ডাটা পার্জ সুরক্ষা:** সাধারণ ডেমো অপারেশনাল ডাটা ডিলিট বা পার্জ কমান্ড (`php artisan traceflow:purge-demo`) কখনোই রুট প্ল্যাটফর্ম এবং সিস্টেম টিম ইউজারদের স্পর্শ করতে পারবে না।
3. **এন্টারপ্রাইজ অডিট ক্ল্যারিটি:** কোন কাজটি সিস্টেম ইন্টারনাল টিম করেছে আর কোন কাজটি ক্লায়েন্ট ফ্যাক্টরি ইউজার করেছে, তা অডিট লগে শতভাগ স্বচ্ছভাবে আলাদা থাকবে।
