<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Organization\Services\PlatformBootstrapService;
use Illuminate\Console\Command;

/**
 * Bootstrap Platform Command
 *
 * Artisan command to execute PlatformBootstrapService (ADR-11).
 *
 * ট্রেসফ্লো প্ল্যাটফর্মের রুট ওনার কোম্পানি (`ROOT-PLATFORM`) এবং
 * ৭টি অভ্যন্তরীণ ইঞ্জিনিয়ারিং অ্যাকাউন্ট ইনিশিয়ালাইজ করার আর্টিসান কমান্ড।
 */
class BootstrapPlatformCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'traceflow:bootstrap-platform';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bootstrap the root platform host company and engineering accounts idempotently (ADR-11)';

    /**
     * Execute the console command.
     */
    public function handle(PlatformBootstrapService $bootstrapService): int
    {
        $this->info('Starting TraceFlow Platform Bootstrap (ADR-11)...');

        $result = $bootstrapService->bootstrap();

        $this->info("Root Host Company: {$result['root_company']->company_name} ({$result['root_company']->company_code})");
        $this->info("Seeded/Verified Accounts: {$result['seeded_accounts']}");
        $this->table(
            ['Username', 'Email', 'Role / Squad'],
            array_map(fn ($acc) => [
                $acc['username'],
                $acc['email'],
                $acc['name'],
            ], PlatformBootstrapService::SYSTEM_ACCOUNTS)
        );

        $this->info('Platform bootstrap finished successfully.');

        return self::SUCCESS;
    }
}
