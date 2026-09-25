# TraceFlow-RMG: রিঅ্যাক্ট ফ্রন্টএন্ড আর্কিটেকচার, সেন্ট্রালাইজড কম্পোনেন্ট ও সিএসএস স্ট্যান্ডার্ড
**Document Code:** TFRMG-ENG-RCT-003  
**Version:** 5.0.0  
**Effective Date:** 2026-09-24  
**Classification:** Frontend Engineering, CSS Architecture & Unified Components  
**Approved By:** Principal Frontend Architect & UX Engineering Council

---

## ১. ভূমিকা ও জিরো ইনলাইন সিএসএস নীতি (Zero Inline CSS & Centralized Component Policy)

TraceFlow-RMG ফ্রন্টএন্ডে কোড কোয়ালিটি, পারফরম্যান্স এবং থিমিং ধারাবাহিকতা বজায় রাখতে একটি কঠোর অনুশাসন কার্যকর থাকবে:

> 🛑 **ZERO INLINE CSS POLICY:**  
> কোনো জেএসএক্স বা টিএসএক্স ফাইলে কোনো পরিস্থিতিতেই ইনলাইন সিএসএস (`style={{ ... }}`) লেখা সম্পূর্ণ নিষিদ্ধ।  
> কোনো পেজ বা মডিউল লেভেলে এড-হক সিএসএস ক্লাস বানিয়ে বাটন, ইনপুট, টেবিল বা কার্ডের নিজস্ব স্টাইল তৈরি করা যাবে না।  
> সিস্টেমের ১০০% স্টাইলিং হতে হবে **কম্পোনেন্ট-বেসড (Component-Based)** এবং **সেন্ট্রালাইজড ভ্যারিয়েবল ও মডিউল চালিত (Centralized CSS Variables Driven)**।

---

## ২. সেন্ট্রালাইজড সিএসএস আর্কিটেকচার (Centralized Styling Engine)

ফ্রন্টএন্ডের সমস্ত স্টাইল একটি কেন্দ্রীয় স্তর থেকে ধাপে ধাপে প্রবাহিত হবে:

```
src/styles/
├── variables.css      # Core Design Tokens (Colors, Radius, Elevation, Fonts)
├── reset.css          # Modern CSS Reset
└── global.css         # Universal Layout Rules & Theme Definitions
       │
       ▼
src/components/ui/
├── Button/Button.module.css          # Uses ONLY var(--element-height), var(--brand-accent)
├── Input/Input.module.css            # Uses ONLY var(--border-subtle), var(--radius-md)
├── Select/Select.module.css          # Unified Dropdown matching Input perfectly
├── DatePicker/DatePicker.module.css  # Unified Calendar popup
├── FormGroup/FormGroup.module.css    # Strict 6px spacing
├── Card/Card.module.css              # Subtle glassmorphism, 16px radius, soft shadow
└── Table/Table.module.css            # 44px header, 52px row height, soft hover
```

---

## ৩. সেন্ট্রালাইজড ডিজাইন টোকেন (`src/styles/variables.css`)

সমস্ত কম্পোনেন্ট শুধুমাত্র এই গ্লোবাল সিএসএস ভেরিয়েবল ব্যবহার করবে:

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
  --form-group-gap: 6px;           /* Distance between Label and Input */

  /* Shadows & Depth */
  --shadow-card: 0 10px 25px -5px rgba(15, 23, 42, 0.04), 0 8px 10px -6px rgba(15, 23, 42, 0.02);
  --shadow-hover: 0 14px 30px -4px rgba(15, 23, 42, 0.08);
  --shadow-glow: 0 0 0 3px rgba(14, 165, 233, 0.18);
  --transition-smooth: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);

  /* Typography */
  --font-family-base: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  --font-size-base: 14px;
  --font-size-sm: 12px;
  --font-weight-medium: 500;
  --font-weight-semibold: 600;
}
```

---

## ৪. কম্পোনেন্ট-বেসড বাস্তবায়ন ও শতভাগ ইনলাইন-মুক্ত উদাহরণ

### ৪.১ ভুল বনাম সঠিক কোডিং প্র্যাকটিস

```tsx
// ❌ সম্পূর্ণ নিষিদ্ধ (ইনলাইন সিএসএস ও কাস্টম ফন্ট/প্যাডিং):
<div style={{ marginTop: '15px', padding: '10px', background: '#fff' }}>
  <button style={{ height: '38px', borderRadius: '5px', backgroundColor: 'blue', color: 'white' }}>
    Submit
  </button>
</div>

// ✅ আদর্শ সেন্ট্রালাইজড কম্পোনেন্ট-বেসড প্র্যাকটিস (জিরো ইনলাইন সিএসএস):
import { Card, CardBody, Button, FormGroup, Input, Select, DatePicker } from '@/components/ui';

export const ProductionFilter = () => {
  return (
    <Card>
      <CardBody className="filter-grid">
        <FormGroup label="Style No">
          <Input placeholder="Enter style..." />
        </FormGroup>

        <FormGroup label="Sewing Line">
          <Select options={lineOptions} />
        </FormGroup>

        <FormGroup label="Production Date">
          <DatePicker />
        </FormGroup>

        <Button variant="primary">
          Apply Filter
        </Button>
      </CardBody>
    </Card>
  );
};
```

---

## ৫. সেন্ট্রালাইজড ইউআই কম্পোনেন্ট লাইব্রেরি রুলস

1. **Button:** শুধুমাত্র প্রপসের মাধ্যমে নিয়ন্ত্রিত হবে (`variant="primary" | "secondary" | "danger"` / `size="default" | "large"` / `isLoading`)।
2. **Input, Select, DatePicker:** সবার আউটার র্যাপার এবং ইন্টারনাল কন্ট্রোল সেন্ট্রাল সিএসএস মডিউল দিয়ে নিয়ন্ত্রিত হবে, কোনো পেজ থেকে তাদের হাইট বা বর্ডার পরিবর্তন করা যাবে না।
3. **Table & DataTable:** সমস্ত পেজিনেশন, সার্চ বক্স ও হেডার রো সেন্ট্রাল কম্পোনেন্ট লাইব্রেরি থেকে পরিচালিত হবে।
4. **Card & Widget:** সারফেস এবং রাউন্ডিং সর্বদা কমপ্যাক্ট `--radius-sm` (বা `rounded-sm`, 2-4px) এবং `--shadow-card` মেনে চলবে (Invariant-29)।

---

## ৬. অটোমেটেড সিআই ও লিন্টিং গার্ডরেইল (ESLint Enforcement)

* রিপোজিটরির ESLint কনফিগারেশনে `react/forbid-component-props` এবং `react/forbid-dom-props` সক্রিয় থাকবে, যা কোনো ফাইলে `style` প্রপস শনাক্ত করলে সাথে সাথে বিল্ড ফেইল করাবে।
* কোনো পিআরে ইনলাইন সিএসএস থাকলে তা স্বয়ংক্রিয়ভাবে প্রত্যাখ্যাত হবে।

---
**বাধ্যবাধকতা:** সমস্ত ফ্রন্টএন্ড ডেভেলপারকে শতভাগ কম্পোনেন্ট-বেসড ও সেন্ট্রালাইজড সিএসএস ফ্রেমওয়ার্ক অনুসরণ করতে হবে।
