# TraceFlow-Platform 🚀
> **Enterprise Multi-Tenant / Appliance-Ready Next-Gen ERP for Apparel & Garments Manufacturing**

[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker&logoColor=white)](#-docker-orchestration)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://php.net)
[![React](https://img.shields.io/badge/React-19.x-61DAFB?logo=react&logoColor=black)](https://react.dev)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-3178C6?logo=typescript&logoColor=white)](https://www.typescriptlang.org)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?logo=postgresql&logoColor=white)](https://www.postgresql.org)
[![Redis](https://img.shields.io/badge/Redis-7-DC382D?logo=redis&logoColor=white)](https://redis.io)

---

## 📌 প্রকল্পের পরিচিতি (Project Overview)

**TraceFlow-Platform** হলো তৈরি পোশাক শিল্পের (Ready-Made Garments - RMG) জন্য একটি আধুনিক, এন্টারপ্রাইজ-গ্রেড, হাই-কনকারেন্ট এবং ক্লাউড/অন-প্রিমিসেস অ্যাপ্লায়েন্স সাপোর্টেড ইআরপি (ERP) প্ল্যাটফর্ম। 

বায়িং ও মার্চেন্ডাইজিং থেকে শুরু করে ওয়্যারহাউস, কাটিং, সুইং কিউসি, ফিনিশিং, প্যাকিং এবং শিপমেন্ট পর্যন্ত ফ্যাক্টরি মেঝের প্রতিটি কার্যক্রম নিখুঁতভাবে পরিচালনা ও ট্র্যাকিং করার উদ্দেশ্যে এটি আর্কিটেক্ট করা হয়েছে।

---

## 🏗️ মূল আর্কিটেকচার ও টেকনোলজি স্ট্যাক (Architecture & Tech Stack)

- **ব্যাকএন্ড:** Laravel 13, PHP 8.3+, Modular Domain-Driven Design (DDD), Spatie Multi-Company RBAC.
- **ফ্রন্টএন্ড:** React 19, TypeScript, Vite, TanStack Table v8, সেন্ট্রালাইজড CSS ভেরিয়েবল ও ডিজাইন টোকেন (Zero Inline CSS).
- **ডাটাবেজ ও ক্যাশ:** PostgreSQL 16 (100% UUID v7 Primary Keys), Redis 7 (ক্যাশ ও ব্যাকগ্রাউন্ড কিউ).
- **রিভার্স প্রক্সি:** Nginx Alpine (লোকাল প্রক্সি ও রাউটিং).
- **পরিবেশ নীতি:** **Strict 100% Docker-Only Policy** (লোকাল পিসিতে কোনো রানটাইম ইনস্টল করার প্রয়োজন নেই)।

---

## 🐳 লোকাল ডেভেলপমেন্ট সেটআপ (Docker Setup)

প্রজেক্টটি চালানোর জন্য আপনার কম্পিউটারে কেবল **Docker Desktop** ইনস্টল ও চালু থাকতে হবে।

### ১. রিপোজিটরি ক্লোন করুন
```bash
git clone https://github.com/techstackbusinessbd/TraceFlow-Platform.git
cd TraceFlow-Platform
```

### ২. এনভায়রনমেন্ট কনফিগারেশন
```bash
# ব্যাকএন্ড .env প্রস্তুত করা (যদি না থাকে)
cp backend/.env.example backend/.env
```

### ৩. ডকার কন্টেইনার চালু করুন
```bash
docker compose up -d
```

### ৪. সার্ভিস অ্যাক্সেস ও পোর্ট ম্যাপিং
- **ওয়েব অ্যাপ্লিকেশন (Frontend & Proxy):** [http://localhost](http://localhost) বা [http://localhost:5173](http://localhost:5173)
- **ব্যাকএন্ড হেলথ এন্ডপয়েন্ট:** [http://localhost/up](http://localhost/up)
- **এপিআই এন্ডপয়েন্ট:** `http://localhost/api/v1`
- **PostgreSQL ডাটাবেজ:** `localhost:5432` (`User: tf_user`, `Pass: tf_password`, `DB: traceflow_rmg_dev`)
- **Redis Cache:** `localhost:6379`

---

## 🛠️ ডেভেলপার কমান্ড রানবুক (Developer Runbook - Docker Only)

লোকাল টার্মিনালে সরাসরি `php` বা `npm` না চালিয়ে সর্বদা ডকার কন্টেইনারের মাধ্যমে এক্সিকিউট করুন:

| প্রয়োজনীয় অ্যাকশন | ডকার কমান্ড |
|---|---|
| **মাইগ্রেশন রান করা** | `docker compose exec backend php artisan migrate` |
| **ব্যাকএন্ড টেস্ট রান করা** | `docker compose exec backend php artisan test` |
| **কম্পোজার প্যাকেজ ইনস্টল** | `docker compose exec backend composer require <package>` |
| **এনপিএম প্যাকেজ ইনস্টল** | `docker compose exec frontend npm install <package>` |
| **ফ্রন্টএন্ড বিল্ড ভেরিফাই** | `docker compose exec frontend npm run build` |
| **কোড ফরম্যাটিং (Pint)** | `docker compose exec backend ./vendor/bin/pint` |
| **ক্যাশ ও অপ্টিমাইজ ক্লিয়ার** | `docker compose exec backend php artisan optimize:clear` |
| **কন্টেইনার বন্ধ করা** | `docker compose down` |

---

## 📁 প্রজেক্ট ডিরেক্টরি পরিচিতি (Directory Layout)

```
TraceFlow-Platform/
├── .agents/                    # এআই ডেভেলপার রোল ও গভর্ন্যান্স স্কিলস
├── backend/                    # Laravel 13 Modular DDD ব্যাকএন্ড সোর্স
├── frontend/                   # React 19 + TypeScript + Vite ফ্রন্টএন্ড সোর্স
├── docker/                     # PHP 8.3, Node 20 এবং Nginx ডকার কনফিগ
├── docs/                       # মডিউল ভিত্তিক SRS ও SOP স্পেসিফিকেশন
│   └── modules/
│       ├── 00-core-and-iam/
│       └── 01-client-onboarding-and-appliance/
├── ERP-AI-DEVELOPMENT-TEAM/    # বেস আর্কিটেকচার, পলিসি, ডাটাবেস স্কিমা ও ADRs
├── docker-compose.yml          # লোকাল অর্কেস্ট্রেশন ব্লুপ্রিন্ট
└── README.md                   # প্রজেক্ট ডকুমেন্টেশন
```

---

## 📜 কোডিং স্ট্যান্ডার্ড ও কোয়ালিটি পলিসি

1. **Zero Hardcoded Enums:** ড্রপডাউন ও অপশনস সেন্ট্রালাইজড লুকআপ এবং ব্যাকড এনাম ভিত্তিক।
2. **UUID v7 Primary Keys:** ডাটাবেজের সমস্ত টেবিলে টাইম-অর্ডারড সিকোয়েন্সিয়াল UUID v7 ব্যবহার করা হয়।
3. **Bilingual Code Commentary:** ইংরেজি টেকনিক্যাল ডকব্লক এবং বাংলা বিজনেস লজিক কমেন্টারি।
4. **Zero Error & Zero Warning:** প্রতিটি ফিচারের জন্য ১০০% টেস্ট কভারেজ এবং লিন্ট পাশ আবশ্যক।

---

## 📄 লাইসেন্স (License)
স্বত্বাধিকার সংরক্ষিত © ২০২৬ [TechStack Business BD](https://github.com/techstackbusinessbd). সমস্ত অধিকার সংরক্ষিত।
