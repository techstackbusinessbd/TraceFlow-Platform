# ADR-19: মডিউলার নেভিগেশন ও সাবডোমেন-অ্যাওয়ার মেন্যু অ্যাগ্রিগেশন আর্কিটেকচার (Modular Navigation & Subdomain-Aware Menu Aggregation)
**নথি কোড:** TFRMG-ARCH-ADR-019  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৫ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** Frontend Architecture, DDD Navigation & Subdomain Isolation  
**অনুমোদনকারী:** প্রিন্সিপাল সফটওয়্যার আর্কিটেক্ট ও টেকনিক্যাল গভর্ন্যান্স কাউন্সিল  

---

## ১. ব্যবসায়িক প্রেক্ষাপট ও সমস্যা (Business Context & Problem Statement)

TraceFlow-RMG একটি পূর্ণাঙ্গ এন্টারপ্রাইজ গার্মেন্টস ইআরপি (Apparel ERP)। পুরো সিস্টেমটিতে মার্চেন্ডাইজিং, কাটিং, সুইং, কিউসি, ইনভেন্টরি, অ্যাকাউন্টস সহ **১৫টিরও বেশি বিশাল মডিউল** থাকবে। প্রতিটি মডিউলে বহুস্তরীয় মেন্যু ও সাব-মেন্যু বিদ্যমান।

### সনাতন আর্কিটেকচারের ত্রুটি:
1. **মনোলিথিক গড ফাইল (Monolithic God File Problem):** সমস্ত মডিউলের মেনু যদি একটিমাত্র ফাইলে রাখা হয়, তবে ফাইলটি ৪,০০০-৫,০০০ লাইনের দানব ফাইলে পরিণত হয়। কোনো একটি মডিউলের লিংক পরিবর্তন করতে গিয়ে অন্য মডিউলের মেনু ভেঙে যাওয়ার ঝুঁকি থাকে এবং গিট ব্রাঞ্চ মার্জে প্রচুর কনফ্লিক্ট সৃষ্টি হয়।
2. **ডোমেন আইসোলেশন লঙ্ঘন:** প্ল্যাটফর্ম ওনার (Superadmin) এর মেইন ডোমেন (`traceflow.app`) এবং ক্লায়েন্ট ফ্যাক্টরির সাবডোমেন (`{client}.traceflow.app`) এর মেনু গুলিয়ে যাওয়ার ঝুঁকি তৈরি হয়।

---

## ২. মূল আর্কিটেকচারাল সিদ্ধান্ত (The Architectural Decision)

> 🛑 **MANDATORY INVARIANT [INVARIANT-26]:**  
> TraceFlow-RMG প্ল্যাটফর্মের নেভিগেশন ও মেন্যু সিস্টেম **"মডিউলার ডোমেন-ড্রাইভেন অ্যাগ্রিগেশন (Modular Domain-Driven Navigation Pattern)"** মেনে চলবে।  
> কোনো মনোলিথিক মেগা ফাইলে সব মেনু রাখা সম্পূর্ণ নিষিদ্ধ। প্রতিটি ফিচার মডিউল তার নিজস্ব ডিরেক্টরিতে (`features/<module>/nav.ts`) মেনু কনফিগার করবে এবং সেন্ট্রাল কনফিগারেশন (`shared/config/navigation.ts`) শুধুমাত্র সেগুলোকে অ্যাগ্রিগেট (Aggregate) করবে।

---

## ৩. ডিরেক্টরি কাঠামো ও ডেটা ফ্লো (Directory Structure & Flow)

```
frontend/src/
├── shared/
│   ├── types/
│   │   └── navigation.ts           <── Core TypeScript Interfaces (NavItem, NavSection)
│   └── config/
│       └── navigation.ts           <── Master Aggregator (মাত্র ২০-২৫ লাইনের ক্লিন ফাইল)
│
└── features/
    ├── platform/
    │   └── nav.ts                  <── প্ল্যাটফর্ম ওনারের মেনু (মেইন ডোমেন: traceflow.app)
    ├── organization/
    │   └── nav.ts                  <── কোম্পানি, প্ল্যান্ট ও লাইন মেনু
    ├── merchandising/
    │   └── nav.ts                  <── বায়ার, স্টাইল, টেক প্যাক ও অর্ডার মেনু
    ├── cutting/
    │   └── nav.ts                  <── কাটিং, মার্কার ও বান্ডেল কার্ড মেনু
    └── sewing/
        └── nav.ts                  <── সুইং লাইন, অ্যান্ডন ও কিউসি মেনু
```

