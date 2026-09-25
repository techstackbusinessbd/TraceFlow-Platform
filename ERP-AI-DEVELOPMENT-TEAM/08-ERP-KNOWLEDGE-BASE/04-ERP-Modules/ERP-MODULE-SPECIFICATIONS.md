# TraceFlow-RMG: সম্পূর্ণ এন্টারপ্রাইজ ইআরপি মডিউল, সাবমডিউল ও ফিচার স্পেসিফিকেশন (Exhaustive Sub-Modules & Feature Matrix)
**নথি কোড:** TFRMG-KB-MOD-004  
**সংস্করণ:** ৫.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** এন্টারপ্রাইজ ইআরপি আর্কিটেকচার, বাউন্ডেড কনটেক্সট ও ফিচার স্পেক্স  
**অনুমোদনকারী:** ট্রেসফ্লো-আরএমজি প্রোডাক্ট আর্কিটেকচার সেল ও বিজনেস এনালিস্ট বোর্ড  
**প্রযোজ্য আর্কিটেকচার:** মাইক্রো-কার্নেল মডুলার মনোলিথ, ডোমেইন ড্রিভেন ডিজাইন (DDD), Zero-Trust Security  

---

## ১. ভূমিকা ও এন্টারপ্রাইজ মডিউল স্ট্রাকচার (System Hierarchy)

TraceFlow-RMG সিস্টেমের প্রতিটি ডোমেনকে কঠোরভাবে তিনটি স্তরে ভাগ করা হয়েছে:
$$\text{Core Module (কোর মডিউল)} \longrightarrow \text{Sub-Module (সাবমডিউল)} \longrightarrow \text{Granular Features (ফিচারসমূহ)}$$

এই ডকুমেন্টেশন অনুযায়ী ডাটাবেজ স্কিমা, পারমিশন ম্যাট্রিক্স, ইউআই মেনু/সাইডবার রাউটিং এবং এপিআই এন্ডপয়েন্ট গঠিত হবে।

```
                                  [ TRACEFLOW-RMG CORE PLATFORM ]
                                                 │
 ┌───────────────┬───────────────┼───────────────┼───────────────┬───────────────┐
 ▼               ▼               ▼               ▼               ▼               ▼
MOD-00          MOD-01          MOD-02          MOD-03          MOD-04          MOD-05...
(IAM & Auth)   (Master Data)   (Merchandising) (Inventory)     (Cutting Floor) (Sewing & Line)
 │               │               │               │               │               │
 ├─ Sub-00.1     ├─ Sub-01.1     ├─ Sub-02.1     ├─ Sub-03.1     ├─ Sub-04.1     ├─ Sub-05.1
 ├─ Sub-00.2     ├─ Sub-01.2     ├─ Sub-02.2     ├─ Sub-03.2     ├─ Sub-04.2     ├─ Sub-05.2
 ├─ Sub-00.3     ├─ Sub-01.3     ├─ Sub-02.3     ├─ Sub-03.3     ├─ Sub-04.3     ├─ Sub-05.3
 └─ Sub-00.4     └─ Sub-01.4     └─ Sub-02.4     └─ Sub-03.4     └─ Sub-04.4     └─ Sub-05.4
```

---

## ২. মডিউল, সাবমডিউল ও বিস্তারিত ফিচার রেজিস্ট্রি (Comprehensive Breakdown)

---

### মডিউল ০০: Identity, Access Management & Security Engine (`MOD-00`)

#### সাবমডিউল ০০.১: Authentication & Single Sign-On (Auth Engine)
* **ফিচারসমূহ:**
  1. **Multi-Guard Login:** ওয়েব ড্যাশবোর্ডের জন্য সেশন এবং মোবাইল/ট্যাবলেটের জন্য Bearer Token Auth।
  2. **Operator Fast RFID/PIN Switch:** ফ্লোর টাচপ্যাডে আরএফআইডি কার্ড স্ক্যান অথবা ৪-সংখ্যার কুইক পিন দিয়ে ১ সেকেন্ডে টার্মিনাল অপারেটর লগইন/হ্যান্ডওভার।
  3. **Multi-Factor Auth (MFA/2FA):** সুপার অ্যাডমিন, মার্চেন্ডাইজিং হেড এবং ফাইন্যান্স অ্যাকাউন্টের জন্য গুগল অথেনটিকেটর TOTP বাধ্যবাধকতা।
  4. **Brute-Force & Bot Shield:** পরপর ৫ বার ব্যর্থ লগইনে ১৫ মিনিটের জন্য স্বয়ংক্রিয় আইপি ও অ্যাকাউন্ট থ্রোটলিং।
  5. **Password Policy Engine:** পাসওয়ার্ডের মেয়াদ, জটিলতা (Min 8 Chars, Special Char, Number) এবং হিস্টোরি রি-ইউজ ব্লক।

#### সাবমডিউল ০০.২: User Lifecycle & Employee Directory
* **ফিচারসমূহ:**
  1. **User Profile & HR Sync:** এমপ্লয়ি আইডি, ছবি, বিভাগ, ডেজিগনেশন, শিফট ও যোগাযোগ নম্বর ম্যানেজমেন্ট।
  2. **Instant Status Suspension:** কোনো কর্মী চাকরি ছাড়লে বা বদলি হলে এক ক্লিকে তার সমস্ত ডিভাইসের অ্যাক্টিভ সেশন ও এপিআই টোকেন তাৎক্ষণিক বাতিল (Revoke All Tokens)।
  3. **Device & IP Binding:** শুধুমাত্র অনুমোদিত ফ্লোর ট্যাবলেট বা নির্দিষ্ট ফ্যাক্টরি নেটওয়ার্ক আইপি থেকে লগইন সীমাবদ্ধ করার পলিসি।

