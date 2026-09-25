# TraceFlow-RMG: এসকিউএ টেস্টিং স্ট্র্যাটেজি ও কোয়ালিটি অ্যাসিউরেন্স ফ্রেমওয়ার্ক
**নথি কোড:** TFRMG-QA-STR-001  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** সফটওয়্যার কোয়ালিটি অ্যাসিউরেন্স ও টেস্ট ইঞ্জিনিয়ারিং  
**প্রযোজ্য টুলস:** Pest PHP / PHPUnit, Playwright / Cypress, k6 Load Tester  
**অনুমোদনকারী:** লিড এসকিউএ ইঞ্জিনিয়ার (Gate 5 Passed)

---

## ১. ভূমিকা ও এসকিউএ টিমের প্রধান উদ্দেশ্য (SQA Core Mission)

TraceFlow-RMG একটি মিশন-ক্রিটিক্যাল প্রোডাকশন সফটওয়্যার। সিস্টেমে একটি ভুলের কারণে পুরো কারখানার উৎপাদন ব্যাহত হতে পারে বা বায়ারের লাখ টাকার শিপমেন্ট আটকে যেতে পারে। 

এসকিউএ ইঞ্জিনিয়ারিংয়ের মূল লক্ষ্য হলো: **জিরো-ব্লকার বাগ, সাব-২০০ মিলিসেকেন্ড হাই-কনকারেন্সি পারফরম্যান্স এবং শতভাগ নিরাপদ ইউজার এক্সপেরিয়েন্স নিশ্চিত করা।**

---

## ২. টেস্ট পিরামিড ও কভারেজ মানদণ্ড (Testing Pyramid & Coverage Criteria)

```
                     / \
                    /   \
                   / E2E \     10% End-to-End Tests (Playwright / Cypress)
                  /───────\
                 /  Integ  \   20% Integration & API Tests (PHPUnit / Pest)
                /───────────\
               /    Unit     \ 70% Fast Unit Tests (Services, Math, Rules)
              /───────────────\
```

### ২.১ ন্যূনতম কাভারেজ ও গেট ৫ পাসের শর্তাবলী:
1. **ইউনিট ও সার্ভিস টেস্ট:** ব্যবসায়িক লজিক ও গণনার ক্ষেত্রে ন্যূনতম **৮৫% কোড কাভারেজ**।
2. **এজ-কেস কাভারেজ:** ভুল বারকোড স্ক্যান, ডুপ্লিকেট স্ক্যান, নেটওয়ার্ক ড্রপ এবং কনকারেন্ট রেস কন্ডিশনের সমস্ত এজ-কেস টেস্টে উত্তীর্ণ হতে হবে।
3. **লো-ল্যাটেন্সি স্ক্যান টেস্ট:** প্রতি সেকেন্ডে ৫০০টি বারকোড স্ক্যান পাঠালেও পি৯৫ (P95) লেটেন্সি ২০০ মিলিসেকেন্ডের নিচে থাকতে হবে।

---

## ৩. টেস্ট কেস স্যুট ও বাস্তব টেস্ট কোড উদাহরণ (Executable Test Specs)

### ৩.১ কনকারেন্সি ও ডুপ্লিকেট স্ক্যান টেস্ট (Pest PHP / PHPUnit)
```php
it('prevents duplicate bundle scan on concurrent floor requests', function () {
    $bundle = BundleCard::factory()->create([
        'barcode' => 'TF-CUT-004-B042',
        'status' => 'READY_FOR_SEWING'
    ]);
    $user = User::factory()->create();

    // সিমুলেট কনকারেন্ট স্ক্যান রিকোয়েস্ট ১
    $response1 = $this->actingAs($user)->postJson('/api/v1/sewing/scan', [
        'barcode' => $bundle->barcode,
        'sewing_line_id' => 1
    ]);

    // সিমুলেট তাৎক্ষণিক ডুপ্লিকেট রিকোয়েস্ট ২
    $response2 = $this->actingAs($user)->postJson('/api/v1/sewing/scan', [
        'barcode' => $bundle->barcode,
        'sewing_line_id' => 1
    ]);

    // ভেরিফিকেশন: প্রথমটি সফল হবে এবং দ্বিতীয়টি আটকে দেবে
    $response1->assertStatus(201);
    $response2->assertStatus(422)
              ->assertJson([
                  'success' => false,
                  'error_code' => 'ERR_DUPLICATE_SCAN'
              ]);
});
```

### ৩.২ ইটুই ইউআই টেস্ট (Playwright - Slide-over & In-line Matrix)
```typescript
import { test, expect } from '@playwright/test';

test('verify slide-over drawer and in-line excel matrix data entry', async ({ page }) => {
  await page.goto('/orders/create');

  // ১. মাস্টার পার্ট পূরণ
  await page.fill('input[placeholder="Enter style..."]', 'TF-POLO-2026');
  await page.fill('input[name="target_quantity"]', '24000');

  // ২. স্লাইড-ওভার ড্রয়ার ওপেন
  await page.click('button:has-text("+ Add Colors & Sizes")');
  await expect(page.locator('.slide-over-drawer')).toBeVisible();

  // ৩. কালার ও সাইজ টিক দিয়ে এপ্লাই
  await page.click('input[value="Navy Blue"]');
  await page.click('button:has-text("Apply to Matrix")');
  await expect(page.locator('.slide-over-drawer')).toBeHidden();

  // ৪. ইন-লাইন এক্সেল গ্রিডে কিবোর্ড দিয়ে কোয়ান্টিটি টাইপ
  const matrixCell = page.locator('table.in-line-matrix input').first();
  await matrixCell.fill('24000');
  await matrixCell.press('Enter');

  // ৫. লাইভ ব্যালেন্স স্ট্যাটাস যাচাই
  await expect(page.locator('.matrix-balance-badge')).toHaveText('100% Balanced (24,000 / 24,000)');
});
```

---

## ৪. লোড টেস্টিং ও পারফরম্যান্স বেঞ্চমার্ক (k6 Load Test)

ফ্লোর শিফট শুরুর সময়ে যখন একসাথে শত শত অপারেটর স্ক্যান শুরু করে, তখন সিস্টেমের আচরণ যাচাই করতে k6 স্ক্রিপ্ট নিয়মিত সিআই পাইপলাইনে চলবে:

* **টার্গেট কনকারেন্ট ভার্চুয়াল ইউজার (VUs):** ২৫০ জন অপারেটর।
* **টেস্ট সময়কাল:** ৫ মিনিট কনস্ট্যান্ট লোড।
* **এক্সেপ্টেন্স ক্রাইটেরিয়া:**
  - `http_req_failed`: < 0.01% (৯৯.৯৯% সফল হতে হবে)।
  - `http_req_duration`: p(95) < 180ms।

---
**গেট ৫ সাইন-অফ মানদণ্ড:** কোনো পি০ (ব্লকার) বা পি১ (ক্রিটিক্যাল) বাগ বিদ্যমান থাকলে রিলিজ ম্যানেজার কোনো কোড প্রোডাকশনে নিতে পারবেন না।
