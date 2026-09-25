<?php

declare(strict_types=1);

namespace App\Domain\Organization\Models;

use App\Shared\Traits\HasUuidV7;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Custom Role Model
 *
 * Uses UUID v7 primary keys for Spatie Roles (Invariant-01).
 * Supports multi-company teams with `company_id` (ADR-08).
 */
class Role extends SpatieRole
{
    use HasUuidV7;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}