#### সাবমডিউল ০০.৩: Dynamic RBAC (Role-Based Access Control)
* **ফিচারসমূহ:**
  1. **Zero-Hardcoded Role Creator:** সুপার অ্যাডমিন প্যানেল থেকে যেকোনো নাম ও কোডে নতুন ভূমিকা (Role) তৈরি (যেমন: `Cutting In-charge`, `Buyer QA Auditor`)।
  2. **Role Hierarchy & Cloning:** বিদ্যমান রোলের পারমিশন ক্লোন করে নতুন স্পেশালাইজড সাব-রোল তৈরি করার ব্যবস্থা।
  3. **Bulk User-Role Mapping:** এক ক্লিকে নির্দিষ্ট বিভাগের সকল কর্মীকে একটি নির্দিষ্ট রোলে অ্যাসাইন বা ডি-অ্যাসাইন করা।

#### সাবমডিউল ০০.৪: Granular Permission Matrix Engine
* **ফিচারসমূহ:**
  1. **Action Matrix per Resource:** প্রতিটি সাবমডিউলের প্রতিটি রিসোর্সে ৬টি প্রমিত পারমিশন: `View`, `Create`, `Edit`, `Delete`, `Approve`, `Export`।
  2. **Temporary Privilege Escalation:** নির্দিষ্ট কাজের জন্য নির্দিষ্ট সময়ের (যেমন: ২ ঘণ্টা) জন্য বিশেষ পারমিশন অনুমোদন ও অটো-এক্সপায়ার।
  3. **ABAC Scope Assignment (Tenant Guard):** ইউজারের রোলের সাথে নির্দিষ্ট ফ্যাক্টরি ইউনিট আইডি, ফ্লোর আইডি এবং লাইন আইডি ট্যাগ করা, যাতে অন্য ইউনিটের ডাটা দেখা বা মডিফাই করা অসম্ভব হয়।

#### সাবমডিউল ০০.৫: Security Audit & Session Tracking
* **ফিচারসমূহ:**
  1. **Active Concurrent Session Monitor:** বর্তমানে কারা কারা সিস্টেমে লগইন রয়েছে তার রিয়েল-টাইম তালিকা ও অ্যাডমিন দ্বারা রিমোট কিল।
  2. **Security Event Logging:** পাসওয়ার্ড চেঞ্জ, পারমিশন বদল, ব্যর্থ অথেনটিকেশনের অপরিবর্তনীয় নিরাপত্তা লগ।

---

### মডিউল ০১: Master Data & Dynamic Configuration (`MOD-01`)

#### সাবমডিউল ০১.১: Zero-Hardcode Lookup Engine (Dynamic Choices)
* **ফিচারসমূহ:**
  1. **Category Management:** `Defect Types`, `Garment Types`, `Wash Types`, `Fabric Constructions`, `Buyer Brands` ইত্যাদি ক্যাটাগরি তৈরি।
  2. **Lookup Key-Value Registry:** প্রতিটি ক্যাটাগরির অধীনে ডায়নামিক অপশন, কালার কোড ও মেটাডাটা সংযোজন।
  3. **Tagged Redis Invalidation:** অ্যাডমিন প্যানেলে কোনো অপশন পরিবর্তনের সাথে সাথে ব্যাকএন্ডের রেডিস ক্যাশ মাইক্রোসেকেন্ডে অটো-ফ্লাশ।

#### সাবমডিউল ০১.২: Organizational Hierarchy & Infrastructure
* **ফিচারসমূহ:**
  1. **Multi-Company Structure:** কোম্পানি $\rightarrow$ বিজনেস ইউনিট $\rightarrow$ ফ্যাক্টরি প্ল্যান্ট $\rightarrow$ বিল্ডিং $\rightarrow$ ফ্লোর $\rightarrow$ সুইং লাইন হায়ারার্কি।
  2. **Production Line Profiling:** লাইনের নাম, মোট মেশিন ক্যাপাসিটি, ডিফল্ট ওয়ার্কার সংখ্যা ও সুপারভাইজার ট্যাগিং।
  3. **Shift & Working Calendar:** ফ্যাক্টরির সাপ্তাহিক ছুটি, সরকারি ছুটি এবং জেনারেল/ওভারটাইম শিফট টাইমিং কনফিগারেশন।

#### সাবমডিউল ০১.৩: System Operations Parameters
* **ফিচারসমূহ:**
  1. **Global Production Settings:** ডিফল্ট কাটিং শ্রিঙ্কেজ এলাউন্স লিমিট, বান্ডেল ডিফল্ট সাইজ, বারকোড প্রিফিক্স রুলস।
  2. **Document Numbering Sequencing:** পিও, জিআরএন, বান্ডেল কার্ড, ইনভয়েসের প্রিফিক্স ও সিকোয়েন্স জেনারেটর (e.g., `TF-PO-2026-0001`)।

