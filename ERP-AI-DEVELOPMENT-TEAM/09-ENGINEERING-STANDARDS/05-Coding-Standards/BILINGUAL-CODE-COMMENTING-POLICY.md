# TraceFlow-RMG: দ্বিভাষিক কোড কমেন্টিং ও ডকুমেন্টেশন স্ট্যান্ডার্ড (Bilingual Code Commenting Policy)
**Document Code:** TFRMG-ENG-COM-001  
**Version:** 5.0.0  
**Effective Date:** 2026-09-24  
**Classification:** Engineering Standards, Code Readability & Developer Maintainability  
**Approved By:** Principal Software Architect & Lead Engineering Council  

---

## ১. ভূমিকা ও উদ্দেশ্য (Core Purpose)

তৈরি পোশাক শিল্প (RMG ERP) অত্যন্ত জটিল ব্যবসায়িক নিয়মাবলী (যেমন: SMV ক্যালকুলেশন, ফেব্রিক শ্রিঙ্কেজ রিল্যাক্সেশন, মার্কার এফিশিয়েন্সি, পেসিমিস্টিক ফ্লোর লকিং ও কার্টোনাইজেশন রেশিও) ধারণ করে।

ভবিষ্যতে দেশি বা বিদেশি যেকোনো নতুন বা জুনিয়র ডেভেলপার যেন কোডবেসে প্রবেশ করেই:
1. কোডটি **ব্যবসায়িক দিক থেকে কেন** লেখা হয়েছে তা তৎক্ষণাৎ নিজের ভাষায় (বাংলা) বুঝতে পারেন।
2. গ্লোবাল স্ট্যান্ডার্ড টেকনিক্যাল ডকুমেন্টেশন ও IDE ইন্টেলিসেন্স (ইংরেজি) বজায় থাকে।
3. কোড পড়া ও রক্ষণাবেক্ষণ (Maintainability) জলের মতো সহজ হয়।

সেই লক্ষ্যেই TraceFlow-RMG কোডবেসে **দ্বিভাষিক কোড কমেন্টিং (Bilingual: Bengali + English Commentary)** বাধ্যতামূলক করা হলো।

---

## ২. দ্বিভাষিক কমেন্টিং নীতি (The Bilingual Invariant)

প্রতিটি ডোমেন সার্ভিস, মেথড, কমপ্লেক্স ক্যালকুলেশন, এবং ফ্রন্টএন্ড হুক/স্টেট হ্যান্ডলারে নিচে উল্লেখিত সুনির্দিষ্ট ফরম্যাট মান্য করতে হবে:

```
/**
 * [English Summary] Concise description of what the function/class does technically.
 * [বাংলা বিবরণ] ব্যবসায়িক প্রেক্ষাপট ও কারখানার বাস্তব কাজের আলোকে এর প্রয়োজনীয়তা।
 *
 * @param ...
 * @return ...
 */
```

### কেন এই দ্বৈত কাঠামো?
- **ইংরেজি অংশ (English):** পিএইচপি ডকব্লোক (PHPDoc) ও টাইপস্ক্রিপ্ট TSDoc স্ট্যান্ডার্ড বজায় রাখে, যা ভিএস কোড বা পিএইচপি স্টর্মে মাউস হোভার করলে অটো-কমপ্লিশন ও টাইপ হিন্ট প্রদর্শন করে।
- **বাংলা অংশ (Bangla):** কারখানার মেঝের বাস্তব পরিস্থিতি (Floor Practice) স্পষ্ট করে তোলে, ফলে কোনো নবাগত ডেভেলপার অসাবধানতাবশত ক্রিটিক্যাল বিজনেস লজিক মুছে ফেলা বা নষ্ট করার ভুল করবেন না।

---

## ৩. ব্যাকএন্ড বাস্তব উদাহরণ (Laravel 13 & PHP 8.3+)

### ৩.১ ডোমেন সার্ভিস ও মেথড কমেন্টিং (Domain Service Example)

```php
<?php

namespace App\Domain\Cutting\Services;

use App\Domain\Cutting\Models\CutPlan;
use InvalidArgumentException;

class MarkerCalculationService
{
    /**
     * Calculate fabric consumption with mandatory shrinkage relaxation allowance.
     * ফেব্রিক কাটার পূর্বে বায়ারের স্পেসিফিকেশন ও ল্যাব টেস্ট রিপোর্ট অনুযায়ী লাইক্রা/স্প্যানডেক্স ফেব্রিকের 
     * জন্য নির্ধারিত রিল্যাক্সেশন ও শ্রিঙ্কেজ এলাউন্স (Shrinkage Allowance) স্বয়ংক্রিয়ভাবে যোগ করা হয়।
     *
     * @param float $netLengthMeters মার্কারের প্রকৃত দৈর্ঘ্য (মিটার)
     * @param float $shrinkagePercentage ল্যাব টেস্টের শ্রিঙ্কেজ শতকরা হার (যেমন: ২.৫%)
     * @return float এলাউন্স সহ চূড়ান্ত প্রয়োজনীয় ফেব্রিক দৈর্ঘ্য
     * @throws InvalidArgumentException শ্রিঙ্কেজের মান ০ এর নিচে হলে এক্সেপশন থ্রো করবে
     */
    public function calculateGrossConsumption(float $netLengthMeters, float $shrinkagePercentage): float
    {
        // নেতিবাচক শ্রিঙ্কেজ ইনপুট প্রতিরোধ (Early Return Guard)
        if ($shrinkagePercentage < 0) {
            throw new InvalidArgumentException("Shrinkage percentage cannot be negative.");
        }

        // বায়ারের টেস্ট রিপোর্ট অনুযায়ী অতিরিক্ত ফেব্রিক মার্জিন হিসাব
        $allowanceMultiplier = 1 + ($shrinkagePercentage / 100);

        return round($netLengthMeters * $allowanceMultiplier, 4);
    }
}
```

