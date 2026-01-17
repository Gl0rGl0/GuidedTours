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
        $this->app->bind(TicketGeneratorInterface::class, LocalTicketGenerator::class);
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

        // Register Event Listeners
        Event::listen(
            VisitBooked::class,
            GenerateTicketFile::class,
        );
    }
}