#### সাবমডিউল ০১.৪: Universal Audit Trail Engine
* **ফিচারসমূহ:**
  1. **Model Change Tracker:** প্রতিটি ডাটাবেজ রেকর্ডের কে, কখন, কোন আইপি থেকে কোন ফিল্ড পরিবর্তন করেছে তার বিফোর/আফটার ডিফল্ট লগ।
  2. **Visual Audit Log Viewer:** ইউজার ইন্টারফেসে হিস্টোরি টাইমলাইন ভিউয়ার।

---

### মডিউল ০২: Merchandising, Costing & Order Execution (`MOD-02`)

#### সাবমডিউল ০২.১: Buyer, Brand & Season Management
* **ফিচারসমূহ:**
  1. **Buyer Directory:** বায়ার নাম, দেশ, পেমেন্ট টার্মস (LC/TT), পোর্ট অব ডিসচার্জ এবং মার্চেন্ডাইজার পোর্টফোলিও।
  2. **Brand & Season Matrix:** বায়ারের বিভিন্ন সাব-ব্র্যান্ড এবং সিজন (Spring/Summer, Autumn/Winter) সাইকেল কনফিগারেশন।

#### সাবমডিউল ০২.২: Tech-Pack, Style Profiling & Measurement Chart
* **ফিচারসমূহ:**
  1. **Style Master:** স্টাইল নম্বর, স্টাইল নাম, গার্মেন্টস ক্যাটাগরি (Polo, T-Shirt, Denim), ফেব্রিক টাইপ ও স্যাম্পল ফটো আপলোড।
  2. **Measurement Spec Sheet:** সাইজ-ভিত্তিক গ্রেডিং মেজারমেন্ট চার্ট (Chest, Length, Sleeve ইত্যাদি) এবং টলারেন্স লিমিট (+/- cm)।
  3. **Tech-Pack Document Vault:** বায়ারের দেওয়া মূল পিডিএফ ও টেক-প্যাক ক্লাউড অবজেক্ট স্টোরেজে (MinIO/S3) ভার্সন কন্ট্রোল সহ সংরক্ষণ।

#### সাবমডিউল ০২.৩: Bill of Materials (BOM) & Pre-Costing Engine
* **ফিচারসমূহ:**
  1. **Fabric Consumption Engine:** মার্কার এফিসিয়েন্সি এবং ফেব্রিক জিএসএম অনুযায়ী প্রতি ডজনে ফেব্রিকের কেজি/গজ চাহিদা হিসাব।
  2. **Trims & Accessories BOM:** থ্রেড, বাটন, জিপার, কেয়ার লেবেল, পলিব্যাগ, হ্যাংট্যাগ ও কার্টনের কনসাম্পশন ও ইউনিট রেট নির্ধারণ।
  3. **Cost of Making (CM) & Overhead:** এসএমভি (SMV) ও লাইন এফিসিয়েন্সি অনুযায়ী প্রোডাকশন খরচ হিসাব।
  4. **Target FOB Price Estimator:** কারখানার প্রফিট মার্জিন, কমিশন ও ফ্রেট খরচ যুক্ত করে চূড়ান্ত এফওবি রেট নির্ধারণ ও ম্যানেজমেন্ট এপ্রুভাল ওয়ার্কফ্লো।

#### সাবমডিউল ০২.৪: Purchase Order (PO) & Dynamic Matrix Engine
* **ফিচারসমূহ:**
  1. **Order PO Profiling:** বায়ার পিও নম্বর, অর্ডার প্লেসমেন্ট ডেট, ক্যানসেলেশন ডেট এবং টার্গেট এক্স-ফ্যাক্টরি ডেট।
  2. **Slide-Over Drawer Matrix (Color/Size Breakdown):** কোনো পপআপ ছাড়া মসৃণ স্লাইড-ওভার ড্রয়ারে ডাবল-অ্যাক্সিস ম্যাট্রিক্স গ্রিডে কালার ও সাইজ ওয়াইজ কোয়ান্টিটি এন্ট্রি ও লাইভ অটো-সাম।
  3. **PO Amendment & History:** বায়ার অর্ডার পরিবর্তনের ক্ষেত্রে রিভিশন ট্র্যাক (Rev 1, Rev 2) এবং পরিবর্তন অডিট।

#### সাবমডিউল ০২.৫: Time & Action (T&A) Calendar & Milestone Tracker
* **ফিচারসমূহ:**
  1. **Critical Path Template:** অর্ডার কনফার্মেশন থেকে শিপমেন্ট পর্যন্ত ২৫টি স্ট্যান্ডার্ড আরএমজি মাইলস্টোন তৈরি।
  2. **Dynamic Lead-Time Calculation:** শিপমেন্ট ডেট থেকে ব্যাকওয়ার্ড ক্যালকুলেশনে ফেব্রিক বুকিং, পিপি মিটিং ও কাটিং শুরুর ডেডলাইন সেট করা।
  3. **T&A Visual Gantt & Bottleneck Alert:** লাল/হলুদ কালার কোডেড ট্র্যাকিং এবং ডেডলাইন মিস হলে সংশ্লিষ্ট মার্চেন্ডাইজার ও ম্যানেজমেন্টকে রিয়েল-টাইম নোটিফিকেশন।

#### সাবমডিউল ০২.৬: Sample Tracking & Buyer Feedback
* **ফিচারসমূহ:**
  1. **Sample Development Workflow:** Proto, Fit, Size-Set, PP (Pre-Production) স্যাম্পল রিকুইজিশন তৈরি।
  2. **Sample Room Dispatch & Comments:** বায়ারের কাছে স্যাম্পল পাঠানো, ট্র্যাকিং কুরিয়ার নম্বর এবং বায়ারের কমেন্টস/অ্যাপ্রুভাল আপলোড।

