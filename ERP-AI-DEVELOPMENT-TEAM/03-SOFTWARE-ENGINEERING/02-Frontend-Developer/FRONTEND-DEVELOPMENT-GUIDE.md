# TraceFlow-RMG: ফ্রন্টএন্ড ডেভেলপার ম্যানুয়াল ও কম্পোনেন্ট ইঞ্জিনিয়ারিং গাইড
**Document Code:** TFRMG-DEV-FE-002  
**Version:** ১.০.০  
**Effective Date:** 2026-09-24  
**Classification:** সফটওয়্যার ইঞ্জিনিয়ারিং বাস্তবায়ন গাইড  
**প্রযোজ্য স্ট্যাক:** React 18+, TypeScript, Vite, CSS Modules, Zustand, Tailwind/Custom Tokens

---

## ১. ভূমিকা ও ফ্রন্টএন্ড ডেভেলপারের বাধ্যবাধকতা (Core Engineering Invariants)

TraceFlow-RMG ফ্রন্টএন্ড ডেভেলপারদের কোডিং করার সময় নিচের ৪টি নিয়ম অলঙ্ঘনীয়ভাবে মেনে চলতে হবে:

1. **🛑 জিরো ইনলাইন সিএসএস নীতি:** কোনো ফাইলে `style={{ ... }}` বা পেজ-লেভেলে নিজস্ব সিএসএস লেখা সম্পূর্ণ নিষিদ্ধ।
2. **১০০% ইউনিফাইড কম্পোনেন্ট লাইব্রেরি:** সমস্ত Button, Input, Dropdown, Datepicker, FormGroup, Table, DataTable, Card, Widget শুধুমাত্র `@/components/ui/` সেন্ট্রাল ফোল্ডার থেকে ব্যবহার করতে হবে।
3. **অল ইউআই ইন ইংলিশ ও কনসাইজ লেবেলিং:** কোনো দীর্ঘ অপ্রয়োজনীয় বাক্য বা বাংলা ইউআই টেক্সট রাখা যাবে না। সংক্ষিপ্ত ও পেশাদার ইংরেজি লেবেল ব্যবহার করতে হবে।
4. **স্লাইড-ওভার ড্রয়ার ও ইন-লাইন এক্সেল গ্রিড:** জটিল ডাটা এন্ট্রির ক্ষেত্রে ছোট মোডালের পরিবর্তে ডানপাশ থেকে আসা Slide-over Drawer এবং কিবোর্ড-ফ্রেন্ডলি In-line Matrix Grid ব্যবহার করতে হবে।

---

## ২. ডিরেক্টরি সংগঠন ও আর্কিটেকচারাল লেআউট (Codebase Structure)

```
src/
├── api/                             # Axios client with Sanctum interceptors
├── components/
│   ├── ui/                          # 100% Unified Central Component Library
│   │   ├── Button/Button.tsx
│   │   ├── Input/Input.tsx
│   │   ├── Select/Select.tsx
│   │   ├── DatePicker/DatePicker.tsx
│   │   ├── FormGroup/FormGroup.tsx
│   │   ├── Card/Card.tsx
│   │   ├── Table/DataTable.tsx
│   │   ├── Drawer/SlideOverDrawer.tsx
│   │   └── MatrixGrid/InLineMatrixGrid.tsx
│   └── feedback/                    # Unified Toast & Sound Alert Player
├── features/                        # Domain-Driven Feature Modules
│   ├── cutting/
│   │   ├── components/BundleScanCard.tsx
│   │   ├── hooks/useCuttingScanner.ts
│   │   └── services/cuttingApi.ts
│   ├── sewing/
│   │   ├── components/LiveAndonBoard.tsx
│   │   └── hooks/useSewingSocket.ts
│   └── orders/
│       └── components/OrderMatrixBreakdown.tsx
├── hooks/
│   ├── useBarcodeScanner.ts         # High-speed global hardware scanner hook
│   └── useSystemConfig.ts           # Dynamic lookups provider
└── styles/
    ├── variables.css                # Centralized Design Tokens (Geometry, Colors)
    └── global.css
```

