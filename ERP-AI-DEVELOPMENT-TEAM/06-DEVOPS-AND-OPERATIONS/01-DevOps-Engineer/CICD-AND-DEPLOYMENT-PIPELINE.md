# TraceFlow-RMG: সিআই/সিডি অটোমেশন ও জিরো-ডাউনটাইম ডেপ্লয়মেন্ট
**নথি কোড:** TFRMG-OPS-CICD-002  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** সিআই/সিডি পাইপলাইন ও অটোমেটেড রিলিজ ম্যানুয়াল  
**প্রযোজ্য ইঞ্জিন:** GitHub Actions / GitLab CI, Docker Registry, SSH Deployer  
**অনুমোদনকারী:** প্রিন্সিপাল ডেভঅপ্স লিড (Gate 6 Passed)

---

## ১. ভূমিকা ও রিলিজ দর্শন (Continuous Delivery Philosophy)

TraceFlow-RMG সিস্টেমের সিআই/সিডি (CI/CD) পাইপলাইনের মূল উদ্দেশ্য হলো মানুষের ভুল দূর করা এবং প্রতিটি নতুন কোডবেস সম্পূর্ণ স্বয়ংক্রিয়ভাবে অডিট, টেস্ট ও প্রোডাকশনে ডেপ্লয় করা।

```
[ Git Push to feature branch ]
              │
              ▼
[ GitHub Actions: Lint & Static Analysis (Pint, PHPStan, ESLint) ]
              │
              ▼
[ Automated Tests (PHPUnit / Pest Unit & Feature Tests) ]
              │
              ▼
[ Security Scan (GitGuardian Secrets, SAST Vulnerability) ]
              │
              ▼
[ Pull Request Merged to `staging` or `main` ]
              │
              ▼
[ Multi-stage Docker Image Build & Push to Registry ]
              │
              ▼
[ Zero-Downtime Rolling Deployment on Factory Cloud ]
```

---

## ২. গিটহাব অ্যাকশনস সিআই পাইপলাইন (`.github/workflows/ci.yml`)

```yaml
name: TraceFlow-RMG CI Pipeline

on:
  push:
    branches: [ develop, staging, main ]
  pull_request:
    branches: [ develop, staging, main ]

jobs:
  backend-quality-and-tests:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16-alpine
        env:
          POSTGRES_DB: traceflow_test
          POSTGRES_USER: test_user
          POSTGRES_PASSWORD: secret_password
        ports:
          - 5432:5432
        options: --health-cmd pg_isready --health-interval 10s --health-timeout 5s --health-retries 5
      redis:
        image: redis:7-alpine
        ports:
          - 6379:6379

    steps:
      - name: Checkout Code
        uses: actions/checkout@v4

      - name: Setup PHP 8.3 with Extensions
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: pdo_pgsql, pgsql, redis, opcache, intl, zip
          coverage: none

      - name: Install Composer Dependencies
        run: composer install --prefer-dist --no-interaction --no-progress

      - name: Check Code Style (Laravel Pint)
        run: ./vendor/bin/pint --test

      - name: Static Analysis (PHPStan Level 8)
        run: ./vendor/bin/phpstan analyse --memory-limit=1G

      - name: Run Pest / PHPUnit Test Suite
        env:
          DB_CONNECTION: pgsql
          DB_HOST: 127.0.0.1
          DB_PORT: 5432
          DB_DATABASE: traceflow_test
          DB_USERNAME: test_user
          DB_PASSWORD: secret_password
          REDIS_HOST: 127.0.0.1
        run: php artisan test --parallel

  frontend-quality-and-build:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout Code
        uses: actions/checkout@v4

      - name: Setup Node.js 20 LTS
        uses: actions/setup-node@v4
        with:
          node-version: 20
          cache: 'npm'

      - name: Install Dependencies
        run: npm ci

      - name: ESLint (Zero Inline CSS Enforcement)
        run: npm run lint

      - name: TypeScript Type Checking
        run: npm run type-check

      - name: Production Vite Build
        run: npm run build
```

---

## ৩. জিরো-ডাউনটাইম ডেপ্লয়মেন্ট প্রটোকল (Zero-Downtime Blue/Green Release)

1. **ডাটাবেজ মাইগ্রেশন সেফটি:**
   - নতুন কন্টেইনার চালুর আগে `php artisan migrate --force` ব্যাকগ্রাউন্ডে এক্সিকিউট হবে। কোনো ব্রেকিং কলাম পরিবর্তন থাকলে তা পূর্বে বর্ণিত ডিপ্রিকেশন পলিসি মেনে সম্পন্ন হবে।
2. **হরাইজন ও রিভার্ব রিস্টার্ট:**
   - কিউ জবগুলো আটকে না রেখে মসৃণ রিস্টার্ট করার জন্য সিআই স্ক্রিপ্ট কল করবে: `php artisan horizon:terminate` এবং `php artisan reverb:restart`।
3. **ক্যাশ ওয়ার্ম-আপ:**
   - ডেপ্লয়মেন্ট সম্পন্ন হওয়ার সাথে সাথে রুট কনফিগ ক্যাশ জেনারেট হবে: `php artisan config:cache && php artisan route:cache`।

---
**বাধ্যবাধকতা:** সিআই পাইপলাইনে একটি টেস্টও ফেইল করলে প্রোডাকশনে কোড ডেপ্লয়মেন্ট স্বয়ংক্রিয়ভাবে স্থগিত হয়ে যাবে।