---

### মডিউল ০৩: Fabric & Trims Inventory / Warehouse (`MOD-03`)

#### সাবমডিউল ০৩.১: Material Inward & Good Receipt Note (GRN)
* **ফিচারসমূহ:**
  1. **Inward Delivery Chalan Entry:** সাপ্লায়ারের চালান ও পারচেজ অর্ডার ম্যাচিং করে কাঁচামাল আনলোডিং লগ।
  2. **Electronic GRN Generation:** সুতা, ফেব্রিক ও ট্রিমসের জন্য স্বতন্ত্র ব্যাচ নম্বর সহ ডিজিটাল জিআরএন ভাউচার তৈরি।
  3. **Excess/Shortage Tolerance Check:** পিও কোয়ান্টিটির সাথে সরবরাহকৃত পরিমাণের তারতম্য (+/- 3%) স্বয়ংক্রিয় ভ্যালিডেশন।

#### সাবমডিউল ০৩.২: Fabric Quality Inspection (4-Point System)
* **ফিচারসমূহ:**
  1. **Roll-by-Roll 4-Point Inspection:** পরিদর্শন মেশিনে প্রতিটি রোলের দৈর্ঘ্য, প্রস্থ, ডিফেক্ট পয়েন্ট (1 to 4 pts) এন্ট্রি।
  2. **Auto Defect Points / 100 Sq. Yards Calculator:** সফটওয়্যার স্বয়ংক্রিয়ভাবে পয়েন্ট ক্যালকুলেট করে রোল স্ট্যাটাস (Accepted / Rejected) নির্ধারণ করবে।
  3. **GSM & Moisture Testing Log:** ফেব্রিকের বিভিন্ন পয়েন্টে জিএসএম ও আর্দ্রতার পরিমাপ সংরক্ষণ।

#### সাবমডিউল ০৩.৩: Shade Sorting, Shrinkage & Batch Allocation
* **ফিচারসমূহ:**
  1. **Washing Shrinkage Grouping:** ওয়াশ টেস্টের পর দৈর্ঘ্য ও প্রস্থের শ্রিঙ্কেজ পার্সেন্টেজ অনুযায়ী রোলগুলোকে লট গ্রুপিং (e.g., Lot 1: -2%, Lot 2: -4%)।
  2. **Shade Band Classification:** লাইটবক্সে শেড ভ্যারিয়েশন দেখে রোল অনুযায়ী A, B, C, D শেড ট্যাগিং।

#### সাবমডিউল ০৩.৪: Warehouse Bin & Rack Coordinate Locator
* **ফিচারসমূহ:**
  1. **Visual Warehouse Map:** ওয়্যারহাউজের জোন, র‍্যাক, রো ও বিন (Zone-Rack-Row-Bin) লোকেশন ম্যানেজমেন্ট।
  2. **Barcode Bin Allocation:** প্রতিটি ফেব্রিক রোল বা ট্রিমস কার্টন কোন বিনে রাখা হয়েছে তা হ্যান্ডহেল্ড স্ক্যানার দিয়ে স্ক্যান ও অ্যাসাইন।

#### সাবমডিউল ০৩.৫: Requisition & FIFO Store Issue
* **ফিচারসমূহ:**
  1. **Cutting Floor Fabric Requisition:** কাটিং লে প্ল্যান অনুযায়ী নির্দিষ্ট কালার, শেড ও লটের ফেব্রিক রিকুইজিশন তৈরি।
  2. **FIFO Validation Guard:** আগে আসা রোল আগে ছাড়ার (First-In, First-Out) বাধ্যবাধকতা। ভুল রোল স্ক্যান করলে সিস্টেমে রিলিজ ব্লক।
  3. **Trims Floor Issue Slip:** সুইং লাইনের জন্য বাটন, সুতা ও লেবেল নির্দিষ্ট স্টাইল ও পিও অনুযায়ী ইস্যু ও ব্যালেন্স ট্র্যাকিং।

---

### মডিউল ০৪: CAD, Spreading & Cutting Floor Execution (`MOD-04`)

#### সাবমডিউল ০৪.১: CAD Marker Planning & Ratio Optimization
* **ফিচারসমূহ:**
  1. **Marker Master & File Upload:** সিএডি সফ্টওয়্যার (Gerber/Lectra) থেকে মার্কার দৈর্ঘ্য, প্রস্থ ও ডাইরেকশন ডাটাবেজে এন্ট্রি।
  2. **Marker Efficiency & Consumption Analyzer:** মার্কারের কাঙ্ক্ষিত এফিসিয়েন্সি (%) এবং প্ল্যানড লে সংখ্যা রেকর্ড করা।
  3. **Ratio Breakdown:** একটি মার্কারে কোন সাইজের কয়টি বডি রয়েছে তার রেশিও কনফিগারেশন (e.g., S:1, M:2, L:2, XL:1)।

#### সাবমডিউল ০৪.২: Fabric Spreading & Lay Planning
* **ফিচারসমূহ:**
  1. **Lay Order Sheet:** কাটিং টেবিল নম্বর, বরাদ্দকৃত ফেব্রিক রোল ও কাঙ্ক্ষিত প্লাই (Ply) সংখ্যা প্ল্যানিং।
  2. **Roll-to-Lay Scanning:** প্রতিটি রোল টেবিলে তোলার আগে বারকোড স্ক্যান করে সঠিক শেড ও লট যাচাই।
  3. **End-Bit & Rejection Tracker:** রোলের শেষ প্রান্তের অবশিষ্টাংশ (End-bit wastage) ও ডিফেক্টিভ পার্টস ওজন ও গজ আকারে রেকর্ড।

