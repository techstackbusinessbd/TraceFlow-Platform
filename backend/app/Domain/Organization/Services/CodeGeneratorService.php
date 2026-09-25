<?php

declare(strict_types=1);

namespace App\Domain\Organization\Services;

use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\DocumentSequence;
use Illuminate\Support\Facades\DB;

/**
 * Universal Code Generator Service (ADR-09 & Invariant-11)
 *
 * Generates atomic, unique document tracking codes with pessimistic locking (lockForUpdate).
 * Supports token substitution like {COMPANY}, {YEAR}, {MONTH}, {PREFIX}, and {SEQUENCE:N}.
 *
 * সেন্ট্রালাইজড কনফিগারেশন ড্রাইভেন অটো-নাম্বারিং ইঞ্জিন।
 * ডাটাবেজ পেসিমিস্টিক লক সহকারে কাজ করে বিধায় একাধিক ফ্লোর টার্মিনাল একই মিলিসেকেন্ডে
 * হিট করলেও কখনোই ডুপ্লিকেট ট্র্যাকিং কোড তৈরি হবে না।
 */
class CodeGeneratorService
{
    /**
     * Generate the next atomic code for a given document type and company.
     *
     * @param  string  $documentType  e.g. 'COMPANY', 'UNIT', 'ORDER', 'CUTTING'
     * @param  Company|string|null  $company  Company model, UUID, or null for global
     * @param  string|null  $fallbackPrefix  Default prefix if sequence is not yet registered
     */
    public function generate(string $documentType, Company|string|null $company = null, ?string $fallbackPrefix = null): string
    {
        $companyId = $company instanceof Company ? $company->id : $company;

        return DB::transaction(function () use ($documentType, $companyId, $fallbackPrefix, $company): string {
            // Find existing sequence with pessimistic lock
            $query = DocumentSequence::where('document_type', $documentType);

            if ($companyId !== null) {
                $query->where('company_id', $companyId);
            } else {
                $query->whereNull('company_id');
            }

            /** @var DocumentSequence|null $sequence */
            $sequence = $query->lockForUpdate()->first();

            if (! $sequence) {
                $prefix = $fallbackPrefix ?? strtoupper(substr($documentType, 0, 3));
                $sequence = DocumentSequence::create([
                    'company_id' => $companyId,
                    'document_type' => $documentType,
                    'prefix' => $prefix,
                    'format_pattern' => '{PREFIX}-{SEQUENCE:5}',
                    'current_sequence' => 0,
                    'padding_length' => 5,
                ]);

                // Re-query with lock
                $sequence = DocumentSequence::where('id', $sequence->id)->lockForUpdate()->firstOrFail();
            }

            // Increment atomic counter
            $nextSequence = $sequence->current_sequence + 1;
            $sequence->current_sequence = $nextSequence;
            $sequence->save();

            // Resolve Company Code token
            $companyCode = 'TF';
            if ($company instanceof Company) {
                $companyCode = $company->company_code;
            } elseif ($companyId !== null) {
                $comp = Company::find($companyId);
                if ($comp) {
                    $companyCode = $comp->company_code;
                }
            }

            // Substitute format pattern tokens
            $pattern = $sequence->format_pattern;
            $paddedSequence = str_pad((string) $nextSequence, $sequence->padding_length, '0', STR_PAD_LEFT);

            // Replace dynamic tokens
            $code = str_replace(
                [
                    '{COMPANY}',
                    '{PREFIX}',
                    '{YEAR}',
                    '{MONTH}',
                    '{DAY}',
                ],
                [
                    $companyCode,
                    $sequence->prefix,
                    date('Y'),
                    date('m'),
                    date('d'),
                ],
                $pattern
            );

            // Replace {SEQUENCE} or {SEQUENCE:N}
            $code = preg_replace_callback('/\{SEQUENCE(?::(\d+))?\}/', function ($matches) use ($nextSequence, $sequence) {
                $padLen = isset($matches[1]) ? (int) $matches[1] : $sequence->padding_length;

                return str_pad((string) $nextSequence, $padLen, '0', STR_PAD_LEFT);
            }, $code) ?? $paddedSequence;

            return $code;
        });
    }
}
