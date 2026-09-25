# TraceFlow-RMG: ব্যাকএন্ড ডেভেলপার ম্যানুয়াল ও আর্কিটেকচারাল ইমপ্লিমেন্টেশন গাইড
**নথি কোড:** TFRMG-DEV-BE-001  
**সংস্করণ:** ১.০.০  
**কার্যকরী তারিখ:** ২৪ সেপ্টেম্বর, ২০২৬  
**শ্রেণীবিভাগ:** সফটওয়্যার ইঞ্জিনিয়ারিং বাস্তবায়ন গাইড  
**প্রযোজ্য লেটেস্ট স্ট্যাক:** Laravel 13 (PHP 8.3+ FPM), PostgreSQL 16+, Redis 7+ (Alpine), Laravel Horizon, Laravel Reverb (WebSockets)

---

## ১. ভূমিকা ও ডেভেলপারের প্রধান দায়িত্ব (Role & Responsibilities)

TraceFlow-RMG ব্যাকএন্ড ডেভেলপারের প্রধান দায়িত্ব হলো উচ্চ-কনকারেন্সি সম্পন্ন আরএমজি প্রোডাকশন ফ্লোরের জন্য সাব-২০০ মিলিসেকেন্ড রেসপন্সিভ, রিয়েল-টাইম এবং জিরো-এরর এপিআই তৈরি করা।

