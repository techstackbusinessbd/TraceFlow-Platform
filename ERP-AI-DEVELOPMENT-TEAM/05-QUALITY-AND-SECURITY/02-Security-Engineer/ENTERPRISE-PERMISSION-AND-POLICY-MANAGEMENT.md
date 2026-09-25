# TraceFlow-RMG: এন্টারপ্রাইজ পারমিশন, রোল ও পলিসি ম্যানেজমেন্ট আর্কিটেকচার
**Document Code:** TFRMG-SEC-POL-001  
**Version:** 6.0.0  
**Effective Date:** 2026-09-24  
**Classification:** Enterprise RBAC, Multi-Tenant Authorization, Laravel Policy Layer, React Guards  
**Approved By:** Lead Security Architect & Solution Architecture Board  

---

## ১. ভূমিকা ও অথরাইজেশন দর্শন (Core Philosophy)

TraceFlow-RMG একটি বহু-কোম্পানি (Multi-Company) সমন্বিত হাই-কনকারেন্সি আরএমজি ইআরপি প্ল্যাটফর্ম। এখানে হেড অফিসের ম্যানেজিং ডিরেক্টর, মার্চেন্ডাইজিং জিএম, কাটিং মাস্টার, ফ্লোর সুইং অপারেটর এবং বায়ার অডিটর একই প্ল্যাটফর্মে কাজ করেন।

### মূল নীতিসমূহ:
1. **জিরো হার্ডকোডেড রোল চেকিং (Zero Hardcoded Roles):** কোনো কন্ট্রোলার, সার্ভিস বা ফ্রন্টএন্ডে `if ($user->role == 'admin')` লেখা সম্পূর্ণ নিষিদ্ধ। সমস্ত অ্যাকশন গ্রানুলার **পারমিশন ও লারাভেল পলিসি (Permissions & Policies)** দিয়ে নিয়ন্ত্রিত হবে।
2. **মাল্টি-কোম্পানি পারমিশন স্কোপিং (Company-Scoped RBAC):** একজন ব্যবহারকারী একটি সিস্টার কনসার্নে `Merchandising Manager` হতে পারেন, কিন্তু অন্য সিস্টার কনসার্নে তার কোনো প্রবেশাধিকার থাকবে না।
3. **এন্ড-টু-এন্ড সিঙ্ক (Backend Policy to Frontend Guards):** ব্যাকএন্ডের পলিসি রুলস এবং ফ্রন্টএন্ডের বাটন/মেনু রেন্ডারিং একই পারমিশন স্ট্রিং দিয়ে পরিচালিত হবে।
4. **ডায়নামিক অ্যাডমিন কনফিগারেশন:** সুপার অ্যাডমিন সরাসরি UI স্ক্রিন থেকে রোল তৈরি ও চেকবক্সের মাধ্যমে পারমিশন ম্যাট্রিক্স পরিবর্তন করতে পারবেন (ডাটাবেজ রি-সিড ছাড়াই)।

---

## ২. পারমিশন নামকরণ স্ট্যান্ডার্ড (Naming Convention)

সমস্ত পারমিশন স্ট্রিং একটি কঠোর ডোমেন কনভেনশন মেনে চলবে:

```
{module}.{resource}.{action}
```

### বাস্তব উদাহরণসমূহ:
| পারমিশন স্ট্রিং | ব্যবহারের ক্ষেত্র ও ডোমেন |
| :--- | :--- |
| `organization.company.create` | নতুন কোম্পানি/সিস্টার কনসার্ন রেজিস্ট্রেশন |
| `organization.setting.manage` | সেন্ট্রাল সিস্টেম অপশনস ও কোড সিকোয়েন্স কনফিগারেশন |
| `merchandising.order.create` | নতুন বায়ার পিও ও স্টাইল বুকিং |
| `merchandising.order.approve` | মার্চেন্ডাইজিং জিএম কর্তৃক চূড়ান্ত পিও অনুমোদন |
| `cutting.marker.create` | কাটিং মার্কার প্ল্যান ও রেশিও প্রস্তুতকরণ |
| `cutting.bundle.scan` | ফ্লোরে বান্ডেল কিউআর স্ক্যান ও লাইন ডেলিভারি |
| `sewing.qc.log_alter` | সুইং অ্যান্ডন টেবিলে ডিফেক্ট বা অল্টার এন্ট্রি |
| `sewing.qc.approve_rework` | অল্টার হওয়া গার্মেন্টস মেরামত শেষে পুনরায় অনুমোদন |
| `commercial.lc.view_financials` | বায়ার এলসি ও এক্সপোর্ট ইনভয়েসের আর্থিক হিসাব দেখা |

