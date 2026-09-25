# SRS: ইউনিফাইড এন্টারপ্রাইজ অথেনটিকেশন ও প্রিমিয়াম লগইন গেটওয়ে
**মডিউল কোড:** MOD-00-AUTH  
**নথি কোড:** TFRMG-SRS-002  
**সংস্করণ:** ৩.০.০  
**স্ট্যাটাস:** APPROVED / PRODUCTION-READY  
**প্রাসঙ্গিক ডিজাইন ও আর্কিটেকচার স্ট্যান্ডার্ড:**  
- `UI-UX-GUIDELINES.md` (ইউনিফাইড এন্টারপ্রাইজ ডিজাইন সিস্টেম ও কালার টোকেন)  
- `ENTERPRISE-UI-COMPONENT-SYSTEM.md` (Category 15: Authentication & Security UI)  
- `ADR-12` (ডুয়াল-ড্যাশবোর্ড ও সিঙ্গেল লগইন গেটওয়ে)  
- `ADR-16` (অ্যান্টি-ফ্যাটিগ সফট স্লেট ক্যানভাস ও আই-কমফোর্ট ডিজাইন)  
- `ADR-21 / INVARIANT-28` (অভিন্ন ক্রস-পেজ কালার সিস্টেম ও জিরো থিম ডিসক্রিপেন্সি)  
- `ADR-22 / INVARIANT-29` (কমপ্যাক্ট কর্নার রেডিয়াস rounded-sm ও প্রিসাইজ ইন্ডাস্ট্রিয়াল এস্থেটিক্স)  

---

## ১. ভূমিকা ও ব্যবসায়িক প্রয়োজনীয়তা (Executive Summary)

TraceFlow-RMG এন্টারপ্রাইজ প্ল্যাটফর্মে প্রবেশদ্বার হিসেবে একটিমাত্র সুসংহত, প্রিমিয়াম ও সিকিউর লগইন গেটওয়ে (`/login`) থাকবে। প্ল্যাটফর্ম ওনার (সুপারঅ্যাডমিন), ইন্টারনাল সিস্টেম স্কোয়াড (DevOps, QA, Database), এবং পোশাক কারখানার ফ্লোর ম্যানেজার—সকলের জন্য এটি একক প্রবেশদ্বার। ব্যাকএন্ডের রোল ও প্রতিষ্ঠান মূল্যায়নের ভিত্তিতে সিস্টেম স্বয়ংক্রিয়ভাবে ইউজারকে তার নির্দিষ্ট ড্যাশবোর্ডে (`/platform/command-center` অথবা `/app/dashboard`) রিডাইরেক্ট করে।

### মাস্টার ইউআই ডিজাইন নীতি অনুসারে মূল লক্ষ্যসমূহ:
1. **১০০% অভিন্ন ডিজাইন সামঞ্জস্য (INVARIANT-28):** সমস্ত পেজের ন্যায় লগইন স্ক্রিনেও সেন্ট্রালাইজড ডিজাইন টোকেন (`variables.css`) হুবহু প্রযোজ্য। কোনো বিচ্ছিন্ন সাইবারপাঙ্ক ডার্ক স্ক্রিন বা র্যান্ডম কালার নয়; এটি মূল প্ল্যাটফর্মের আই-কমফোর্ট সফট স্লেট ক্যানভাসের অবিচ্ছেদ্য অংশ।
2. **অ্যান্টি-ফ্যাটিগ সফট স্লেট ক্যানভাস (ADR-16):** সফট ওয়ার্ম স্লেট ক্যানভাস (`--color-bg-base` / `#F8FAFC`) এবং সেন্ট্রাল ফ্লোটিং কার্ড হোয়াইট সারফেস (`--color-bg-surface` / `#FFFFFF`)।
3. **সেন্টারড ফ্লোটিং এন্টারপ্রাইজ কার্ড (Max Width 460px):** স্ক্রিনের ঠিক কেন্দ্রস্থলে সুবিন্যস্ত প্রফেশনাল ফ্লোটিং কার্ড লেআউট (`items-center justify-center`)।
4. **কমপ্যাক্ট কর্নার জিওমেট্রি (INVARIANT-29):** বড় বা বাবলি কার্ভ সম্পূর্ণ নিষিদ্ধ; কার্ড, ইনপুট ও বাটনে বাধ্যতামূলক কমপ্যাক্ট রেডিয়াস (`rounded-sm` / 2-4px)।
5. **১০০% সার্ভার-সাইড ভ্যালিডেশন (INVARIANT-27):** ফর্মের কোনো ব্রাউজার ডিফল্ট বাবল টুলটিপ থাকবে না (`<form noValidate>`); সকল ভ্যালিডেশন ব্যাকএন্ড FormRequest এবং ৪২২ এরর ম্যাপিং দ্বারা পরিচালিত হবে।

