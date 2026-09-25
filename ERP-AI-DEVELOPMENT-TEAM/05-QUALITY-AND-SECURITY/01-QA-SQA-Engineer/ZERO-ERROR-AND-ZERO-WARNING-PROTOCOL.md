# TraceFlow-RMG: জিরো-এরর, জিরো-ওয়ার্নিং ও প্রি-কমিট ভেরিফিকেশন প্রোটোকল
**Document Code:** TFRMG-QA-ZRO-001  
**Version:** 5.0.0  
**Effective Date:** 2026-09-24  
**Classification:** Quality Assurance, Static Analysis, Zero-Error & Zero-Warning Mandate  
**Approved By:** Lead SQA Architect & Principal System Engineer  

---

## ১. ভূমিকা ও মূল নীতি (The Zero-Defect Mandate)

TraceFlow-RMG একটি মিশন-ক্রিটিক্যাল এন্টারপ্রাইজ সিস্টেম। ফ্লোর স্ক্যানিং বা প্রোডাকশন ডাটাবেজে একটি সাধারণ সিনট্যাক্স এরর বা আনহ্যান্ডেলড টাইপস্ক্রিপ্ট ওয়ার্নিং কারখানার শত শত সুইং লাইন থামিয়ে দিয়ে আর্থিক ক্ষতির কারণ হতে পারে।

অতএব, ব্যাকএন্ড (Laravel 13 / PHP 8.3+) এবং ফ্রন্টএন্ড (React 19 / TypeScript)-এ নতুন কোড লেখা বা এডিট করার ক্ষেত্রে একটি অলঙ্ঘনীয় নীতি কার্যকর হলো:

> 🛑 **ZERO-ERROR & ZERO-WARNING POLICY:**  
> কোনো ফাইলে কোনো অবস্থাতেই সিনট্যাক্স এরর, আনইউজড ইমপোর্ট (Unused Import), আনরিজলভড টাইপ (Type Error), লিন্টিং ওয়ার্নিং বা রানটাইম নোটিশ রেখে কাজ সম্পন্ন ঘোষণা করা যাবে না।  
> **কোড লেখার পর স্বয়ংক্রিয় ভ্যালিডেশন কমান্ড চালিয়ে ১০০% ক্লিন রিপোর্ট নিশ্চিত না হওয়া পর্যন্ত কোড ডেলিভারি নিষিদ্ধ।**

---

## ২. ব্যাকএন্ড ভেরিফিকেশন প্রোটোকল (Backend Protocol: PHP 8.3 / Laravel 13)

ব্যাকএন্ডে যেকোনো ফাইল লেখা বা পরিবর্তনের পরপরই ডেভেলপার বা এআই এজেন্টকে টার্মিনালে নিচের ধাপগুলো ক্রমানুসারে এক্সিকিউট করে যাচাই করতে হবে:

```
┌────────────────────────────────────────────────────────────────────────┐
│                   BACKEND VERIFICATION PIPELINE                        │
├─────────────────┬──────────────────────────────────────────────────────┤
│ 1. Syntax Check │ php -l [file] (PHP Built-in Linter)                  │
├─────────────────┼──────────────────────────────────────────────────────┤
│ 2. Code Style   │ ./vendor/bin/pint --test (Laravel Pint Linter)       │
├─────────────────┼──────────────────────────────────────────────────────┤
│ 3. Static Types │ ./vendor/bin/phpstan analyse (Level 8 Analysis)      │
├─────────────────┼──────────────────────────────────────────────────────┤
│ 4. Route Health │ php artisan route:list (Zero Route Exception)        │
├─────────────────┼──────────────────────────────────────────────────────┤
│ 5. Unit Tests   │ php artisan test (Zero Failures)                     │
└─────────────────┴──────────────────────────────────────────────────────┘
```

