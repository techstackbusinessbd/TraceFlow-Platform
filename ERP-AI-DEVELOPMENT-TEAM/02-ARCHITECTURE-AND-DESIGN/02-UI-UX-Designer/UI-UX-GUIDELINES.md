# TraceFlow-RMG: ইউনিফাইড এন্টারপ্রাইজ ডিজাইন সিস্টেম ও কম্পোনেন্ট স্ট্যান্ডার্ড
**Document Code:** TFRMG-ARCH-UIUX-001  
**Version:** 5.0.0  
**Effective Date:** 2026-09-24  
**Classification:** Enterprise Design System, Unified Components & Master-Detail UX  
**Approved By:** Principal Product Designer & Solution Architecture Council

---

## ১. ভূমিকা ও শতভাগ অভিন্ন ডিজাইন নীতি (100% Unified Design Consistency Policy)

TraceFlow-RMG প্ল্যাটফর্মের সমস্ত মডিউল, পেজ এবং ফিচারে একটি কঠোর নিয়ম কার্যকর থাকবে: **"একক সিস্টেম, একক ভাষা, শতভাগ অভিন্ন ডিজাইন (Single Design System Across All Pages)"**।

কারখানার কাটিং স্ক্রিন, সুইং অ্যান্ডন বোর্ড, ফিনিশিং প্যাকিং টেবিল, কমার্শিয়াল ড্যাশবোর্ড থেকে শুরু করে সুপার অ্যাডমিন কনফিগারেশন প্যানেল পর্যন্ত—সিস্টেমের প্রতিটি পেজে সমস্ত উপাদানের গঠন, উচ্চতা, বর্ডার, রঙ, প্যাডিং, ফন্ট এবং অ্যানিমেশন হুবহু একই কেন্দ্রীয় ডিজাইন টোকেন ও কম্পোনেন্ট লাইব্রেরি থেকে পরিচালিত হবে।

---

## ২. মাস্টার-ডিটেইল ও জটিল ডাটা এন্ট্রি প্যাটার্ন (Master-Detail & Order Matrix UX Pattern)

গার্মেন্টস অর্ডারে মাস্টার ইনফরমেশন (Buyer, Style, Delivery Date, Season) পূরণের পর বহুসংখ্যক Color এবং Size Matrix এন্ট্রি করার জন্য ঐতিহ্যবাহী ছোট পপআপ বা মোডাল পরিহার করা হয়েছে। 

তার পরিবর্তে একটি আধুনিক ও উচ্চগতির **"স্লাইড-ওভার ড্রয়ার পিকার (Slide-over Drawer Picker) + লাইভ ইন-লাইন ম্যাট্রিক্স গ্রিড (In-line Matrix Grid)"** স্ট্যান্ডার্ড কার্যকর করা হলো।

```
┌────────────────────────────────────────────────────────────────────────┐
│                        ORDER CREATION PAGE                             │
├────────────────────────────────────────────────────────────────────────┤
│ [Card 1: Master Order Info]                                            │
│ Buyer: [ H&M ▼ ]    Style: [ TF-POLO-2026 ▼ ]    Delivery: [ 2026-11-15]│
│ Total PO Qty: [ 24,000 Pcs ]   SMV: [ 12.5 ]     Season: [ Autumn 2026] │
├────────────────────────────────────────────────────────────────────────┤
│ [Card 2: Color & Size Matrix Breakdown]                                │
│ [ + Add Colors & Sizes ] ──► (Triggers Smooth Right Slide-Over Drawer) │
│                                                                        │
│ ┌────────────────────────────────────────────────────────────────────┐ │
│ │ Color Name  │ XS   │ S    │ M    │ L    │ XL   │ 2XL  │ Total Qty  │ │
│ ├─────────────┼──────┼──────┼──────┼──────┼──────┼──────┼────────────┤ │
│ │ Navy Blue   │ 200  │ 500  │ 1000 │ 1000 │ 500  │ 200  │ 3,400 Pcs  │ │
│ │ Heather Grey│ 200  │ 500  │ 1000 │ 1000 │ 500  │ 200  │ 3,400 Pcs  │ │
│ │ Total       │ 400  │ 1000 │ 2000 │ 2000 │ 1000 │ 400  │ 6,800 Pcs  │ │
│ └────────────────────────────────────────────────────────────────────┘ │
│ Target: 24,000 Pcs | Allocated: 6,800 Pcs | Remaining: 17,200 Pcs ⚠️   │
└────────────────────────────────────────────────────────────────────────┘
```

### ২.১ স্লাইড-ওভার ড্রয়ারের ভূমিকা (Slide-Over Drawer Picker)
1. **প্রেক্ষাপট অক্ষুণ্ণ রাখা:** স্ক্রিনের ডানপাশ থেকে আসা ড্রয়ারটি ব্যাকগ্রাউন্ড পেজকে পুরোপুরি ঢেকে দেয় না; ফলে ইউজার মাস্টার কার্ডের ডাটা দেখতে পান।
2. **মাস্টার ডাটা সিলেকশন:** ড্রয়ার থেকে বায়ারের অনুমোদিত কালার ও সাইজগুলো এক ক্লিকে সিলেক্ট করে `Apply to Matrix` বাটনে চাপ দিলে ড্রয়ার বন্ধ হয়ে যাবে।

