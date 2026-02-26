<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->segment(1);

        if (! in_array($locale, ['en', 'it'])) {
            $locale = 'en'; // Default fallback
        }

        App::setLocale($locale);
        
        // This makes sure we don't have to pass ['locale' => 'it'] to every route() call
        URL::defaults(['locale' => $locale]);

        // Proceed without removing the locale prefix from the request, Laravel's router handles it now
        return $next($request);
    }
}