---

## ৩. ডাটাবেজ স্কিমা ও মাল্টি-টেন্যান্ট স্কোপিং (Spatie Permission Setup)

সিস্টেমে অফিসিয়াল `spatie/laravel-permission` প্যাকেজ ব্যবহার করা হয়েছে। মাল্টি-কোম্পানি আইসোলেশন নিশ্চিত করতে টিম/কোম্পানি স্কোপিং সক্রিয় করা হয়েছে:

```sql
-- Spatie Permission Core Tables with UUID v7 & Company Scoping
-- roles: id, company_id (NULL = Global SuperAdmin, UUID = Company Specific), name, guard_name
-- permissions: id, name, guard_name
-- model_has_roles: role_id, model_type, model_id, company_id
-- model_has_permissions: permission_id, model_type, model_id, company_id
-- role_has_permissions: permission_id, role_id
```

### মাল্টি-কোম্পানি স্কোপিং লজিক:
```php
// config/permission.php
'teams' => true, // Enables company_id scoping natively in Spatie
'team_foreign_key' => 'company_id',
```

---

## ৪. ব্যাকএন্ড পলিসি আর্কিটেকচার (Laravel 13 Policy Layer)

প্রতিটি ডোমেন মডেলের জন্য একটি ডেডিকেটেড `Policy` ক্লাস থাকবে। কন্ট্রোলারে সরাসরি পারমিশন না দেখে পলিসি মেথড কল করতে হবে।

### পলিসি ক্লাসের গঠন (`app/Domain/Organization/Policies/CompanyPolicy.php`):
```php
<?php

namespace App\Domain\Organization\Policies;

use App\Domain\Organization\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    /**
     * Super Admin bypass - সমস্ত পলিসিতে গ্লোবাল অ্যাডমিন অটো-পাস করবে
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine if the user can view the company records.
     * ইউজার তার নিজস্ব কোম্পানি অথবা গ্লোবাল গ্রুপ পারমিশন থাকলে দেখতে পারবে।
     */
    public function view(User $user, Company $company): bool
    {
        return $user->hasPermissionTo('organization.company.view') 
            && ($user->company_id === $company->id || $user->is_group_admin);
    }

    /**
     * Determine if the user can update company settings.
     */
    public function update(User $user, Company $company): bool
    {
        return $user->hasPermissionTo('organization.company.edit') 
            && $user->company_id === $company->id;
    }
}
```

### কন্ট্রোলারে ক্লিন পলিসি প্রয়োগ (Skinny Controller):
```php
public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
{
    // পলিসি চেক - অনুমতি না থাকলে স্বয়ংক্রিয়ভাবে 403 Forbidden রিটার্ন করবে
    $this->authorize('update', $company);

    $updatedCompany = $this->companyService->updateCompany($company, $request->validated());

    return (new CompanyResource($updatedCompany))->response();
}
```

---

## ৫. ফ্রন্টএন্ড অথরাইজেশন ও ইউআই গার্ডস (React 19 & TypeScript)

লগইন করার সময় ব্যবহারকারীর অনুমোদিত পারমিশনের তালিকা টোকেন সহ ফ্রন্টএন্ডে আসবে এবং গ্লোবাল Zustand Store-এ সংরক্ষিত থাকবে।

### ৫.১ ডিক্লেয়ারেটিভ UI গার্ড কম্পোনেন্ট (`<Can />`):
যে বাটনে বা ফিচারে ইউজারের অনুমতি নেই, সেটি DOM-এ রেন্ডারই হবে না:

```tsx
import { Can } from '@/components/auth/Can';

// ব্যবহারকারীর অনুমোদন থাকলে তবেই Approve বাটন দেখা যাবে
<Can permission="merchandising.order.approve">
  <Button variant="success" onClick={handleApprove}>
    Approve Buyer Order
  </Button>
</Can>
```

