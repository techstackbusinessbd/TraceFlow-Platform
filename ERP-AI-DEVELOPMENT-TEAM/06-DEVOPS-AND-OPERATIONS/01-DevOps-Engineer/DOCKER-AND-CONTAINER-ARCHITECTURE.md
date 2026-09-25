# TraceFlow-RMG: ডেভঅপ্স, ডকার ও কন্টেইনারাইজেশন ব্লুপ্রিন্ট
**নথি কোড:** TFRMG-OPS-DKR-001  
**সংস্করণ:** ২.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** ইনফ্রাস্ট্রাকচার ও ডেভঅপ্স ইঞ্জিনিয়ারিং  
**প্রযোজ্য টেক স্ট্যাক:** Docker, Docker Compose, Nginx, PHP 8.3 FPM, PostgreSQL 16+, Redis 7+ Alpine, Laravel Horizon, Laravel Reverb, MinIO, Node.js Vite  
**অনুমোদনকারী:** লিড ডেভঅপ্স আর্কিটেক্ট (Gate 6 Passed)

---

## ১. ভূমিকা ও ১০০% ডকারাইজড পরিবেশ নীতি (Strict Docker-Only Policy)

TraceFlow-RMG প্ল্যাটফর্মের ডেভেলপমেন্ট এবং প্রোডাকশন পরিবেশের জন্য একটি কঠোর অনুশাসন কার্যকর থাকবে:

> 🛑 **NO LOCAL MACHINE RUNTIME POLICY:**  
> কোনো ডেভেলপারের লোকাল কম্পিউটারে সরাসরি PHP, Node.js, PostgreSQL বা Redis ইনস্টল করে প্রজেক্ট রান করা সম্পূর্ণ নিষিদ্ধ।  
> **লোকাল ডেভেলপমেন্ট (Local Development), স্ট্যাজিং এবং প্রোডাকশন—প্রতিটি পরিবেশ শতভাগ ডকার ও ডকার কম্পোজ (Docker & Docker Compose)-এর ভেতরে পরিচালিত হবে।**  
> এর উদ্দেশ্য হলো: "It works on my machine" জাতীয় জটিলতা চিরতরে দূর করা এবং কারখানার সমস্ত ডেভেলপারের জন্য হুবহু একই ধরনের লাইব্রেরি ও ডিপেনডেন্সি পরিবেশ নিশ্চিত করা।

---

## ২. লোকাল ডেভেলপমেন্ট ডকার কম্পোজ ব্লুপ্রিন্ট (`docker-compose.yml`)

লোকাল কম্পিউটারে কোড এডিটিংয়ের সাথে সাথে রিয়েল-টাইম হট-রিলোড (Hot Reloading / Live Sync) নিশ্চিত করতে নিচের লোকাল ডকার কনফিগারেশন প্রযোজ্য হবে:

```yaml
version: '3.9'

services:
  # ১. লোকাল রিভার্স প্রক্সি (Nginx)
  proxy:
    image: nginx:alpine
    container_name: tfrmg_local_proxy
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./docker/local/nginx/conf.d:/etc/nginx/conf.d:ro
      - ./backend:/var/www/html:delegated
      - ./frontend/dist:/var/www/frontend:ro
    depends_on:
      - backend
      - reverb
    networks:
      - tfrmg_local_net

  # ২. ব্যাকএন্ড লোকাল ডেভেলপমেন্ট ইঞ্জিন (PHP 8.3 FPM + Xdebug)
  backend:
    build:
      context: .
      dockerfile: docker/local/php/Dockerfile.dev
    container_name: tfrmg_local_backend
    restart: unless-stopped
    volumes:
      - ./backend:/var/www/html:delegated
    environment:
      APP_ENV: local
      APP_DEBUG: 'true'
      DB_CONNECTION: pgsql
      DB_HOST: postgres
      DB_PORT: 5432
      DB_DATABASE: traceflow_rmg_dev
      DB_USERNAME: tf_user
      DB_PASSWORD: tf_password
      REDIS_HOST: redis
      REDIS_PORT: 6379
    depends_on:
      postgres:
        condition: service_healthy
      redis:
        condition: service_healthy
    networks:
      - tfrmg_local_net

  # ৩. ফ্রন্টএন্ড লোকাল ডেভেলপমেন্ট সার্ভার (Vite Hot-Reload Server)
  frontend:
    build:
      context: ./frontend
      dockerfile: ../docker/local/node/Dockerfile.dev
    container_name: tfrmg_local_frontend
    restart: unless-stopped
    ports:
      - "5173:5173"
    volumes:
      - ./frontend:/app:delegated
      - /app/node_modules
    environment:
      - VITE_API_BASE_URL=http://localhost/api/v1
      - VITE_REVERB_HOST=localhost
      - VITE_REVERB_PORT=8080
    networks:
      - tfrmg_local_net

  # ৪. লোকাল কিউ ওয়ার্কার (Horizon)
  horizon:
    build:
      context: .
      dockerfile: docker/local/php/Dockerfile.dev
    container_name: tfrmg_local_horizon
    restart: unless-stopped
    command: ["php", "artisan", "horizon"]
    volumes:
      - ./backend:/var/www/html:delegated
    depends_on:
      - backend
      - redis
    networks:
      - tfrmg_local_net

  # ৫. লোকাল রিয়েল-টাইম ওয়েবসকেট (Reverb)
  reverb:
    build:
      context: .
      dockerfile: docker/local/php/Dockerfile.dev
    container_name: tfrmg_local_reverb
    restart: unless-stopped
    command: ["php", "artisan", "reverb:start", "--host=0.0.0.0", "--port=8080"]
    ports:
      - "8080:8080"
    volumes:
      - ./backend:/var/www/html:delegated
    depends_on:
      - backend
      - redis
    networks:
      - tfrmg_local_net

  # ৬. লোকাল ডাটাবেজ (PostgreSQL 16+)
  postgres:
    image: postgres:16-alpine
    container_name: tfrmg_local_postgres
    restart: unless-stopped
    environment:
      POSTGRES_DB: traceflow_rmg_dev
      POSTGRES_USER: tf_user
      POSTGRES_PASSWORD: tf_password
    ports:
      - "5432:5432" # DBeaver / TablePlus দিয়ে লোকাল কানেকশনের জন্য
    volumes:
      - local_pgdata:/var/lib/postgresql/data
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U tf_user -d traceflow_rmg_dev"]
      interval: 5s
      timeout: 3s
      retries: 5
    networks:
      - tfrmg_local_net

  # ৭. লোকাল ইন-মেমোরি ক্যাশ (Redis 7+ Alpine)
  redis:
    image: redis:7-alpine
    container_name: tfrmg_local_redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    volumes:
      - local_redisdata:/data
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 5s
      timeout: 3s
      retries: 5
    networks:
      - tfrmg_local_net

  # ৮. লোকাল অবজেক্ট স্টোরেজ (MinIO)
  minio:
    image: minio/minio:latest
    container_name: tfrmg_local_minio
    restart: unless-stopped
    command: server /data --console-address ":9001"
    ports:
      - "9000:9000"
      - "9001:9001" # Web UI Console
    environment:
      MINIO_ROOT_USER: minio_admin
      MINIO_ROOT_PASSWORD: minio_password
    volumes:
      - local_miniodata:/data
    networks:
      - tfrmg_local_net

networks:
  tfrmg_local_net:
    driver: bridge

volumes:
  local_pgdata:
  local_redisdata:
  local_miniodata:
```

---

## ৩. লোকাল ডেভেলপমেন্ট কমান্ড রানবুক (Developer Commands inside Docker)

লোকাল কম্পিউটারে কোনো কমান্ড সরাসরি টার্মিনালে রান করা যাবে না। সর্বদা ডকার কন্টেইনারের ভেতর দিয়ে এক্সিকিউট করতে হবে:

| কাঙ্ক্ষিত অ্যাকশন | নিষিদ্ধ লোকাল কমান্ড (DO NOT RUN ON PC) | বাধ্যতামূলক ডকার কমান্ড (STRICT DOCKER RUN) |
|---|---|---|
| **মাইগ্রেশন চালানো** | `php artisan migrate` | `docker compose exec backend php artisan migrate` |
| **টেস্ট রান করা** | `php artisan test` | `docker compose exec backend php artisan test` |
| **কম্পোজার প্যাকেজ ইনস্টল**| `composer require <pkg>` | `docker compose exec backend composer require <pkg>` |
| **এনপিএম প্যাকেজ ইনস্টল** | `npm install <pkg>` | `docker compose exec frontend npm install <pkg>` |
| **কোড ফরম্যাটিং (Pint)** | `./vendor/bin/pint` | `docker compose exec backend ./vendor/bin/pint` |
| **কন্টেইনার শুরু করা** | — | `docker compose up -d` |
| **কন্টেইনার বন্ধ করা** | — | `docker compose down` |

---

## ৪. প্রোডাকশন ডকার কম্পোজ ব্লুপ্রিন্ট (`docker-compose.prod.yml`)

প্রোডাকশন এনভায়রনমেন্টে কোনো লোকাল ভলিউম মাউন্ট বা ডিবাগ পোর্ট থাকবে না। সম্পূর্ণ প্রি-বিল্ট অপ্টিমাইজড ইমেজ চলবে:
* সমস্ত কোড ডকার ইমেজের ভেতরে কপি করা থাকবে।
* OPcache প্রোডাকশন মোডে এনেবল থাকবে।
* SSL সার্টিফিকেট Nginx প্রক্সির সাথে বাইন্ড থাকবে।

---
**বাধ্যবাধকতা:** কোনো ডেভেলপার লোকাল পিসিতে সরাসরি ডাটাবেজ বা পিএইচপি রান করে কোড পুশ করার চেষ্টা করলে তা শৃঙ্খলাভঙ্গ হিসেবে গণ্য হবে।