### ২.২ ইন-লাইন এক্সেল-লাইক স্প্রেডশিট গ্রিড (In-line Matrix Grid)
1. **কিবোর্ড-ফার্স্ট নেভিগেশন:** অপারেটর মাউস ছাড়াই কিবোর্ডের `Tab` বা `Arrow Keys` দিয়ে দ্রুতগতিতে সাইজ কোয়ান্টিটি টাইপ করতে পারবেন।
2. **লাইভ অটো-সাম ও রিকনসিলিয়েশন:** প্রতিটি রো এবং কলামের শেষে স্বয়ংক্রিয় যোগফল হিসাব হবে এবং মাস্টার অর্ডারের টার্গেটের সাথে তাৎক্ষণিক রিকনসাইল হবে:
   - কম হলে: `Remaining: 2,000 Pcs` (Amber Warning).
   - মিলে গেলে: `100% Balanced (24,000 / 24,000)` (Emerald Green Badge).
   - বেশি হলে: `Target Exceeded by 500 Pcs` (Crimson Alert).

---

## ৩. প্রিমিয়াম ডিজাইন টোকেন ও কালার প্যালেট (Design Tokens)

```css
:root {
  /* Surface & Background */
  --bg-app: #f8fafc;
  --bg-surface: #ffffff;
  --bg-card: rgba(255, 255, 255, 0.92);
  --border-subtle: #e2e8f0;
  --border-focus: #0ea5e9;

  /* Brand Accents */
  --brand-primary: #0f172a;        /* Deep Slate Navy */
  --brand-accent: #0284c7;         /* Vibrant Cobalt */
  --brand-success: #10b981;        /* Emerald Mint */
  --brand-warning: #f59e0b;        /* Amber */
  --brand-danger: #ef4444;         /* Crimson Coral */

  /* Component Geometry & Spacing (Universal across all pages) */
  --element-height: 40px;          /* Standard Input/Button/Dropdown height */
  --element-height-lg: 48px;       /* Floor Touch/Scanner Terminal height */
  --radius-sm: 6px;
  --radius-md: 10px;               /* Standard for inputs, buttons, dropdowns */
  --radius-lg: 16px;               /* Standard for cards & widgets */
  --form-group-gap: 6px;

  /* Shadows & Depth */
  --shadow-card: 0 10px 25px -5px rgba(15, 23, 42, 0.04), 0 8px 10px -6px rgba(15, 23, 42, 0.02);
  --shadow-glow: 0 0 0 3px rgba(14, 165, 233, 0.18);
  --transition-smooth: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);

  /* Typography */
  --font-family-base: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  --font-size-base: 14px;
}
```

---

## ৪. অভিন্ন ইউআই উপাদান মানদণ্ড (Unified Component Specifications)

* **Button:** উচ্চতা `40px` (ফ্লোর মোডে `48px`), বর্ডার রেডিয়াস `10px`, ট্রানজিশন স্মুথ।
* **Input, Dropdown, DatePicker:** উচ্চতা বাধ্যতামূলক `40px`, ১ পিক্সেল সাবটল বর্ডার, ফোকাসে নীল গ্লো।
* **FormGroup & Label:** দূরত্ব ফিক্সড `6px`, লেবেল সংক্ষেপ ও ইংরেজি (যেমন: `Style No`, `Delivery Date`, `Target Qty`)।
* **Card & Widget:** গ্লাস সারফেস, বর্ডার রেডিয়াস `16px`, সফট শ্যাডো (`--shadow-card`), প্যাডিং `20px` বা `24px`।
* **Table & DataTable:** হেডার ফিক্সড `44px`, বডি রো `52px`, হোভারে ব্যাকগ্রাউন্ড কালার `#f1f5f9`।

---

## ৫. সেন্ট্রালাইজড অ্যাডমিন কন্ট্রোল ও জিরো হার্ডকোড (Admin Dynamic Rules)

1. **ডায়নামিক প্যারামিটার:** সমস্ত ড্রপডাউন, স্ট্যাটাস এবং ফিল্ডের আচরণ সেন্ট্রাল `system_categories` ও `system_options` টেবিল থেকে অ্যাডমিন প্যানেলের মাধ্যমে নিয়ন্ত্রণ হবে।
2. **জিরো ইনলাইন সিএসএস:** কোনো ফাইলে ইনলাইন সিএসএস (`style={{ ... }}`) লেখা সম্পূর্ণ নিষিদ্ধ। সমস্ত উপাদান অবশ্যই সেন্ট্রালাইজড কম্পোনেন্ট লাইব্রেরি (`@/components/ui`) থেকে আসতে হবে।

---

## ৬. মাস্টার কম্পোনেন্ট রেফারেন্স (Master Component Reference)
সিস্টেমের ১৬টি ক্যাটাগরির সমস্ত কম্পোনেন্টের তালিকা, প্রপস, হাই-ডেনসিটি ডাটা টেবিল, ইআরপি স্পেসিফিক উইজেট ও সিস্টেম স্টেট ম্যাট্রিক্সের বিস্তারিত গাইডলাইন দেখুন:
👉 **[ENTERPRISE-UI-COMPONENT-SYSTEM.md](file:///d:/ERP/TraceFlow-RMG/ERP-AI-DEVELOPMENT-TEAM/02-ARCHITECTURE-AND-DESIGN/02-UI-UX-Designer/ENTERPRISE-UI-COMPONENT-SYSTEM.md)**

---
**বাধ্যবাধকতা:** মাস্টার-ডিটেইল স্ক্রিন ও ফর্ম তৈরিতে এই স্পেসিফিকেশন অতিক্রম করে কোনো ছোট মোডাল বা বিশৃঙ্খল টেবিল তৈরি করা যাবে না।
