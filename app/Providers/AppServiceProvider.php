<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
        if (app()->environment('production') || str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Register policies for vendor models so Spatie models enforce policies
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);

        // Grant Super Admin full access to all gates & permissions
        Gate::before(function ($user, $ability) {
            return ($user->hasRole('super_admin') || $user->hasRole('superadmin')) ? true : null;
        });
    }
}