---

## ২. ইউজার ইন্টারফেস ও স্ক্রিন আর্কিটেকচার (UI / UX Specifications)

### ২.১ লেআউট ও ভিজ্যুয়াল আর্কিটেকচার (UI-UX-GUIDELINES & ADR-16 Aligned)
- **ক্যানভাস ও ব্যাকগ্রাউন্ড এস্থেটিক্স:**
  - ডিফল্ট ব্যাকগ্রাউন্ড: পিওর অ্যান্টি-ফ্যাটিগ সফট স্লেট ক্যানভাস (`--color-bg-base` / `bg-slate-50` / `#F8FAFC`)।
  - টেক্সচার: সূক্ষ্ম প্রফেশনাল ডট গ্রিড প্যাটার্ন (`.bg-dot-pattern`, opacity 40%)। কোনো চড়া বা অস্বস্তিকর ব্লার/দাগ সম্পূর্ণ নিষিদ্ধ।
- **সেন্টারড সাইন-ইন কার্ড (Screen Center Floating Card - Max 460px):**
  - স্ক্রিনের একদম কেন্দ্রে (Vertically & Horizontally centered) ফ্লোটিং কার্ড লেআউট।
  - কার্ড সারফেস: পিওর ক্লিন হোয়াইট সারফেস (`bg-white` / `--color-bg-surface`), সাবটল এন্টারপ্রাইজ বর্ডার (`border-slate-200/90` / `--color-border-subtle`), এবং ক্লিন প্রফেশনাল শ্যাডো (`shadow-md`)।
  - টপ অ্যাকসেন্ট স্ট্রিপ: কার্ডের শীর্ষে সূক্ষ্ম এন্টারপ্রাইজ কোবাল্ট অ্যাকসেন্ট স্ট্রিপ (`h-1.5 bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-600`)।
  - জিওমেট্রি: কমপ্যাক্ট এন্টারপ্রাইজ রেডিয়াস (`rounded-sm` / 2-4px, `--radius-sm`)।
- **ব্র্যান্ডিং হেডার:**
  - লোগো কন্টেইনার: ডিপ স্লেট নেভি স্কয়ার ব্যাজ (`bg-slate-900`, `rounded-sm`) এবং ট্রেসফ্লো ভেক্টর মার্ক।
  - টাইটেল: `TraceFlow` টেক্সট (`text-2xl font-bold text-slate-900`) সাথে `RMG Core` ইন্ডাস্ট্রিয়াল ব্যাজ (`bg-blue-50 text-blue-700 border-blue-200 text-[10px] uppercase tracking-widest px-2 py-0.5 rounded-sm`)।
  - সাব-টাইটেল: `ENTERPRISE APPAREL OS` (`text-[11px] text-slate-500 tracking-wide uppercase`)।
  - হেডলাইন: `Sign In to Workspace` (`text-xl font-bold text-slate-900`) এবং সংক্ষিপ্ত বিবরণ।

---

