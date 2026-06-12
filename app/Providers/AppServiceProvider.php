<?php

namespace App\Providers;

use App\Enums\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

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
        Gate::before(function ($user, $ability) {
            return $user->hasRole(Role::Admin->value) ? true : null;
        });

        Activity::saving(function (Activity $activity) {
            if (request()) {
                $activity->properties = $activity->properties
                    ->put('ip', request()->ip())
                    ->put('user_agent', substr((string) request()->userAgent(), 0, 255));
            }
        });
    }
}
