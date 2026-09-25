# Phase 0: ইনফ্রাস্ট্রাকচার ও ডকার সেটআপ চেকলিস্ট

- [x] **0.1 ডকার কন্টেইনারাইজেশন (Strict Docker-Only Policy)**
  - [x] PHP 8.3 FPM + Composer + PostgreSQL/Redis এক্সটেনশন সহ `Dockerfile.dev` তৈরি
  - [x] Node.js 20 Alpine + Vite Hot Reloading সহ `Dockerfile.dev` তৈরি
  - [x] Nginx Alpine রিভার্স প্রক্সি কনফিগারেশন (`/api`, `/up` ও `/` রাউটিং)
  - [x] `docker-compose.yml` আর্কিটেকচার (PostgreSQL 16, Redis 7, Backend, Frontend, Proxy)
- [x] **0.2 ফ্রেশ প্রজেক্ট ইনিশিয়ালাইজেশন**
  - [x] ডকার কন্টেইনারের ভেতর Laravel 13 ইনিশিয়ালাইজেশন
  - [x] ডকার কন্টেইনারের ভেতর React 19 + TypeScript + Vite ইনিশিয়ালাইজেশন
  - [x] PostgreSQL 16 ডাটাবেজ কানেকশন কনফিগারেশন
  - [x] Redis 7 ক্যাশ ও সেশন কানেকশন কনফিগারেশন
- [x] **0.3 ভার্সন কন্ট্রোল ও রিপোজিটরি সেটআপ**
  - [x] সিকিউর রুট `.gitignore` ফাইল তৈরি
  - [x] গিট ইনিশিয়ালাইজ ও রিমোট অরিজিন বাইন্ডিং (`techstackbusinessbd/TraceFlow-Platform.git`)
  - [x] প্রজেক্ট রুট `README.md` তৈরি ও গিটহাবে ইনিশিয়াল পুশ
