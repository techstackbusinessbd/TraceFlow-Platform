<?php

declare(strict_types=1);

namespace App\Domain\Organization\Models;

use App\Shared\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Document Sequence Model
 *
 * Stores sequence counters and formatting patterns for auto-code generation (ADR-09).
 *
 * ইউনিভার্সাল ডকুমেন্ট নাম্বারিং মডেল। বিভিন্ন ট্র্যাকিং কোড যেমন কোম্পানি কোড,
 * ফ্যাক্টরি ইউনিট কোড, অর্ডার পিও এবং বান্ডেল বারকোড সিকোয়েন্স পরিচালনা করে।
 */
class DocumentSequence extends Model
{
    use HasFactory, HasUuidV7;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'document_sequences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'document_type',
        'prefix',
        'format_pattern',
        'current_sequence',
        'padding_length',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'current_sequence' => 'integer',
        'padding_length' => 'integer',
    ];

    /**
     * Get the company that owns the document sequence.
     *
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
