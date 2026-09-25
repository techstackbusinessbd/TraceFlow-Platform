# TraceFlow-RMG: এআই এজেন্ট মেমোরি ও কনটেক্সট স্টোর
**নথি কোড:** TFRMG-AI-MEM-004  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** এআই মেমোরি ও প্রোজেক্ট কনটেক্সট ক্যাটাগরি  
**অনুমোদনকারী:** এআই আর্কিটেকচার সেল

---

## ১. ভূমিকা (Agent Memory Invariants)

যাতে প্রতিটি এআই এজেন্ট নতুন টাস্ক শুরু করার সময় পূর্ববর্তী অনুমোদিত সিদ্ধান্ত এবং প্রকল্পের মূল আর্কিটেকচারাল প্রেক্ষাপট তাৎক্ষণিকভাবে স্মরণ করতে পারে, সেজন্য এই সেন্ট্রালাইজড কনটেক্সট মেমোরি সংরক্ষিত থাকবে।

---

## ২. অনুমোদিত স্থাপত্যিক সিদ্ধান্তসমূহ (Immutable Decisions Context)

1. **Backend:** Laravel 13 (PHP 8.3+ FPM) with Horizon and Reverb.
2. **Database:** PostgreSQL 16+ with Mandatory **UUID v7** primary keys.
3. **Cache:** Redis 7+ Alpine with tagged cache invalidation.
4. **Frontend:** React 18+ with zero inline CSS and centralized `@/components/ui/` library.
5. **UI/UX Pattern:** Slide-over Drawer + In-line Excel-like Spreadsheet Grid for Color/Size PO breakdowns.
6. **Configuration Policy:** 100% Dynamic, Zero Hardcoded Enums, Centralized Admin Control.

---

## ৩. ডোমেইন টার্মিনোলজি রেফারেন্স মেমোরি
* **Piece / Bundle:** তৈরি পোশাকের একক বা বান্ডেল।
* **SMV:** Standard Minute Value (পোশাক তৈরির আন্তর্জাতিক মানদণ্ড সময়)।
* **Lay:** কাটিং টেবিলে কাপড়ের স্তূপ (Plies)।
* **Defect / Alteration:** ত্রুটি ও তার সংশোধন প্রক্রিয়া।
* **AQL:** Acceptance Quality Limit (বায়ারের ফাইনাল শিপমেন্ট অডিট মানদণ্ড)।

---
**ব্যবহার বিধি:** প্রতিটি এআই এজেন্টের প্রম্পট ইনিশিয়ালাইজেশনের সময় এই ফাইলটি মেমোরি কনটেক্সট হিসেবে ইনজেক্ট হবে।