### প্রধান নীতিমালার সারসংক্ষেপ:
1. **কঠোর লেয়ার্ড প্যাটার্ন:** FormRequest $\rightarrow$ Controller $\rightarrow$ Domain Service $\rightarrow$ Repository/Model $\rightarrow$ JsonResource।
2. **জিরো হার্ডকোড ও সেন্ট্রালাইজড কনফিগ:** কোডের ভেতরে কোনো স্ট্যাটাস বা অপশন ফিক্সড থাকবে না; সেন্ট্রাল `AppConfig::getLookupOptions(...)` থেকে ডায়নামিক্যালি লোড হবে।
3. **অল এপিআই মেসেজ ইন ইংলিশ:** সমস্ত রেসপন্স মেসেজ প্রমিত ইংরেজিতে এবং বাহুল্যবর্জিত সংক্ষিপ্ত হবে।
4. **ট্রানজ্যাকশন সেফটি ও রেস কন্ডিশন গার্ডরেইল:** ফ্লোর বারকোড স্ক্যানিংয়ে ডেটাবেজ পেসিমিস্টিক লক ও রেডিস ডিস্ট্রিবিউটেড লক বাধ্যতামূলক।
5. **১০০% ডকার-অনলি ডেভেলপমেন্ট:** লোকাল পিসিতে কোনো পিএইচপি বা ডাটাবেজ সরাসরি রান করা যাবে না। সব কাজ ডকার কন্টেইনারে করতে হবে (দেখুন: [DOCKER-LOCAL-GUIDE.md](file:///d:/ERP/TraceFlow-RMG/ERP-AI-DEVELOPMENT-TEAM/06-DEVOPS-AND-OPERATIONS/01-DevOps-Engineer/DOCKER-LOCAL-GUIDE.md))।

---

## ২. ডিরেক্টরি সংগঠন ও আর্কিটেকচারাল লেআউট (Codebase Structure)

```
app/
├── Http/
│   ├── Controllers/Api/v1/
│   │   ├── Cutting/BundleScanController.php   # Skinny Controller (Max 15-20 lines)
│   │   ├── Sewing/LineProductionController.php
│   │   └── Packing/CartonScanController.php
│   ├── Requests/
│   │   ├── Cutting/StoreBundleScanRequest.php # FormRequest with custom English validation
│   │   └── Sewing/StoreSewingScanRequest.php
│   └── Resources/Api/v1/
│       ├── Cutting/BundleResource.php         # Clean JSON API Resource / DTO
│       └── Sewing/SewingOutputResource.php
├── Services/
│   ├── Cutting/BundleTrackingService.php      # Pure Business Logic, Math, Transactions
│   ├── Sewing/EfficiencyCalculationService.php# SMV and Real-time Line Balancing
│   └── System/AppConfigService.php            # Dynamic Centralized Lookup Provider
├── Repositories/
│   ├── Cutting/BundleRepository.php           # Optimized Indexed DB Queries
│   └── Sewing/SewingProductionRepository.php
├── Models/
│   ├── BundleCard.php                         # Eloquent Model with Scopes & Relations
│   └── SewingProductionLog.php
└── Events/
    ├── BundleScannedEvent.php                 # Broadcasted to Laravel Reverb for Live Screen
    └── LineTargetUpdatedEvent.php
```

---

## ৩. স্ট্যান্ডার্ড কোড বাস্তবায়ন উদাহরণ (Production-Grade Code Examples)

### ৩.১ স্কিনি কন্ট্রোলার (Skinny Controller Pattern)
```php
namespace App\Http\Controllers\Api\v1\Cutting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cutting\StoreBundleScanRequest;
use App\Http\Resources\Api\v1\Cutting\BundleResource;
use App\Services\Cutting\BundleTrackingService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BundleScanController extends Controller
{
    public function store(
        StoreBundleScanRequest $request, 
        BundleTrackingService $trackingService
    ): JsonResponse {
        $bundle = $trackingService->processFloorScan(
            $request->validated(), 
            $request->user()
        );

        return (new BundleResource($bundle))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
```

### ৩.২ ডোমেইন সার্ভিস লেয়ার ও ডায়নামিক ট্রানজ্যাকশন (Domain Service with Anti-Race Lock)
```php
namespace App\Services\Cutting;

use App\Models\BundleCard;
use App\Services\System\AppConfigService;
use App\Events\BundleScannedEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Exception;

class BundleTrackingService
{
    public function processFloorScan(array $data, $user): BundleCard
    {
        $barcode = $data['barcode'];

        // ১. রেডিস ডিস্ট্রিবিউটেড লক (৫ সেকেন্ডের কনকারেন্সি গার্ড)
        return Cache::lock("bundle_scan_{$barcode}", 5)->block(3, function () use ($barcode, $data, $user) {
            return DB::transaction(function () use ($barcode, $data, $user) {
                // ২. পোস্টগ্রেস পেসিমিস্টিক রো-লেভেল লক
                $bundle = BundleCard::where('barcode', $barcode)
                    ->lockForUpdate()
                    ->firstOrFail();

                // ৩. সেন্ট্রালাইজড কনফিগ থেকে ভ্যালিডেশন চেক
                $validStatuses = AppConfigService::getLookupOptions('BUNDLE_SEWING_ACCEPTABLE_STATUSES');
                if (!in_array($bundle->status, $validStatuses)) {
                    throw new Exception("Invalid bundle status for line loading.");
                }

                // ৪. স্ট্যাটাস আপডেট ও অডিট ট্রেইল
                $bundle->update([
                    'status' => 'IN_SEWING',
                    'sewing_line_id' => $data['sewing_line_id'],
                    'updated_by' => $user->id,
                ]);

                // ৫. লাইভ অ্যান্ডন ডিসপ্লেতে রিভার্ব ব্রডকাস্ট
                broadcast(new BundleScannedEvent($bundle))->toOthers();

                return $bundle;
            });
        });
    }
}
```

---

## ৪. ইউনিট ও ফিচার টেস্টিং বাধ্যবাধকতা (Automated Testing Rules)

প্রতিটি সার্ভিসের জন্য `tests/Feature/` ফোল্ডারে টেস্ট ফাইল থাকা বাধ্যতামূলক:
1. **Happy Path:** সঠিক বারকোড দিলে ২০ সেকেন্ডেরও কম সময়ে ২০০ রেসপন্স আসে কিনা।
2. **Duplicate Scan:** পরপর দুইবার দ্রুত স্ক্যান পাঠালে দ্বিতীয়টি ৪২২ এরর দেয় কিনা।
3. **Line Mismatch:** ভুল লাইনে স্ক্যান করলে সিস্টেম তাৎক্ষণিকভাবে অ্যালার্ট পাঠায় কিনা।

---
**বাধ্যবাধকতা:** কোনো কোড সরাসরি মেইন বা ডেভেলপ ব্রাঞ্চে মার্জ করার আগে ডকার কন্টেইনারে `docker compose exec backend php artisan test` এবং `docker compose exec backend ./vendor/bin/pint` ত্রুটিহীনভাবে পাস করতে হবে। লোকাল পিসি হোস্টে সরাসরি কোনো পিএইচপি টেস্ট রান করা যাবে না।