### ৫.২ রাউট প্রটেকশন ও ৪MD ৩ পেজ (Protected Route Guard):
```tsx
// router.tsx
<Route 
  path="/settings/sequences" 
  element={
    <PermissionRoute permission="organization.setting.manage">
      <SequenceConfigPage />
    </PermissionRoute>
  } 
/>
```
*অনুমতি না থাকলে ব্যবহারকারীকে একটি মার্জিত **`403 Access Restricted`** স্ক্রিন দেখানো হবে, যেখানে তার প্রয়োজনীয় পারমিশন কোড ও ডিপার্টমেন্ট হেডকে রিকোয়েস্ট করার বাটন থাকবে।*

### ৫.৩ ইউজার-নির্দিষ্ট ডিরেক্ট পারমিশন ওভাররাইড (User Direct Button & Action Overrides)
অ্যাডমিন প্যানেল থেকে কোনো নির্দিষ্ট ব্যবহারকারীকে তার বেস রোলের বাইরে গিয়ে সরাসরি কোনো বাটন/অ্যাকশনের অ্যাক্সেস দেওয়া (`Direct Grant`) কিংবা কেড়ে নেওয়া (`Direct Deny`) যাবে:

```
┌────────────────────────────────────────────────────────────────────────┐
│ USER ACCESS CONTROL: Md. Rafiqul Islam (Senior Merchandiser)           │
│ Base Role: [ Merchandiser ▼ ]                                          │
├────────────────────────────────────────────────────────────────────────┤
│ 📁 Merchandising & Costing Module                                      │
│    ├── 📄 Orders Menu ............................ [✓] Allow   [ ] Deny│
│    │    ├── 🔘 [+ Create New Order] Button ....... [✓] Allow   [ ] Deny│
│    │    ├── 🔘 [Edit Matrix] Button .............. [✓] Allow   [ ] Deny│
│    │    ├── 🔘 [Approve Buyer PO] Button ......... [✓] Allow   [ ] Deny│
│    │    └── 🔘 [Export Excel/PDF] Action ......... [ ] Allow   [✓] Deny│
└────────────────────────────────────────────────────────────────────────┘
```
- **কোড লেভেলে হ্যান্ডলিং:** Spatie-এর `$user->givePermissionTo(...)` এবং `$user->revokePermissionTo(...)` দিয়ে ডাটাবেজের `model_has_permissions` টেবিলে ইউজারের সরাসরি ওভাররাইড সংরক্ষিত হবে।

---

## ৬. রোলস ও পারমিশন ম্যাট্রিক্স (Pre-built Industrial Roles)

সিস্টেমে প্রাথমিক বুটস্ট্র্যাপের জন্য নিচের স্ট্যান্ডার্ড রোলসমূহ নির্ধারিত থাকবে:

| রোল নাম (Role) | অনুমোদিত ডোমেনসমূহ | মূল দায়িত্ব ও কাজের পরিধি |
| :--- | :--- | :--- |
| **`SuperAdmin`** | All Domains (Wildcard) | পুরো গ্রুপ লেভেলের কনফিগারেশন, নতুন কোম্পানি তৈরি ও সিস্টেম রুলস। |
| **`CompanyAdmin`** | Organization, HR, Config | নিজস্ব সিস্টার কনসার্নের ইউজার তৈরি, লাইন সেটআপ ও প্ল্যান্ট ম্যাপিং। |
| **`MerchandisingManager`** | Merchandising, Costing | বায়ার বুকিং, সাইজ ম্যাট্রিক্স অনুমোদন ও টেক-প্যাক প্রাইসিং ফাইনাল। |
| **`CuttingMaster`** | Cutting Floor | মার্কার অনুমোদন, লে স্প্রেডিং অনুমোদন ও বান্ডেল কিউআর শীট প্রিন্ট। |
| **`SewingSupervisor`** | Sewing Floor | লাইন লোডিং, আওয়ারলি টার্গেট ও অ্যান্ডন অ্যালার্ট মনিটরিং। |
| **`QualityInspector`** | QC & Defect Logging | এন্ডলাইন ডিফেক্ট এন্ট্রি, অল্টারেশন মার্কিং ও রি-ওয়ার্ক পাস। |
| **`FinishingSupervisor`** | Finishing & Packing | ওয়াশ রিসিভ, আয়রনিং কোয়ালিটি ও কার্টোনাইজেশন সিল। |
| **`CommercialManager`** | Commercial & Shipping | এলসি এন্ট্রি, কাস্টমস ইনভয়েস ও এক্সপোর্ট গেট পাস তৈরি। |

---

## ৭. অডিট ট্রেইল ও পারমিশন নিরাপত্তা নীতি

