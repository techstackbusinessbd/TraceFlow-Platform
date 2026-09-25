# TraceFlow-RMG: মোবাইল ও হ্যান্ডহেল্ড টার্মিনাল আর্কিটেকচার গাইড
**Document Code:** TFRMG-DEV-MOB-003  
**Version:** ১.০.০  
**Effective Date:** 2026-09-24  
**Classification:** মোবাইল সফটওয়্যার ইঞ্জিনিয়ারিং ও হ্যান্ডহেল্ড টার্মিনাল গাইড  
**প্রযোজ্য প্ল্যাটফর্ম:** Android Enterprise OS (Zebra, Honeywell, Chainway Rugged Terminals), React Native / PWA

---

## ১. ভূমিকা ও ফ্লোর ডিভাইসের প্রেক্ষাপট (Handheld Device Context)

তৈরি পোশাক কারখানার ফেব্রিক গোডাউন ও ফিনিশিং কার্টনিং সেকশনে কর্মীরা ভারী ডেস্কটপ কম্পিউটারের বদলে রাগেড হ্যান্ডহেল্ড বারকোড টার্মিনাল (Rugged Handheld PDA / Scanner Mobile) ব্যবহার করেন। 

এই ডিভাইসগুলোতে বিল্ট-ইন 2D ইমেজার স্ক্যানার, ফিজিক্যাল কিপ্যাড এবং তুলনামূলক ছোট স্ক্রিন থাকে। এই নথির উদ্দেশ্য হলো মোবাইল ও হ্যান্ডহেল্ড অ্যাপ্লিকেশনে কীভাবে জিরো-ল্যাটেন্সি স্ক্যানিং এবং নিরবচ্ছিন্ন অফলাইন কার্যকারিতা নিশ্চিত করা হবে তা নির্ধারণ করা।

---

## ২. মোবাইল আর্কিটেকচারাল স্তম্ভসমূহ (Mobile Engineering Invariants)

1. **হার্ডওয়্যার ওএম স্ক্যানার এসডিকে ইন্টিগ্রেশন (Zebra EMDK / Honeywell SDK):**
   - সাধারণ ক্যামেরার বদলে ডিভাইসের ডেডিকেটেড 2D স্ক্যানার হেডকে ব্রডকাস্ট ইনটেন্ট (Android Broadcast Intent) লিসেনার দিয়ে রিড করতে হবে।
2. **অফলাইন-ফার্স্ট ক্যাশিং ও অটো-সিঙ্ক (Offline-First Sync Engine):**
   - ওয়াইফাই ড্রপ হলেও স্থানীয় ডাটাবেজে (WatermelonDB / SQLite) স্ক্যান জমা থাকবে।
   - নেটওয়ার্ক রিকানেক্ট হলে ব্যাকগ্রাউন্ড কিউ ক্রমানুসারে সার্ভার এপিআইতে ডাটা পুশ করবে।
3. **ইউনিফাইড ও হাই-কনট্রাস্ট ফ্লোর ইউআই:**
   - মোবাইল ইন্টারফেসের সমস্ত বাটন ন্যূনতম `48px` উচ্চতার হবে যাতে গ্লাভস পরে সহজে ট্যাপ করা যায়।
   - সমস্ত ইউআই লেবেল সংক্ষিপ্ত ইংরেজিতে হবে (যেমন: `Carton No`, `Scan Barcode`, `Carton Qty`)।

---

## ৩. ডিরেক্টরি সংগঠন (Mobile Codebase Structure)

```
mobile/
├── src/
│   ├── api/                         # Mobile Axios client with retry logic
│   ├── components/
│   │   ├── Button/FloorButton.tsx   # 48px touch target with high tactile feedback
│   │   ├── Input/ScannerInput.tsx   # Focus-free scanner input
│   │   └── Card/ScanStatusCard.tsx  # Dynamic color-coded status (Green/Red)
│   ├── db/
│   │   ├── schema.ts                # Offline SQLite / WatermelonDB schema
│   │   └── syncQueue.ts             # Background sync processor
│   ├── hardware/
│   │   ├── ZebraDataWedge.ts        # Intent listener for Zebra scanners
│   │   └── HoneywellScanner.ts      # Intent listener for Honeywell scanners
│   └── screens/
│       ├── FabricInwardScan.tsx     # Fabric roll barcode scanning
│       └── CartonPackingScan.tsx    # Mobile carton packing & audit
```

---

## ৪. ব্রডকাস্ট ইনটেন্ট লিসেনার কোড উদাহরণ (Zebra DataWedge Handler)

```typescript
// ✅ ডেডিকেটেড হার্ডওয়্যার স্ক্যানার ইনটেন্ট লিসেনার
import { NativeEventEmitter, NativeModules } from 'react-native';

export const registerScannerReceiver = (onBarcodeScanned: (code: string) => void) => {
  const eventEmitter = new NativeEventEmitter(NativeModules.DataWedgeIntents);

  const subscription = eventEmitter.addListener('barcode_scan_broadcast', (event) => {
    const scannedCode = event.data?.trim();
    if (scannedCode) {
      onBarcodeScanned(scannedCode);
    }
  });

  return () => subscription.remove();
};
```

---

## ৫. ব্যাটারি ও মেমোরি অপ্টিমাইজেশন
* ফ্লোরে টানা ৮ ঘণ্টার শিফটে যেন ব্যাটারি ড্রেন না হয়, সেজন্য কোনো অপ্রয়োজনীয় ব্যাকগ্রাউন্ড পোলিং করা যাবে না; সার্ভারের সাথে যোগাযোগের জন্য লাইটওয়েট পুশ নোটিফিকেশন ব্যবহৃত হবে।
* অফলাইন সিঙ্ক সম্পন্ন হলে মেমোরি খালি করার জন্য ওল্ড লগ ডাটা স্বয়ংক্রিয়ভাবে ক্লিনআপ হবে।

---
**প্রয়োগ ক্ষেত্র:** মোবাইল ও হ্যান্ডহেল্ড অ্যাপ্লিকেশনে এই আর্কিটেকচারাল স্ট্যান্ডার্ড শতভাগ প্রযোজ্য থাকবে।
