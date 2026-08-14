<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Spatie\Activitylog\Models\Activity;
use App\Policies\RolePolicy;
use App\Policies\ActivityPolicy;

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
        // Register policies for vendor models so Spatie models enforce policies
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);

        // Grant Super Admin full access to all gates & permissions
        Gate::before(function ($user, $ability) {
            return ($user->hasRole('super_admin') || $user->hasRole('superadmin')) ? true : null;
        });
    }
}