1. **ইমিউটেবল পারমিশন অডিট (Spatie Activity Log):**
   - কোনো ব্যবহারকারীর রোল বা পারমিশন পরিবর্তন করা হলে অডিট টেবিলে স্বয়ংক্রিয়ভাবে রেকর্ড হবে: কে পরিবর্তন করল, কার রোল পরিবর্তন হলো, পূর্বের মান ও নতুন মান।
2. **টোকেন রেভোকেশন (Instant Session Sync):**
   - কোনো ইউজারের ক্রিটিক্যাল পারমিশন বাতিল করা হলে তার সক্রিয় API টোকেন বা সেশন অবিলম্বে রিভোক (Revoke) বা রিফ্রেশ হবে যাতে তাৎক্ষণিক অ্যাকশন ব্লক হয়।
3. **ক্যাশিং:**
   - পারমিশন চেক যেন ডাটাবেজ স্লো না করে, সেজন্য স্প্যাটির ইন্টারনাল ক্যাশ রেডিসে সংরক্ষিত থাকবে।

---

## ৮. নতুন মডিউলের স্বয়ংক্রিয় পারমিশন জেনারেশন ইঞ্জিন (Automated Permission Discovery Engine)

ডেভেলপাররা যখনই নতুন কোনো ডোমেন মডিউল (যেমন: `Cutting`, `Sewing`, `Commercial`) তৈরি করবেন, তখন ডাটাবেজে ম্যানুয়ালি SQL লিখে পারমিশন এন্ট্রি করার প্রয়োজন পড়বে না। সিস্টেমের **Auto-Discovery Engine** স্বয়ংক্রিয়ভাবে পারমিশন রেজিস্টার করবে।

### ৮.১ ডিক্লেয়ারেটিভ মডিউল ম্যানিফেস্ট (`Domain/<Context>/manifest.php`)
প্রতিটি ডোমেনের ভেতরে একটি ম্যানিফেস্ট ফাইল থাকবে:

```php
// app/Domain/Cutting/manifest.php
return [
    'module' => 'cutting',
    'label' => 'Cutting & Marker Floor',
    'resources' => [
        'marker' => [
            'label' => 'Marker Planning',
            'actions' => ['view', 'create', 'edit', 'delete', 'approve'],
        ],
        'bundle' => [
            'label' => 'Bundle Cards & QR',
            'actions' => ['view', 'create', 'scan', 'print_qr', 'rework'],
        ],
    ],
];
```

### ৮.২ স্বয়ংক্রিয় সিঙ্ক আর্টিসান কমান্ড (`php artisan traceflow:sync-permissions`)
নতুন মডিউল যুক্ত করার পর একটি একক কমান্ড চালালেই পুরো ডাটাবেজ স্বয়ংক্রিয়ভাবে সিঙ্ক হয়ে যাবে:

```bash
docker compose exec -T tf_backend php artisan traceflow:sync-permissions
```

**এই কমান্ডটির কাজের ধাপ:**
1. **ডোমেন অটো-স্ক্যান:** সমস্ত `app/Domain/*/manifest.php` স্বয়ংক্রিয়ভাবে স্ক্যান করে অ্যাকশন লিস্ট তৈরি করবে।
2. **আইডেমপোটেন্ট ইনসার্ট (Idempotent Sync):** যে পারমিশনগুলো ইতোমধ্যে ডাটাবেজে আছে সেগুলো অপরিবর্তিত থাকবে; নতুন পারমিশনগুলো (`cutting.bundle.scan`, `cutting.marker.approve`) স্বয়ংক্রিয়ভাবে `permissions` টেবিলে ইনসার্ট হবে।
3. **SuperAdmin অটো-অ্যাসাইন:** সদ্য আবিষ্কৃত সমস্ত নতুন পারমিশন তাৎক্ষণিকভাবে `SuperAdmin` রোলে যুক্ত হয়ে যাবে যাতে ডেভেলপার বা অ্যাডমিন তাৎক্ষণিক কাজ করতে পারেন।
4. **রেডিস পারমিশন ক্যাশ ক্লিয়ার:** ক্যাশ স্বয়ংক্রিয়ভাবে ফ্লাশ হয়ে ফ্রন্টএন্ড অ্যাডমিন প্যানেলে নতুন মডিউলের ট্রি-ভিউ ও বাটন চেকবক্সগুলো লাইভ হয়ে যাবে।