### ২.২ ব্রাউজার রাউট ও সিকিউর রিডাইরেকশন নীতি (Browser Routing & Auth Guard)
- **লগইন পেজ ইউআরএল:** বাধ্যতামূলকভাবে প্রমিত `/login` হবে।
- **অথেনটিকেটেড ইউজার প্রবেশাধিকার নিষিদ্ধ (Guest-Only Guard - ইনভ্যারিয়েন্ট ১৮.১):** 
  - কোনো ইউজার একবার সফলভাবে লগইন থাকলে (Authenticated Session), সে কোনো পরিস্থিতিতেই `/login` পেজে প্রবেশ করতে পারবে না।
  - যদি কোনো অথেনটিকেটেড ইউজার ব্রাউজারে ম্যানুয়ালি `/login` লিখে এন্টার দেয়, সিস্টেম সাথে সাথে তাকে ইন্টারসেপ্ট করে তার নির্ধারিত কর্মক্ষেত্র বা ড্যাশবোর্ডে (`/platform/command-center` বা `/app/dashboard`) স্বয়ংক্রিয়ভাবে রিডাইরেক্ট করে দেবে।
- **লগআউট ও অননুমোদিত রিকোয়েস্ট:** লগআউট করার পর বা মেয়াদোত্তীর্ণ সেশনের ক্ষেত্রে সিস্টেম স্বয়ংক্রিয়ভাবে ব্যবহারকারীকে এই `/login` রুটে ফিরিয়ে আনবে।
- **ড্যাশবোর্ড রাউটিং:** সফল লগইনের পর ব্যাকএন্ডের `dashboard_target` রেসপন্স অনুযায়ী নির্দিষ্ট ড্যাশবোর্ডে রিডাইরেক্ট হবে।

---

### ২.৩ স্ক্রিন ফিল্ড ও লেবেল (ENTERPRISE-UI-COMPONENT-SYSTEM Category 15 Aligned)

| ফিল্ডের নাম | উপাদান টাইপ | আইকন / স্লট | প্লেসহোল্ডার / ভ্যালু | ভিজ্যুয়াল রূপ ও ভ্যালিডেশন |
|---|---|---|---|---|
| `Email or Username` | `Text Input (40px)` | `User (16px)` | `e.g. superadmin or operator@company.com` | হোয়াইট সারফেস (`bg-white border-slate-300 rounded-sm`), সার্ভার FormRequest |
| `Password` | `Password Input (40px)` | `Lock (16px)` + `Eye Toggle` | `••••••••••••` | হোয়াইট সারফেস, শো/হাইড আইকন বাটন, সার্ভার FormRequest |
| `Remember Session` | `Checkbox` | N/A | `Remember device for 30 days` | কমপ্যাক্ট ব্লু অ্যাকসেন্ট চেকবক্স (`accent-blue-600`) |
| `Forgot Password` | `Inline Link` | N/A | `Forgot password?` | প্রফেশনাল ব্লু টেক্সট লিংক (`text-blue-600 hover:text-blue-700`) |
| `Sign In Button` | `Primary Button (44px)` | `ArrowRight (16px)` | `Sign In to Platform` | প্রাইমারি ব্লু (`bg-blue-600 hover:bg-blue-700 rounded-sm text-white shadow-md`) |

---

### ২.৪ ভ্যালিডেশন ও এরর হ্যান্ডলিং রুলস (ইনভ্যারিয়েন্ট-২৭)
- `<form noValidate>` বাধ্যতামূলক যাতে ব্রাউজারের নেটিভ পপআপ বাধা দেওয়া যায়।
- ফিল্ডের নিচে সার্ভার-সাইড ভ্যালিডেশন এরর পরিষ্কার লাল টেক্সটে (`text-xs text-rose-600 font-medium`) প্রদর্শিত হবে।
- ইনভ্যালিড ক্রেডেনশিয়ালস বা সাধারণ অথেনটিকেশন ব্যর্থতার ক্ষেত্রে টপ-লেভেল অ্যালার্ট কার্ড (`<Alert variant="danger">`) প্রদর্শিত হবে।

---

### ২.৫ কুইক ডেমো সুইচ (Evaluation Quick-Fill Grid)
মূল্যায়নের সুবিধার জন্য সাইন-ইন কার্ডের নিচে একটি সুবিন্যস্ত কুইক-অ্যাকাউন্ট সিলেক্টর থাকবে:
1. **Superadmin (Platform Owner):** `superadmin` / `superadmin@traceflow.internal` (টার্গেট: `/platform/command-center`)
2. **DevOps Squad (Internal Core):** `backend.team` / `backend.team@traceflow.internal` (টার্গেট: `/platform/command-center`)
3. **Factory GM (Tenant Manager):** `factory.manager` / `factory.manager@fashiontex.com` (টার্গেট: `/app/dashboard`)

