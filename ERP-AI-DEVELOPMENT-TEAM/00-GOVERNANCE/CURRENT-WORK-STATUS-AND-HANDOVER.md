# TraceFlow-Platform: বর্তমান কাজের স্থিতি ও প্রজেক্ট স্ট্যাটাস (Project Status & Handover)
**সর্বশেষ আপডেট সময়:** ২০২৬-০৯-২৫, সন্ধ্যা ০৬:২৮ (GMT+6)  
**স্ট্যাটাস:** একদম ফ্রেশ ও নিউ প্রজেক্ট (Fresh & New Project) — শুধুমাত্র আর্কিটেকচার ও রিকোয়ারমেন্টস ডকুমেন্টেশন প্রস্তুত। কোনো কোড এখনও শুরু করা হয়নি (Zero Code Implementation)।

---

## ১. বর্তমান প্রকল্পের অবস্থান (Current Project Status)
- **ডকুমেন্টেশন ও আর্কিটেকচার:** অনুমোদিত ও প্রস্তুত (`ERP-AI-DEVELOPMENT-TEAM/`, `docs/modules/`)।
- **ডকার এনভায়রনমেন্ট স্ট্যাটাস:** ১০০% সক্রিয় ও সফলভাবে রানিং (Strict Docker-Only Policy Passed):
  - `tfrmg_local_backend`: PHP 8.3 FPM + Laravel 13 (Test suite 100% Passed).
  - `tfrmg_local_frontend`: Node.js 20 + React 19 + TypeScript + Vite (Port 5173 / Proxy Port 80).
  - `tfrmg_local_postgres`: PostgreSQL 16 (Health: Healthy, Port 5432).
  - `tfrmg_local_redis`: Redis 7 Alpine (Health: Healthy, Port 6379).
  - `tfrmg_local_proxy`: Nginx Alpine Reverse Proxy (Port 80).
- **কোডবেস স্ট্যাটাস:** ফ্রেশ বেস স্ক্যাফোল্ডিং সম্পন্ন, বিজনেস লজিক কোডিং এখনও শুরু হয়নি (Zero Domain Code)।

---

## ২. ডেভেলপার রানবুক অনুস্মারক (Docker Exec Only)
- মাইগ্রেশন: `docker compose exec backend php artisan migrate`
- টেস্ট: `docker compose exec backend php artisan test`
- ফ্রন্টএন্ড রান/বিল্ড: `docker compose exec frontend npm run build`
- ব্যাকএন্ড কোড ফরম্যাটিং: `docker compose exec backend ./vendor/bin/pint`
১. **প্রজেক্ট বুটস্ট্র্যাপ ও ইনিশিয়ালাইজেশন:**
   - ব্যাকএন্ড: Laravel 13 প্রজেক্ট সেটআপ, PostgreSQL 16 ও Redis 7 কনফিগারেশন, UUID v7 বেস সেটআপ।
   - ফ্রন্টএন্ড: React 19 + TypeScript + Vite সেটআপ, ডিজাইন টোকেন ও সিএসএস ভেরিয়েবল কনফিগারেশন।
২. **কোর ও আইএএম মডিউল (00-core-and-iam):**
   - SRS ও SOP অনুযায়ী অথেনটিকেশন, ডুয়াল ড্যাশবোর্ড এবং টেন্যান্ট/কোম্পানি মাস্টার মডেলিং ও ইমপ্লিমেন্টেশন।
৩. **পরবর্তী ডোমেন মডিউলসমূহ:**
   - Client Onboarding, Merchandising & Costing ইত্যাদি ধাপে ধাপে ডেভেলপমেন্ট।

---

## ৩. পরবর্তী করণীয় (Next Steps to Begin Development)
১. ইউজার কনফার্মেশন নিয়ে ব্যাকএন্ড ফোল্ডার স্ট্রাকচার ও ইনিশিয়ালাইজেশন শুরু করা (Laravel 13)।
২. ফ্রন্টএন্ড ফোল্ডার স্ট্রাকচার ও ইনিশিয়ালাইজেশন শুরু করা (React 19 + TypeScript + Vite)।
৩. `docs/modules/00-core-and-iam/SRS.md` এবং `SOP.md` মেনে প্রথম কোডিং ফেজ শুরু করা।