### ২.১ বাধ্যতামূলক কমান্ডসমূহ (Docker Execution):
```bash
# ১. সিনট্যাক্স চেক (নির্দিষ্ট ফাইল)
docker compose exec -T tf_backend php -l app/Domain/Organization/Services/CompanyService.php

# ২. লিন্ট ও স্টাইল ফরম্যাটিং যাচাই
docker compose exec -T tf_backend ./vendor/bin/pint --test

# ৩. স্ট্যাটিক টাইপ ও মিসিং মেথড অ্যানালাইসিস
docker compose exec -T tf_backend ./vendor/bin/phpstan analyse --memory-limit=2G

# ৪. রাউটিং ও কনফিগ লোড ভেরিফিকেশন
docker compose exec -T tf_backend php artisan route:list --path=api/v1

# ৫. অটোমেটেড টেস্ট এক্সিকিউশন
docker compose exec -T tf_backend php artisan test
```

---

## ৩. ফ্রন্টএন্ড ভেরিফিকেশন প্রোটোকল (Frontend Protocol: React 19 / TypeScript)

ফ্রন্টএন্ডে যেকোনো কম্পোনেন্ট বা হুক লেখার পর নিচের ধাপগুলো ক্রমানুসারে যাচাই করতে হবে:

```
┌────────────────────────────────────────────────────────────────────────┐
│                   FRONTEND VERIFICATION PIPELINE                       │
├─────────────────┬──────────────────────────────────────────────────────┤
│ 1. Type Check   │ npx tsc --noEmit (Strict TypeScript Compilation)     │
├─────────────────┼──────────────────────────────────────────────────────┤
│ 2. Code Linting │ npm run lint (ESLint Zero Warnings Mandate)          │
├─────────────────┼──────────────────────────────────────────────────────┤
│ 3. Build Check  │ npm run build (Vite Zero Bundle Error)               │
└─────────────────┴──────────────────────────────────────────────────────┘
```

### ৩.১ বাধ্যতামূলক কমান্ডসমূহ (Docker Execution):
```bash
# ১. স্ট্রিক্ট টাইপস্ক্রিপ্ট টাইপ চেক (No Emit)
docker compose exec -T tf_frontend npx tsc --noEmit

# ২. ইএসলিন্ট ও আনইউজড ভ্যারিয়েবল স্ক্যান
docker compose exec -T tf_frontend npm run lint

# ৩. প্রোডাকশন বান্ডেল টেস্ট
docker compose exec -T tf_frontend npm run build
```

---

## ৪. প্রি-ডেলিভারি চেকলিস্ট (Pre-Delivery Definition of Done)

একটি টাস্ক সম্পন্ন হওয়ার আগে নিচের ৫টি প্রশ্ন যাচাই করতে হবে:

1. **[CHECK-1] কোনো সিনট্যাক্স এরর নেই তো?**
   - ব্যাকএন্ডের ফাইল `php -l` দিয়ে `No syntax errors detected` নিশ্চিত করেছে কিনা।
2. **[CHECK-2] টাইপস্ক্রিপ্টে কোনো লাল দাগ বা `any` টাইপ আছে কি?**
   - `tsc --noEmit` কোনো টাইপ এরর দেয়নি তো?
3. **[CHECK-3] কোনো আনইউজড ইমপোর্ট বা ভ্যারিয়েবল আছে কি?**
   - অপ্রয়োজনীয় `use` বা `import` স্টেটমেন্ট ফেলে দেওয়া হয়েছে কিনা।
4. **[CHECK-4] পেজ লোডে কনসোল এরর শূন্য তো?**
   - ব্রাউজার কনসোলে কোনো `Warning: Each child in a list should have a unique key` বা `Uncaught Exception` আছে কিনা।
5. **[CHECK-5] ডকার লগ সম্পূর্ণ পরিচ্ছন্ন তো?**
   - `docker compose logs` ফাইলে কোনো Fatal Error বা Warning নেই।

---

## ৫. সিআই/সিডি অটোমেশন ও পুল রিকোয়েস্ট ব্লকার (CI Blocker)

গিটহাবে কোনো পুল রিকুয়েস্ট ওপেন হলে স্বয়ংক্রিয় গিটহাব অ্যাকশনস (GitHub Actions) রান হবে:
- ১টি লিন্ট ওয়ার্নিং থাকলেও **PR Merge Blocked** হয়ে যাবে।
- কোড পুশ করার পূর্বে ডেভেলপার লোকালি ডকারে এই কমান্ডগুলো রান করা বাধ্যতামূলক।