---

## ৪. টাইপ ডেফিনিশন ও স্ট্যান্ডার্ড কন্ট্রাক্ট (`shared/types/navigation.ts`)

```typescript
import { type LucideIcon } from 'lucide-react';

export interface NavItem {
  id: string;
  name: string;             // মেন্যুর লেবেল (যেমন: 'Buyer Orders')
  path: string;             // নেটিভ হাইপারলিংক ইউআরএল (যেমন: '/app/orders')
  icon: LucideIcon;         // লাসিড আইকন
  permission?: string;      // ব্যাকএন্ড পারমিশন কি (যেমন: 'orders.view')
  badge?: string | number;  // অপশনাল কাউন্টার (যেমন: ৫টি পেন্ডিং)
  children?: NavItem[];     // সাব-মেন্যু তালিকা
}

export interface NavSection {
  id: string;
  title: string;            // সেকশন টাইটেল (যেমন: 'PRODUCTION FLOOR')
  order: number;            // সাইডবারে প্রদর্শনের ক্রমিক (১, ২, ৩...)
  permission?: string;      // সেকশন লেভেল পারমিশন
  items: NavItem[];
}
```

---

## ৫. মাস্টার অ্যাগ্রিগেশন ও ডোমেন সেপারেশন (`shared/config/navigation.ts`)

```typescript
import { platformNav } from '../../features/platform/nav';
import { organizationNav } from '../../features/organization/nav';
import { merchandisingNav } from '../../features/merchandising/nav';
import { cuttingNav } from '../../features/cutting/nav';
import { sewingNav } from '../../features/sewing/nav';
import type { NavSection } from '../types/navigation';

// ১. মেইন ডোমেন (Platform Owner Workspace: traceflow.app)
export const PLATFORM_OWNER_NAVIGATION: NavSection[] = [
  platformNav,
];

// ২. ক্লায়েন্ট সাবডোমেন (Factory Client Workspace: {client}.traceflow.app)
export const CLIENT_FACTORY_NAVIGATION: NavSection[] = [
  organizationNav,
  merchandisingNav,
  cuttingNav,
  sewingNav,
].sort((a, b) => a.order - b.order);
```

---

## ৬. সাইডবার ফিল্টারিং ও রেন্ডারিং রুলস

1. **ডোমেন-নির্দিষ্ট সিলেকশন:**
   - যদি ইউজার মেইন ডোমেনে থাকে ➔ `PLATFORM_OWNER_NAVIGATION` লোড হবে।
   - যদি ক্লায়েন্ট সাবডোমেনে থাকে ➔ `CLIENT_FACTORY_NAVIGATION` লোড হবে।
2. **গ্র্যানুলার পারমিশন গার্ড:**
   - ব্যবহারকারীর অথেন্টিকেশন প্রোফাইলে বরাদ্দকৃত পারমিশন (`user.permissions`) এর সাথে আইটেমের `permission` মিলিয়ে যাচাই করা হবে। ইউজার যে ফিচারের পারমিশন পাবে না, সাইডবারে সেই মেনুটি সম্পূর্ণ অদৃশ্য (Hidden) থাকবে।
3. **ADR-17 & INVARIANT-24 কমপ্লায়েন্স:**
   - সাইডবারের প্রতিটি মেনু আইটেম সেমান্টিক `<Link href="...">` দ্বারা তৈরি হবে, যাতে ব্যবহারকারী মাউসের রাইট-ক্লিক করে **"Open link in new tab"** অথবা মিডল-ক্লিক করতে পারেন।

---

## ৭. কারিগরি প্রভাব ও ইতিবাচক দিক (Consequences & Benefits)

* **জিরো গিট কনফ্লিক্ট:** কাটিং টিমের ডেভেলপার কাটিং ফোল্ডারে কাজ করবে, মার্চেন্ডাইজিং টিম মার্চেন্ডাইজিংয়ে—সেন্ট্রাল ফাইল নিয়ে কারও মধ্যে কনফ্লিক্ট হবে না।
* **সহজ এক্সটেনশন (Plug-and-Play Scalability):** পরবর্তীতে ওয়াশিং, ফিনিশিং বা অ্যাকাউন্টস মডিউল যুক্ত হলে শুধুমাত্র সংশ্লিষ্ট মডিউলে একটি `nav.ts` লিখে সেন্ট্রাল ফাইলে ১ লাইনে রেজিস্টার করলেই পুরো সাইডবারে অন্তর্ভুক্ত হয়ে যাবে।
