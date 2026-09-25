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
     * কোম্পানির মূল টেবিল তৈরি করা হচ্ছে যেখানে প্রতিটি পোশাক প্রস্তুতকারক প্রতিষ্ঠানের
     * নাম, ইউনিক কোড এবং মুদ্রা সংরক্ষিত থাকবে (UUID v7 Primary Key)।
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('company_name', 150);
            $table->string('company_code', 50)->unique();
            $table->string('business_type', 50)->default('Woven');
            $table->string('currency', 10)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
