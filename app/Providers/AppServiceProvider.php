<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; 
use App\Models\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Exceptions\Handler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ExceptionHandler::class, Handler::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define Gate roles
        Gate::define('configurator', function (User $user) {
            return $user->hasRole('configurator');
        });

        Gate::define('volunteer', function (User $user) {
            return $user->hasRole('volunteer');
        });
        Gate::define('fruitore', function (User $user) {
            return $user->hasRole('fruitore');
        });
    }
}
