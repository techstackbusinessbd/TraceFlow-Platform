# TraceFlow-RMG: লোকাল ডকার এনভায়রনমেন্ট স্টার্ট ও ডেভেলপার গাইডলাইন
**নথি কোড:** TFRMG-OPS-DKR-002  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** টিম অনবোর্ডিং ও ডেভঅপ্স এক্সিকিউশন গাইড  
**অনুমোদনকারী:** লিড ডেভঅপ্স আর্কিটেক্ট (Gate 6 Passed)  
**টিম রোল:** DevOps Engineer & Software Engineering Team  

---

> 🛑 **সতর্কবার্তা:** লোকাল পিসিতে সরাসরি কোনো PHP, Node.js, PostgreSQL বা Redis ইনস্টল করে কাজ করা সম্পূর্ণ নিষিদ্ধ। সমস্ত কিছু ডকার কন্টেইনারে চলবে।

---

## ১. প্রজেক্ট চালু করার নিয়ম (Docker Startup)

লোকাল কম্পিউটারে ডকার ডেস্কটপ (Docker Desktop) চালু রেখে টার্মিনালে নিচের কমান্ড দিন:

```bash
# সব কন্টেইনার ব্যাকগ্রাউন্ডে চালু করা
docker compose up -d
```

কন্টেইনারগুলোর স্ট্যাটাস চেক করতে:
```bash
docker compose ps
```

---

## ২. ডেভেলপার কমান্ড তালিকা (Docker Exec Commands)

লোকাল পিসির টার্মিনালে সরাসরি `php`, `composer` বা `npm` কমান্ড দেওয়া যাবে না। নিচের মতো কন্টেইনারের মাধ্যমে এক্সিকিউট করতে হবে:

| কাঙ্ক্ষিত কাজ | ডকার এক্সিকিউশন কমান্ড (STRICT) |
|---|---|
| **মাইগ্রেশন রান করা** | `docker compose exec backend php artisan migrate` |
| **টেস্ট রান করা** | `docker compose exec backend php artisan test` |
| **কম্পোজার প্যাকেজ ইনস্টল** | `docker compose exec backend composer require [package-name]` |
| **এনপিএম প্যাকেজ ইনস্টল** | `docker compose exec frontend npm install [package-name]` |
| **কোড ফরম্যাটিং (Pint)** | `docker compose exec backend ./vendor/bin/pint` |
| **রুট ও কনফিগ ক্লিয়ার** | `docker compose exec backend php artisan optimize:clear` |
| **ডাটাবেজ সিডার রান** | `docker compose exec backend php artisan db:seed` |

---

## ৩. লোকাল সার্ভিস পোর্ট ও ইউআরএল ম্যাপিং

* **Frontend & Web Portal:** `http://localhost` (Nginx Proxy) বা `http://localhost:5173` (Direct Vite Dev Server)
* **Backend API:** `http://localhost/api/v1`
* **PostgreSQL Database:** `localhost:5432` (User: `tf_user`, Pass: `tf_password`, DB: `traceflow_rmg_dev`)
* **Redis Cache:** `localhost:6379`
* **Laravel Reverb WebSockets:** `http://localhost:8080`
* **MinIO Object Storage Console:** `http://localhost:9001` (User: `minio_admin`, Pass: `minio_password`)
* **MinIO S3 API Endpoint:** `http://localhost:9000`

---

## ৪. প্রজেক্ট বন্ধ করা

```bash
docker compose down
```
ডাটাবেজের ডাটা সহ ভলিউম রিমুভ করতে চাইলে:
```bash
docker compose down -v
```
