<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; 
use Illuminate\Support\Facades\Event;
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
