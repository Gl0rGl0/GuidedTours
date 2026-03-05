<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; 
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Exceptions\Handler;
use App\Services\TicketGenerator\TicketGeneratorInterface;
use App\Services\TicketGenerator\LocalTicketGenerator;
use App\Events\VisitBooked;
use App\Listeners\GenerateTicketFile;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ExceptionHandler::class, Handler::class);
        
        // Bind the TicketGenerator Interface to the Local Implementation
        // $this->app->bind(TicketGeneratorInterface::class, LocalTicketGenerator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // parameter 'locale' set as the current one if present, or the default one from config/app.php
        // this version should be safe even if {locale} is not in the route, but in general it is better to put it in
        // non-safe version below
        URL::defaults([
            'locale' => in_array(request()->segment(1), ['it', 'en'])
                        ? request()->segment(1) 
                        : config('app.locale')
        ]);

        // non-safe version:
        // URL::defaults(['locale' => request()->segment(1) ?: config('app.locale')]);

        // Define Gate roles
        Gate::define('configurator', function (User $user) {
            return $user->hasRole('Admin');
        });

        Gate::define('volunteer', function (User $user) {
            return $user->hasRole('Guide');
        });

        Gate::define('fruitore', function (User $user) {
            return $user->hasRole('Customer');
        });

        // Register Event Listeners
        /*
        Event::listen(
            VisitBooked::class,
            GenerateTicketFile::class,
        );
        */
        // Force HTTPS when using ngrok (or any HTTPS proxy) to avoid mixed content errors
        if (request()->header('X-Forwarded-Proto') === 'https' || str_contains(request()->url(), 'ngrok-free.app')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}