#### সাবমডিউল ০৪.৩: Automated Bundle Ticket & UUID v7 Barcode Engine
* **ফিচারসমূহ:**
  1. **Intelligent Bundle Generator:** মোট প্লাই সংখ্যাকে নির্ধারিত বান্ডেল সাইজে (যেমন: ১০ বা ২০ পিস) স্বয়ংক্রিয় ব্রেকডাউন।
  2. **UUID v7 Sequential Barcode/QR Generation:** প্রতিটি বান্ডেলের জন্য ইউনিক, টাইম-সর্টেড ক্রিপ্টোগ্রাফিক কিউআর কোড তৈরি।
  3. **Direct ZPL II / ESC-POS Printing:** নেটওয়ার্ক বারকোড প্রিন্টারে সরাসরি হাই-স্পিড বান্ডেল টিকেট প্রিন্ট কমান্ড প্রেরণ।

#### সাবমডিউল ০৪.৪: Ply Numbering & Cut Piece Ticketing
* **ফিচারসমূহ:**
  1. **Garment Body Part Breakdown:** প্রতিটি বান্ডেলের উপাদান (Front, Back, Sleeve, Collar, Pocket) অনুযায়ী স্বতন্ত্র পিস স্টিকার জেনারেট।
  2. **Sequential Ply Sticker:** বান্ডেল সাইজ ১০ হলে ১ থেকে ১০ পর্যন্ত ক্রমিক নম্বর দিয়ে পিস স্টিকার যাতে সেলাইয়ের সময় প্লাই মিক্স না হয়।

#### সাবমডিউল ০৪.৫: Parts Sorting, Fusing & Embellishment Tracking
* **ফিচারসমূহ:**
  1. **Collar/Cuff Fusing Dispatch & Receive:** ফিউজিং সেকশনে পার্টস পাঠানো এবং কোয়ালিটি চেক করে ফেরত পাওয়ার ট্র্যাকিং।
  2. **Embroidery / Printing Job Outward & Inward:** বাইরে বা ভেতরের প্রিন্টিং সেকশনে কাটিং পার্টস পাঠানো, লস/রিজেকশন এবং শতভাগ রিকনসিলিয়েশন।

---

### মডিউল ০৫: Sewing Floor Execution, Line Loading & Real-Time Andon (`MOD-05`)

#### সাবমডিউল ০৫.১: Operation Breakdown (OB) & Line Layout
* **ফিচারসমূহ:**
  1. **OB Sheet Master:** সুইং স্টাইলের প্রতিটি অপারেশন (Join Shoulder, Set Collar, Hemming ইত্যাদি) সিকোয়েন্স তৈরি।
  2. **SMV & Target Allocation:** প্রতিটি অপারেশনের স্ট্যান্ডার্ড মিনিট ভ্যালু (SMV), প্রয়োজনীয় মেশিনের ধরন (Single Needle, Overlock, Flatlock) নির্ধারণ।
  3. **Operator Skill Matrix:** অপারেটরদের দক্ষতা গ্রেড (A, B, C) অনুযায়ী নির্দিষ্ট মেশিনে লাইন অ্যাসাইনমেন্ট।

#### সাবমডিউল ০৫.২: Line Loading & Bundle Ingestion
* **ফিচারসমূহ:**
  1. **Sewing Line Input Scan:** কাটিং থেকে আসা বান্ডেল লাইনের শুরুতে স্ক্যান করে সুইং ইনপুট রেজিস্টার করা।
  2. **WIP (Work-in-Progress) Real-Time Ledger:** লাইনের ভেতরে বর্তমানে কত পিস কাপড় প্রসেসিংয়ে আছে তার লাইভ কাউন্ট।

#### সাবমডিউল ০৫.৩: Hourly Production Tracking (Live Terminal)
* **ফিচারসমূহ:**
  1. **Hour-by-Hour Counter:** সকাল ৮টা থেকে প্রতি ঘণ্টার নির্ধারিত টার্গেট বনাম প্রকৃত উৎপাদনের স্বয়ংক্রিয় হিসেব।
  2. **End-of-Line Production Scan:** লাইনের শেষ প্রান্তে গার্মেন্টস কিউসি পাস হওয়ার সাথে সাথে প্রোডাকশন আউটপুট ডাটাবেজে ইনক্রিমেন্ট।
  3. **Target Variance Alerts:** কোনো ঘণ্টায় টার্গেটের ২০% কম প্রোডাকশন হলে লাইন ইন-চার্জের কাছে তাৎক্ষণিক পুশ নোটিফিকেশন।

#### সাবমডিউল ০৫.৪: Operator Efficiency & Productivity Engine
* **ফিচারসমূহ:**
  1. **Live Efficiency Calculator:** $\text{Efficiency} = \frac{\text{Produced Pieces} \times \text{SMV}}{\text{Total Operators} \times \text{Working Minutes}} \times 100\%$ সূত্রে লাইভ পার্সেন্টেজ জেনারেশন।
  2. **Operator Performance Scoreboard:** কোন অপারেটর কত এফিসিয়েন্টলি কাজ করছে তার দৈনিক পারফরম্যান্স রেকর্ড।

