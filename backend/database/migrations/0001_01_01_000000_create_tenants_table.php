<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * সেন্ট্রাল কন্ট্রোল প্লেইনে ক্লায়েন্ট টেন্যান্টের মূল টেবিল তৈরি করা হচ্ছে (ADR-13 ও MOD-01-APPLIANCE)।
     * প্রতিটি ক্লায়েন্ট হোল্ডিং গ্রুপের জন্য নিজস্ব সাবডোমেন, কাস্টম ডোমেন, ডেপ্লয়মেন্ট টাইপ
     * এবং ডেডিকেটেড ডাটাবেজের কানেকশন ক্রেডেনশিয়ালস সংরক্ষিত থাকবে।
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table): void {
            $table->uuid('id')->primary(); // UUID v7
            $table->string('client_name', 150);
            $table->string('client_slug', 50)->unique();
            $table->string('subdomain', 100)->unique();
            $table->string('custom_domain', 150)->nullable()->unique();
            $table->string('deployment_type', 50)->default('CLOUD_SAAS'); // CLOUD_SAAS, ON_PREMISES_APPLIANCE
            
            // Database-per-Client Isolation (ADR-13)
            $table->string('db_host', 100)->default('postgres');
            $table->integer('db_port')->default(5432);
            $table->string('db_name', 100)->unique();
            $table->string('db_username', 100);
            $table->text('db_password'); // Encrypted
            
            // Appliance & On-Premises Fields (ADR-14)
            $table->string('server_ip', 50)->nullable();
            $table->string('hardware_fingerprint', 255)->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
