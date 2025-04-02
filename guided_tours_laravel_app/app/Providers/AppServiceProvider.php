<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // Import Gate facade
use App\Models\User; // Import User model

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
        // Define Gate for 'configurator' role
        Gate::define('configurator', function (User $user) {
            return $user->role === 'configurator';
        });

        // Define other Gates if needed (e.g., 'volunteer', 'fruitore')
        Gate::define('volunteer', function (User $user) {
            return $user->role === 'volunteer';
        });
        Gate::define('fruitore', function (User $user) {
            return $user->role === 'fruitore';
        });
    }
}