#### সাবমডিউল ০৫.৫: Real-Time Floor Andon Board (WebSockets)
* **ফিচারসমূহ:**
  1. **Large Screen TV Andon View:** সুইং ফ্লোরে ঝুলানো টিভিতে পেজ রিফ্রেশ ছাড়াই WebSockets (Laravel Reverb) দিয়ে সেকেন্ডে সেকেন্ডে লাইভ ডাটা আপডেট।
  2. **Bottleneck & Breakdown Alert:** কোনো মেশিনে যান্ত্রিক ত্রুটি দেখা দিলে ট্যাবলেট থেকে বোতাম টিপলে অ্যান্ডন বোর্ডে লাল সংকেত ও সাইরেন অ্যালার্ট।

---

### মডিউল ০৬: Quality Assurance & Defect Alteration Engine (`MOD-06`)

#### সাবমডিউল ০৬.১: In-Line QC & Roaming Inspection
* **ফিচারসমূহ:**
  1. **Roaming QC Traffic Light Check:** সুইং লাইনের ভেতরে পরিদর্শকের জন্য গ্রিন/ইয়েলো/রেড অডিট টুল।
  2. **Early Defect Catch:** লাইনের মাঝামাঝি ত্রুটি চিহ্নিত করে সাথে সাথে সংশ্লিষ্ট সুইং অপারেটরকে সতর্ক করা।

#### সাবমডিউল ০৬.২: End-Line 100% Quality Inspection (Tablet Touchpad)
* **ফিচারসমূহ:**
  1. **Visual Garment Defect Mapper:** ট্যাবলেটের পর্দায় পোশাকের স্কেচ ছবিতে সরাসরি টাচ করে ডিফেক্টের স্থান মার্কিং (Collar, Armhole, Hem)।
  2. **Defect Code Library:** স্কিপ স্টিচ, ব্রোকেন স্টিচ, ওপেন সিম, আন-ইভেন, স্পট ইত্যাদির ড্রপডাউন নির্বাচন।
  3. **1-Second Fast Pass:** কোনো ত্রুটি না থাকলে মাত্র ১টি স্ক্রিন ট্যাপে 'QC PASS' স্ট্যাটাস ও আউটপুট ডাটাবেজ আপডেট।

#### সাবমডিউল ০৬.৩: Defect, Alter & Rework Cycle (Traceability)
* **ফিচারসমূহ:**
  1. **Defect Barcode Tagging:** ত্রুটিযুক্ত পোশাকে অল্টার স্টিকার লাগানো এবং অল্টারম্যানের কাছে প্রেরণ।
  2. **Rework Completion & Re-Inspection:** মেরামত শেষে পোশাকটি পুনরায় কিউসি ডেস্কে স্ক্যান করে পাস বা ফাইনাল রিজেক্ট করা।
  3. **DHU (Defects per Hundred Units) Calculator:** লাইনের দৈনিক ও ঘণ্টাওয়ারি DHU এবং রিজেকশন পার্সেন্টেজ রিপোর্ট।

#### সাবমডিউল ০৬.৪: Pre-Shipment & Final AQL Audit
* **ফিচারসমূহ:**
  1. **AQL Sampling Standards (ISO 2859-1):** অর্ডার সাইজ অনুযায়ী নরমাল/টাইটেন্ড ইন্সপেকশনের স্যাম্পল সাইজ এবং গ্রহণযোগ্য ডিফেক্ট সংখ্যা (AQL 1.5, 2.5, 4.0) স্বয়ংক্রিয় গণনা।
  2. **Pass/Fail Certificate Generation:** বায়ার কিউএ অডিটের পর ইলেকট্রনিক সিগনেচার সহ এন্ট্রি ও এআইসিএল সার্টিফিকেট ইস্যু।

---

### মডিউল ০৭: Finishing, Packing & Cartonization Engine (`MOD-07`)

#### সাবমডিউল ০৭.১: Finishing Receiving & Washing Plant Logistics
* **ফিচারসমূহ:**
  1. **Finishing Inward Scan:** সুইং ফ্লোর থেকে ১০০% পাস গার্মেন্টস রিসিভ করে ফিনিশিং ডেক্সে লোড।
  2. **Washing Dispatch & Delivery Chalan:** লন্ড্রি প্ল্যান্টে পাঠানো, ওয়াশ সাইকেল ট্র্যাকিং এবং ফিরে আসার পর ড্যামেজ রিকনসিলিয়েশন।

#### সাবমডিউল ০৭.২: Ironing, Poly Packing & Price Hangtagging
* **ফিচারসমূহ:**
  1. **Ironing Output Counter:** ইস্ত্রি সম্পন্ন হওয়া কাপড়ের সংখ্যা ও অপারেটর ওয়াইজ ট্র্যাকিং।
  2. **Price Tag & Barcode Verification:** বায়ারের ইউপিসি (UPC/EAN) প্রাইস স্টিকার স্ক্যান করে সঠিক স্টাইল ও সাইজ মিলিয়ে পলিব্যাগে সিলিং।

#### সাবমডিউল ০৭.৩: Metal Detection & Safety Protocol
* **ফিচারসমূহ:**
  1. **9-Point Calibration Log:** প্রতি শিফট শুরুর আগে মেটাল ডিটেক্টর মেশিনের ক্যালিব্রেশন টেস্ট রেকর্ড।
  2. **Needle Fragment Detection:** কাপড়ের ভেতর কোনো সুঁইয়ের ভাঙা কণা ধরা পড়লে তাৎক্ষণিক অ্যালার্ট এবং আইসোলেশন বক্সে লক।