---

## ৩. স্ট্যান্ডার্ড কোড বাস্তবায়ন উদাহরণ (Production-Grade Code Examples)

### ৩.১ হাই-স্পিড বারকোড স্ক্যানার হুক (`useBarcodeScanner.ts`)
```typescript
import { useEffect, useRef } from 'react';

interface UseBarcodeScannerProps {
  onScan: (barcode: string) => void;
  minIntervalMs?: number; // Scanner keystroke interval (usually < 30ms)
}

export const useBarcodeScanner = ({ onScan, minIntervalMs = 35 }: UseBarcodeScannerProps) => {
  const bufferRef = useRef<string>('');
  const lastKeyTimeRef = useRef<number>(Date.now());

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      const now = Date.now();
      const interval = now - lastKeyTimeRef.current;
      lastKeyTimeRef.current = now;

      // Enter key signals end of barcode scan
      if (e.key === 'Enter') {
        if (bufferRef.current.trim().length > 3) {
          onScan(bufferRef.current.trim());
        }
        bufferRef.current = '';
        return;
      }

      // If user typing slowly, reset buffer (ignore regular manual keyboard typing)
      if (interval > minIntervalMs && bufferRef.current.length > 0) {
        bufferRef.current = '';
      }

      if (e.key.length === 1) {
        bufferRef.current += e.key;
      }
    };

    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [onScan, minIntervalMs]);
};
```

### ৩.২ স্লাইড-ওভার ড্রয়ার ও ইন-লাইন ম্যাট্রিক্স বাস্তবায়ন (`OrderMatrixBreakdown.tsx`)
```tsx
import React, { useState } from 'react';
import { Card, CardHeader, CardBody, Button, SlideOverDrawer, InLineMatrixGrid } from '@/components/ui';
import { useSystemConfig } from '@/hooks/useSystemConfig';

export const OrderMatrixBreakdown = ({ targetQty, onSaveMatrix }) => {
  const [isDrawerOpen, setDrawerOpen] = useState(false);
  const [selectedColors, setSelectedColors] = useState<string[]>([]);
  const { getOptions } = useSystemConfig();

  const colorOptions = getOptions('BUYER_COLORS');
  const sizeOptions = getOptions('GARMENT_SIZES');

  return (
    <Card className="mt-6">
      <CardHeader 
        title="Color & Size Matrix Breakdown" 
        action={
          <Button variant="secondary" onClick={() => setDrawerOpen(true)}>
            + Add Colors & Sizes
          </Button>
        }
      />
      <CardBody>
        {/* In-line Excel-like spreadsheet grid */}
        <InLineMatrixGrid 
          colors={selectedColors}
          sizes={sizeOptions}
          targetQuantity={targetQty}
          onMatrixChange={onSaveMatrix}
        />
      </CardBody>

      {/* Non-obstructive Right Slide-Over Drawer */}
      <SlideOverDrawer 
        title="Select Colors & Sizes"
        isOpen={isDrawerOpen} 
        onClose={() => setDrawerOpen(false)}
        onApply={(colors) => {
          setSelectedColors(colors);
          setDrawerOpen(false);
        }}
        options={colorOptions}
      />
    </Card>
  );
};
```

---

## ৪. কোয়ালিটি গার্ডরেইল ও বিল্ড ভ্যালিডেশন
* কোডে কোনো `any` টাইপস্ক্রিপ্ট টাইপ রাখা নিষিদ্ধ।
* সমস্ত ইভেন্ট লিসেনার ও ওয়েবসকেট চ্যানেল কম্পোনেন্ট আনমাউন্টে ডিসকানেক্ট করতে হবে।
* বিল্ডের সময় `npm run lint` এবং `npm run type-check` জিরো ওয়ার্নিং নিয়ে পাস হতে হবে।

---
**বাধ্যবাধকতা:** কোনো ডেভেলপার ইনলাইন সিএসএস বা নিজস্ব কাস্টম কম্পোনেন্ট ব্যবহার করলে পিআর স্বয়ংক্রিয়ভাবে ব্লক হবে।
