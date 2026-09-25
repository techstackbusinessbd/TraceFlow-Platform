<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * [ADR-11 DECISION-02] Superadmin Automatic Permission Bypass
         *
         * Allows Superadmins and Platform Admins to automatically pass all authorization
         * checks without requiring individual permission rows.
         */
        Gate::before(function ($user, $ability): ?bool {
            if ($user->is_platform_admin || $user->hasRole('superadmin')) {
                return true;
            }

            return null;
        });
    }
}
