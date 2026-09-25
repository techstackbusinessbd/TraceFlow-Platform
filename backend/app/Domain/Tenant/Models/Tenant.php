<?php

declare(strict_types=1);

namespace App\Domain\Tenant\Models;

use App\Shared\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tenant Model (Central Control Plane)
 *
 * Represents an individual client enterprise / holding group on TraceFlow-Platform.
 *
 * সেন্ট্রাল কন্ট্রোল প্লেইনে প্রতিটি ক্লায়েন্ট হোল্ডিং গ্রুপের মূল রেকর্ড ধারণ করে।
 * এটি ক্লায়েন্টের নিজস্ব সাবডোমেন, কাস্টম ডোমেন, ডাটাবেজ ক্রেডেনশিয়ালস এবং সার্ভার ডেপ্লয়মেন্ট মডেল ম্যাপ করে।
 */
class Tenant extends Model
{
    use HasFactory, HasUuidV7;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_name',
        'client_slug',
        'subdomain',
        'custom_domain',
        'deployment_type',
        'db_host',
        'db_port',
        'db_name',
        'db_username',
        'db_password',
        'server_ip',
        'hardware_fingerprint',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'db_password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'db_password' => 'encrypted',
        'is_active' => 'boolean',
        'db_port' => 'integer',
    ];

    /**
     * Get the manufacturing companies operating under this tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Domain\Organization\Models\Company, $this>
     */
    public function companies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Domain\Organization\Models\Company::class, 'tenant_id');
    }
}
