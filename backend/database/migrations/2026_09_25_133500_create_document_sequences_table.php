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
     * ইউনিভার্সাল ডকুমেন্ট নাম্বারিং ও অটো-কোড জেনারেশন টেবিল (ADR-09 ও Invariant-11)।
     * প্রতিটি কোম্পানির বিভিন্ন ডকুমেন্ট টাইপের (যেমন: COMPANY, FACTORY_UNIT, BUYER_ORDER, CUTTING_BATCH)
     * জন্য প্যাটার্ন ফরম্যাট, প্রিফিক্স এবং কারেন্ট সিকোয়েন্স কাউন্টার সংরক্ষণ করে।
     */
    public function up(): void
    {
        Schema::create('document_sequences', function (Blueprint $table): void {
            $table->uuid('id')->primary(); // UUID v7
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->string('document_type', 50); // e.g. COMPANY, FACTORY_UNIT, BUYER_ORDER, CUTTING_BATCH
            $table->string('prefix', 20); // e.g. TF, UNIT, PO, CUT
            $table->string('format_pattern', 100)->default('{PREFIX}-{SEQUENCE:5}'); // e.g. {COMPANY}/{YEAR}/{PREFIX}-{SEQUENCE:5}
            $table->unsignedBigInteger('current_sequence')->default(0);
            $table->unsignedInteger('padding_length')->default(5);
            $table->timestamps();

            // Unique combination per company (or null global company) and document type
            $table->unique(['company_id', 'document_type'], 'doc_sequences_company_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_sequences');
    }
};