#### সাবমডিউল ০৭.৪: Cartonization & Assortment Matrix Packing
* **ফিচারসমূহ:**
  1. **Solid Packing Mode:** একটি কার্টনে নির্দিষ্ট একটি সাইজ ও কালার পূর্ণ করার লজিক।
  2. **Ratio Assorted Packing Mode:** জটিল বায়ার রেশিও (যেমন: Black: S-2, M-4, L-4; মোট ১০ পিস) অনুযায়ী স্ক্যান করার সময় ভুল পিস দিলে লাল এরর ও অডিও বিপ সাউন্ড।
  3. **Carton Closure & Status Lock:** কার্টনের ক্যাপাসিটি পূরণ হলে কার্টন ক্লোজ করা এবং সিস্টেম থেকে অটোমেটিক **SSCC-18** কার্টন বারকোড প্রিন্ট।

#### সাবমডিউল ০৭.৫: Weighing Scale IoT Verification (RS-232)
* **ফিচারসমূহ:**
  1. **Electronic Scale Interfacing:** ডিজিটাল ওজন মাপার স্কেলের সাথে সরাসরি সিরিয়াল কম-পোর্ট (RS-232) দিয়ে ওজন ডাটা রিড।
  2. **Standard Weight Tolerance Engine:** পোশাকের স্ট্যান্ডার্ড ওজন + কার্টনের খালি ওজনের তুলনায় বর্তমান ওজন টলারেন্সের (+/- 50 গ্রাম) মধ্যে আছে কিনা তা যাচাই। ওজন অমিল হলে কার্টনের স্ট্যাটাস হোল্ড।

---

### মডিউল ০৮: Commercial, Shipping & Export Logistics (`MOD-08`)

#### সাবমডিউল ০৮.১: Export Invoicing & Final Packing List (PL)
* **ফিচারসমূহ:**
  1. **Automated Packing List Generator:** ফিনিশিং সেকশনের বন্ধ হওয়া সকল কার্টনের নেট ওজন, গ্রস ওজন, সিবিএম (CBM) হিসাব করে শতভাগ নির্ভুল বায়ার প্যাকিং লিস্ট প্রস্তুত।
  2. **Commercial Invoice Builder:** পারচেজ অর্ডার, এইচএস কোড (HS Code), কারেন্সি ও ইনকোটার্মস (FOB, CIF, CFR) সহ কমার্শিয়াল ইনভয়েস জেনারেশন।

#### সাবমডিউল ০৮.২: Container Stuffing & Dispatch Verification
* **ফিচারসমূহ:**
  1. **Stuffing Plan & Capacity Calculator:** ২০ ফুট বা ৪০ ফুট হাই-কিউব কন্টেইনারে কত কার্টন ধরবে তার 3D/সিবিএম প্ল্যানিং।
  2. **Gate-Pass & Container Loading Scan:** ট্রাকে বা কন্টেইনারে কার্টন তোলার সময় সরাসরি বারকোড স্ক্যান; কোনো ভুল অর্ডারের কার্টন ট্রাকে লোড করার চেষ্টা করলে সাইরেন অ্যালার্ট।

#### সাবমডিউল ০৮.৩: Letter of Credit (L/C) & Banking Realization
* **ফিচারসমূহ:**
  1. **Master L/C Registry:** বায়ার এল/সি নম্বর, ইস্যুয়িং ব্যাংক, অ্যামাউন্ট, শিপমেন্ট এক্সপায়ারি ও নেগোসিয়েশন ডেট ট্র্যাকিং।
  2. **Back-to-Back (B2B) L/C Allocation:** ফেব্রিক ও ট্রিমস সাপ্লায়ারদের জন্য ব্যাক-টু-ব্যাক এল/সি লিয়েন এবং পেমেন্ট ডিউ ডেট ট্র্যাকিং।

#### সাবমডিউল ০৮.৪: Shipping Documents & Forwarder Handoff
* **ফিচারসমূহ:**
  1. **Forwarder Delivery & Tracking:** ফ্রেইট ফরোয়ার্ডারের কাছে কার্গো রিসিভ হ্যান্ডওভার ডক।
  2. **Bill of Lading (B/L) & EXP Form:** কাস্টমস ইএক্সপি (EXP) ফরম এবং বি/এল রেকর্ড ডিজিটালাইজেশন।

---

### মডিউল ০৯: Production Costing, Payroll & Financial Ledger (`MOD-09`)

#### সাবমডিউল ০৯.১: Order Post-Costing & Profitability Analysis
* **ফিচারসমূহ:**
  1. **Actual vs Budgeted Cost Comparison:** প্রি-কস্টিং বাজেটের বিপরীতে প্রকৃত ফেব্রিক ব্যবহার, এক্সেসরিজ খরচ এবং সিএম ব্যয়ের তুলনা।
  2. **Net Contribution & Margin Report:** প্রতিটি বায়ার অর্ডারে নিট কত ডলার মুনাফা বা ক্ষতি হয়েছে তার স্বয়ংক্রিয় ব্যালেন্স শিট।