### ৩.২ কন্ট্রোলার ও এপিআই এন্ডপয়েন্ট কমেন্টিং (Controller Example)

```php
    /**
     * Store a newly created company entity with auto-generated code.
     * নতুন কোম্পানি রেজিস্ট্রেশন: অ্যাডমিন প্যানেলে ইউজার কোড ইনপুট করতে পারবে না; 
     * সিস্টেম সেন্ট্রাল সিকোয়েন্স ইঞ্জিন থেকে স্বয়ংক্রিয়ভাবে ইউনিক কোম্পানি কোড (যেমন: CMP-001) তৈরি করে নিবে।
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        // ডোমেন সার্ভিসে অর্কেস্ট্রেশন ও ক্যাশ পার্জিং নিশ্চিতকরণ
        $company = $this->companyService->createCompany($request->validated());

        return (new CompanyResource($company))
            ->response()
            ->setStatusCode(201);
    }
```

---

## ৪. ফ্রন্টএন্ড বাস্তব উদাহরণ (React 19 & TypeScript)

### ৪.১ কাস্টম হুক ও স্টেট কমেন্টিং (Custom Hook Example)

```typescript
/**
 * Hook to manage multi-dimensional color and size breakdown matrix.
 * বায়ার অর্ডারের কালার ও সাইজ ম্যাট্রিক্স গ্রিডের রিয়েল-টাইম হিসাব:
 * ব্যবহারকারী যখনই কোনো সাইজে পিস এন্ট্রি করবে, রো এবং কলামের যোগফল স্বয়ংক্রিয়ভাবে 
 * হিসাব হবে এবং মূল অর্ডারের টার্গেট কোয়ান্টিটির সাথে ব্যালেন্স যাচাই করবে।
 *
 * @param targetTotalQuantity অর্ডারের মূল টার্গেট পিস
 */
export function useOrderMatrix(targetTotalQuantity: number) {
  // ম্যাট্রিক্স ডাটা স্টেট
  const [matrixData, setMatrixData] = useState<MatrixRow[]>([]);

  /**
   * Live reconciliation check
   * মোট বরাদ্দকৃত পিস ও অবশিষ্ট পিসের তাৎক্ষণিক পার্থক্য নির্ণয়
   */
  const allocatedQuantity = useMemo(() => {
    return matrixData.reduce((total, row) => total + row.totalPcs, 0);
  }, [matrixData]);

  const isBalanced = allocatedQuantity === targetTotalQuantity;

  return { matrixData, allocatedQuantity, isBalanced };
}
```

---

## ৫. নিষিদ্ধ ও অনুমোদিত কমেন্ট প্যাটার্ন (Do's and Don'ts)

| নিষিদ্ধ কমেন্ট (❌ FORBIDDEN) | অনুমোদিত দ্বিভাষিক কমেন্ট (✅ MANDATORY) |
| :--- | :--- |
| `// Function to calculate` (রোবোটিক কমেন্ট) | `/** Calculates hourly line target. প্রতি সুইং লাইনের অপারেটর দক্ষতা ও SMV-এর ভিত্তিতে আওয়ারলি টার্গেট নির্ধারণ। */` |
| `// Return response` (অর্থহীন কমেন্ট) | `// ফ্রন্টএন্ডে ২৫০ মিলিসেকেন্ডের নিচে রেসপন্স নিশ্চিত করতে ট্যাগড ক্যাশ রিটার্ন` |
| `// Step 1: get data` (জেনেরিক কমেন্ট) | `// ফ্লোরে ডুপ্লিকেট স্ক্যান রোধে পেসিমিস্টিক ডাটাবেজ রো লক (lockForUpdate) সক্রিয়করণ` |
| `// Temporary fix` বা `// TODO: later` | `// বায়ার এইচঅ্যান্ডএম স্পেশাল শর্ত: এক্স-ফ্যাক্টরি শিপমেন্টের ৭ দিন পূর্বে কিউসি লক কার্যকর হবে` |

---

## ৬. টিম ওনারশিপ ও কোড রিভিউ চেক

১. **Pull Request (PR) গেট:** পিআর রিভিউয়ের সময় লিড ইঞ্জিনিয়ার যাচাই করবেন প্রতিটি পাবলিক সার্ভিস মেথড এবং জটিল ব্লকে এই দ্বিভাষিক কমেন্ট সঠিক রয়েছে কিনা।
২. **নতুন ডেভেলপারের অনবোর্ডিং:** এই নিয়মের ফলে প্রজেক্টে আসা যেকোনো নতুন সফটওয়্যার ইঞ্জিনিয়ার বা ইন্টার্ন সিনিয়র ডেভেলপারদের বিরক্ত না করেই কোডের আরএমজি বিজনেস লজিক পড়ে শতভাগ বুঝতে পারবেন।