- **বাটন এস্থেটিক্স:** সাবটল লাইট স্লেট কার্ড (`bg-slate-50/80 border-slate-200 hover:bg-blue-50/60 hover:border-blue-400 rounded-sm`), টেক্সট ডার্ক স্লেট (`text-slate-800`), মনো ফন্ট ইউজারনেম।

---

### ২.৬ কার্ড ফুটার (Compliance & Hardware Note)
- কার্ডের নিচে সংরক্ষিত ফুটার স্ট্রিপ:
  - কন্টেইনার: `bg-slate-50/80 border-t border-slate-100 py-2.5 px-6 rounded-b-sm`
  - টেক্সট: `Hardware Protected • ISO 27001 Certified • TraceFlow-RMG v1.0` (`text-[10px] text-slate-400 font-medium`)

---

## ৩. প্রযুক্তিগত স্পেসিফিকেশন ও সিকিউরিটি

### ৩.১ অথেনটিকেশন ফ্লো ও এন্ডপয়েন্ট
- **Browser Route:** `/login`
- **HTTP Method:** `POST`
- **API Endpoint:** `/api/v1/auth/login`
- **Payload:**
```json
{
  "login": "superadmin", 
  "password": "SecretPlatform2026!",
  "device_name": "TraceFlow-Enterprise-Browser"
}
```
*(নোট: ব্যাকএন্ড ফর্মরিকোয়েস্টে `login` ফিল্ড গ্রহণ করবে যা ইমেইল অথবা ইউজারনেম যেকোনো একটি হতে পারে, ব্যাকওয়ার্ড কম্প্যাটিবিলিটির জন্য `email` কি-ও সাপোর্ট করবে)*

- **সফল রেসপন্স (`200 OK`):**
```json
{
  "data": {
    "token": "1|sanctum_bearer_token_string",
    "user": {
      "id": "01923e4f-7b12-7000-8000-000000000001",
      "name": "Super Admin",
      "email": "superadmin@traceflow.internal",
      "username": "superadmin",
      "roles": ["superadmin"]
    },
    "company": {
      "id": "01923e4e-1234-7000-8000-000000000001",
      "name": "Platform Root",
      "is_platform_host": true
    },
    "dashboard_target": "/platform/command-center"
  }
}
```
- **ব্যর্থ রেসপন্স (`422 Unprocessable Content`):**
```json
{
  "message": "These credentials do not match our records.",
  "errors": {
    "login": ["Invalid email/username or password provided."]
  }
}
```

---

## ৪. একসেপ্টেন্স ক্রাইটেরিয়া (Quality & Design Acceptance Criteria)

1. **ডিজাইন সিস্টেমের শতভাগ আনুগত্য:** ক্যানভাস, কার্ড, টেক্সট, বাটন ও ইনপুট শতভাগ `UI-UX-GUIDELINES.md` এবং `styles/variables.css` অনুযায়ী সফট স্লেট ও ব্লু-৬০০ প্রাইমারি ব্র্যান্ডেড হতে হবে।
2. **জিরো ইনলাইন সিএসএস:** কোনো জেএসএক্স উপাদানে `style={{ ... }}` থাকবে না।
3. **কমপ্যাক্ট কর্নার রেডিয়াস (INVARIANT-29):** কোনো উপাদানে `rounded-xl` বা `rounded-full` থাকবে না; কেবল কমপ্যাক্ট `rounded-sm` (2-4px) ব্যবহৃত হবে।
4. **জিরো ব্রাউজার বাবল (INVARIANT-27):** ফর্মে কোনো ব্রাউজার ডিফল্ট বাবল আসবে না; সব এরর আসবে সার্ভার ফর্মরিকোয়েস্ট থেকে।
5. **টাইপস্ক্রিপ্ট ও বিল্ড:** `npx tsc --noEmit` সম্পূর্ণ জিরো এরর ও জিরো ওয়ার্নিং সহ সম্পন্ন হতে হবে।
