<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'setlocale' => \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            return redirect()->route('home')->with('error_message', __('messages.errors.access_denied'));
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            switch ($e->getStatusCode()) {
                case 401:
                    return redirect()->route('login')->with('error_message', __('messages.errors.unauthenticated'));
                case 403:
                    return redirect()->route('home')->with('error_message', __('messages.errors.access_denied'));
                case 404:
                    return redirect()->route('home')->with('error_message', __('messages.errors.page_not_found'));
                case 419:
                    return redirect()->route('home')->with('error_message', __('messages.errors.session_expired'));
                case 500:
                    return redirect()->route('home')->with('error_message', __('messages.errors.internal_error'));
            }
            return redirect()->route('home')->with('error_message', __('messages.errors.exception_error'));
        });

        $exceptions->render(function (\BadMethodCallException $e, Request $request) {
            return redirect()->route('home')->with('error_message', __('messages.errors.resource_not_found'));
        });
    })->create();
