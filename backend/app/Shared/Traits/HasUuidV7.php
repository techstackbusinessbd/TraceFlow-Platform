<?php

declare(strict_types=1);

namespace App\Shared\Traits;

use Illuminate\Support\Str;

/**
 * HasUuidV7 Trait
 *
 * Automatically assigns a sequential time-ordered UUID v7 as the primary key.
 *
 * এই ট্রেইটটি মডেল ক্রিয়েট হওয়ার পূর্বে স্বয়ক্রিয়ভাবে একটি ক্রমানুসারে সাজানো (Time-ordered)
 * UUID v7 প্রাইমারি কি হিসেবে অ্যাসাইন করে, যা PostgreSQL বি-ট্রি ইনডেক্সের পারফরম্যান্স বহুগুণ বাড়ায়।
 */
trait HasUuidV7
{
    /**
     * Boot the trait and generate UUID v7 for model primary key.
     */
    protected static function bootHasUuidV7(): void
    {
        static::creating(function ($model): void {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid7();
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     */
    public function getKeyType(): string
    {
        return 'string';
    }
}
