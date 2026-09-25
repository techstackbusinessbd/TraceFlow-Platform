<?php

declare(strict_types=1);

namespace App\Domain\Organization\Models;

use App\Shared\Traits\HasUuidV7;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Custom Permission Model
 *
 * Uses UUID v7 primary keys for Spatie Permissions (Invariant-01).
 */
class Permission extends SpatiePermission
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
