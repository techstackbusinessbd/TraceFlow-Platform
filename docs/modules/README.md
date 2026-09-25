# TraceFlow-RMG: মডিউল স্পেসিফিকেশন ও এসওপি ডকুমেন্টেশন আর্কিটেকচার
**ডকুমেন্ট ভার্সন:** 1.0.0  
**সর্বশেষ আপডেট:** 2026-09-24  
**স্ট্যাটাস:** APPROVED / ACTIVE  

---

## ১. ভূমিকা (Overview)
প্রজেক্টের যাবতীয় বিজনেস ফাংশনাল স্পেক্স (SRS) এবং ফ্যাক্টরি মেঝের কাজের নিয়মাবলী (SOP) কেন্দ্রীয় টিম ফোল্ডারের বাইরে সরাসরি রুট ডিরেক্টরি `docs/modules/` এর অধীনে মডিউল অনুসারে সাজানো থাকবে।

---

## ২. মডিউল ডিরেক্টরি স্ট্রাকচার (Module Directory Layout)

```
docs/modules/
├── 00-core-and-iam/
│   ├── SOP.md
│   └── SRS.md
├── 01-merchandising-and-costing/
│   ├── SOP.md
│   └── SRS.md
├── 02-inventory-and-warehouse/
│   ├── SOP.md
│   └── SRS.md
├── 03-cad-and-cutting-floor/
│   ├── SOP.md
│   └── SRS.md
├── 04-sewing-floor-and-inline-qc/
│   ├── SOP.md
│   └── SRS.md
├── 05-finishing-and-cartonization/
│   ├── SOP.md
│   └── SRS.md
└── 06-commercial-and-shipping/
    ├── SOP.md
    └── SRS.md
```

---

## ৩. ফাইলের ভূমিকা ও দায়িত্ব (Role of SOP & SRS)

1. **`SOP.md` (Standard Operating Procedure):**
   - ফ্যাক্টরি মেঝের ফিজিক্যাল অপারেটরদের কাজের ধাপ (মানুষ, মেশিন, উপাদান ফ্লো)।
   - কোনো ব্যতিক্রম বা ডিফেক্ট হলে ফ্লোরে কী পদক্ষেপ নেওয়া হবে।

2. **`SRS.md` (Software Requirements Specification):**
   - সফটওয়্যারের স্ক্রিন, ফিল্ডস, ভ্যালিডেশন রুলস, এবং এপিআই রিকোয়ারমেন্ট।
   - ১০০% ডায়নামিক লুকআপ ও ম্যাট্রিক্স এন্ট্রি স্পেক্স।
