<?php

declare(strict_types=1);

namespace App\Domain\Organization\Models;

use App\Shared\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Company Model
 *
 * Represents an individual garment manufacturing company / business tenant within the group.
 *
 * এই মডেলটি ট্রেসফ্লো প্ল্যাটফর্মের আওতাধীন একক পোশাক কারখানা কোম্পানি/টেন্যান্টের প্রতিনিধিত্ব করে।
 * প্রতিটি কারখানার নিজস্ব কোম্পানি কোড (যেমন: ITSL, AKCL) এবং লেনদেনের বেস কারেন্সি থাকে।
 */
class Company extends Model
{
    use HasFactory, HasUuidV7;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'company_name',
        'company_code',
        'company_type',
        'business_type',
        'currency',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent tenant control plane record if this company belongs to a client tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Domain\Tenant\Models\Tenant, $this>
     */
    public function tenant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Domain\Tenant\Models\Tenant::class, 'tenant_id');
    }

    /**
     * Get the users that belong to this company/tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\User, $this>
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\User::class, 'company_id');
    }
}
