<?php

declare(strict_types=1);

namespace App\Domain\Organization\Services;

use App\Domain\Organization\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Platform Bootstrap Service (ADR-11)
 *
 * Idempotently provisions the root platform host company and the 7 system engineering accounts.
 * Safe to execute multiple times across local development, testing, and production environments.
 *
 * এই সার্ভিসটি প্ল্যাটফর্মের রুট ওনার কোম্পানি (`ROOT-PLATFORM`, `PLATFORM_HOST`) এবং
 * ৭টি অভ্যন্তরীণ ইঞ্জিনিয়ারিং সিস্টেম অ্যাকাউন্ট ইডেমপোটেন্ট পদ্ধতিতে ইনিশিয়ালাইজ করে।
 * এটি সাধারণ ডেমো সিডার নয়; তাই ডেমো ডাটা পার্জ করার পরও সিস্টেম ওনার অ্যাকাউন্ট অক্ষত থাকবে।
 */
class PlatformBootstrapService
{
    /**
     * Root Platform Company Code
     */
    public const ROOT_COMPANY_CODE = 'ROOT-PLATFORM';

    /**
     * Root Platform Company Name
     */
    public const ROOT_COMPANY_NAME = 'TraceFlow Platform Engine';

    /**
     * Internal Engineering Accounts Definition (ADR-11 Decision 03)
     */
    public const SYSTEM_ACCOUNTS = [
        [
            'name' => 'Platform Superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@traceflow.internal',
            'is_platform_admin' => true,
        ],
        [
            'name' => 'Backend Engineering Squad',
            'username' => 'backend.team',
            'email' => 'backend.team@traceflow.internal',
            'is_platform_admin' => true,
        ],
        [
            'name' => 'Frontend UI/UX Squad',
            'username' => 'frontend.team',
            'email' => 'frontend.team@traceflow.internal',
            'is_platform_admin' => true,
        ],
        [
            'name' => 'Database & Schema Squad',
            'username' => 'database.team',
            'email' => 'database.team@traceflow.internal',
            'is_platform_admin' => true,
        ],
        [
            'name' => 'DevOps & SRE Squad',
            'username' => 'devops.team',
            'email' => 'devops.team@traceflow.internal',
            'is_platform_admin' => true,
        ],
        [
            'name' => 'Quality Assurance Squad',
            'username' => 'qa.team',
            'email' => 'qa.team@traceflow.internal',
            'is_platform_admin' => true,
        ],
        [
            'name' => 'Business Analyst Squad',
            'username' => 'ba.team',
            'email' => 'ba.team@traceflow.internal',
            'is_platform_admin' => true,
        ],
    ];

    /**
     * Bootstrap the platform root company and engineering accounts idempotently.
     *
     * @return array{root_company: Company, seeded_accounts: int}
     */
    public function bootstrap(): array
    {
        Log::info('Initiating TraceFlow Platform Bootstrap (ADR-11)...');

        // 1. Idempotently find or create the ROOT-PLATFORM company
        $rootCompany = Company::firstOrCreate(
            ['company_code' => self::ROOT_COMPANY_CODE],
            [
                'tenant_id' => null, // Root host company does not belong to any client tenant
                'company_name' => self::ROOT_COMPANY_NAME,
                'company_type' => 'PLATFORM_HOST',
                'business_type' => 'Platform',
                'currency' => 'USD',
                'is_active' => true,
            ]
        );

        $defaultPassword = config('app.platform_bootstrap_password', env('PLATFORM_BOOTSTRAP_PASSWORD', 'TraceFlow@2026!Root'));
        $seededCount = 0;

        // 2. Idempotently find or create each of the 7 system accounts
        foreach (self::SYSTEM_ACCOUNTS as $accountData) {
            $user = User::firstOrNew(['email' => $accountData['email']]);

            $isNew = ! $user->exists;

            $user->name = $accountData['name'];
            $user->username = $accountData['username'];
            $user->company_id = $rootCompany->id;
            $user->is_platform_admin = $accountData['is_platform_admin'];
            $user->is_active = true;

            // Only set password if user is newly created or password was not previously set
            if ($isNew || empty($user->password)) {
                $user->password = Hash::make($defaultPassword);
            }

            $user->save();
            $seededCount++;
        }

        Log::info("TraceFlow Platform Bootstrap successfully completed. Root Company: {$rootCompany->company_code}, Accounts: {$seededCount}");

        return [
            'root_company' => $rootCompany,
            'seeded_accounts' => $seededCount,
        ];
    }
}