#### সাবমডিউল ০৯.২: Piece-Rate & Operator Incentive Payroll
* **ফিচারসমূহ:**
  1. **Barcode-Based Piece Rate Engine:** যেসকল অপারেশনে পিস-রেট মজুরি প্রযোজ্য, তাদের স্ক্যান হিস্টোরি থেকে স্বয়ংক্রিয় মজুরি ক্যালকুলেট।
  2. **Over-Target Production Incentive:** নির্ধারিত টার্গেটের চেয়ে বেশি উৎপাদনকারী অপারেটর ও লাইন কর্মীদের বোনাস তালিকা প্রস্তুত।

#### সাবমডিউল ০৯.৩: Cost of Poor Quality (COPQ) Audit
* **ফিচারসমূহ:**
  1. **Financial Defect Loss Ledger:** কাপড়ের অপচয়, অল্টারেশনের কাজের সময় এবং ফাইনাল রিজেকশনের কারণে টাকার অঙ্কে কত ক্ষতি হয়েছে তার অডিট রিপোর্ট।

---

## ৩. এন্টারপ্রাইজ মডিউল, সাবমডিউল ও ডাটাবেজ ম্যাপিং সামারি

| মডিউল কোড | মডিউল শিরোনাম | সাবমডিউল তালিকা | মূল ডাটাবেজ এনটিটিসমূহ (UUID v7 PK) |
|---|---|---|---|
| **MOD-00** | Identity, Auth & Access Control | 00.1 Auth, 00.2 Users, 00.3 RBAC, 00.4 Permissions, 00.5 Audit | `users`, `roles`, `permissions`, `role_has_permissions`, `user_factory_scopes`, `login_audit_logs` |
| **MOD-01** | Master Data Engine | 01.1 Lookups, 01.2 Org Hierarchy, 01.3 Settings, 01.4 Audit Trail | `lookup_categories`, `lookup_values`, `companies`, `factory_lines`, `system_parameters` |
| **MOD-02** | Merchandising & Costing | 02.1 Buyer/Brand, 02.2 Tech-Pack, 02.3 BOM, 02.4 PO Matrix, 02.5 T&A, 02.6 Samples | `buyer_styles`, `bill_of_materials`, `buyer_purchase_orders`, `po_color_sizes`, `tna_milestones` |
| **MOD-03** | Fabric & Trims Inventory | 03.1 Inward/GRN, 03.2 4-Point QC, 03.3 Shade/Shrinkage, 03.4 Bins, 03.5 Issue | `goods_receipt_notes`, `fabric_rolls`, `fabric_inspection_logs`, `warehouse_bins`, `store_issues` |
| **MOD-04** | CAD & Cutting Floor | 04.1 CAD Markers, 04.2 Lay Sheet, 04.3 Bundle Engine, 04.4 Ply Stickers, 04.5 Parts Fusing | `cad_markers`, `cutting_lays`, `bundle_cards`, `cut_piece_tickets`, `part_transfer_logs` |
| **MOD-05** | Sewing Floor Execution | 05.1 OB Layout, 05.2 Line Input, 05.3 Hourly Counter, 05.4 Efficiency, 05.5 Andon Board | `operation_breakdowns`, `sewing_line_inputs`, `sewing_production_logs`, `line_hourly_targets` |
| **MOD-06** | Quality Assurance (QA) | 06.1 In-Line, 06.2 End-Line Touchpad, 06.3 Defect/Alter, 06.4 AQL Audit | `quality_inspection_logs`, `defect_records`, `alter_repair_logs`, `aql_inspections` |
| **MOD-07** | Finishing & Packing | 07.1 Receiving/Wash, 07.2 Poly/Iron, 07.3 Metal Detect, 07.4 Cartonization, 07.5 Scale IoT | `finishing_pieces`, `metal_detection_logs`, `carton_boxes`, `carton_pack_items`, `carton_weights` |
| **MOD-08** | Commercial & Shipping | 08.1 Invoicing/PL, 08.2 Container Stuffing, 08.3 L/C Realization, 08.4 Shipping Docs | `commercial_invoices`, `packing_lists`, `container_dispatch_logs`, `letter_of_credits` |
| **MOD-09** | Accounts & Costing | 09.1 Post-Costing, 09.2 Piece-Rate Payroll, 09.3 COPQ Quality Cost | `order_post_costings`, `operator_incentive_ledgers`, `copq_audit_ledgers` |

---

## ৪. সিস্টেম আর্কিটেকচারাল রুলস ও বাস্তবায়নের বাধ্যবাধকতা

1. **ইউআই রাউটিং ও মেনু সংগঠন:** ফ্রন্টএন্ডের নেভিগেশন সাইডবার ও রাউটিং কঠোরভাবে এই মডিউল এবং সাবমডিউল ক্রমিক (`/merchandising/orders`, `/cutting/bundles`, `/sewing/andon`) মেনে চলবে।
2. **গ্র্যানুলার পারমিশন চেকিং:** কন্ট্রোলারের প্রতিটি মেথডে সাবমডিউল অ্যাকশন পলিসি ভ্যালিডেশন বাধ্যতামূলক (যেমন: `$this->authorize('approve', $cuttingLay)` বা `$this->authorize('export', $packingList)`)।
3. **জিরো কোড হার্ডকোডিং:** কোনো সাবমডিউলের অপশন কোডে ফিক্সড থাকবে না; সব `MOD-01.1` ডায়নামিক লুকআপ ইঞ্জিন থেকে ক্যাশ হয়ে লোড হবে।
