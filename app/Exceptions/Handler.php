<?php

namespace App\Exceptions;

use BadMethodCallException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof BadMethodCallException) {
            return redirect()
                ->route('home')
                ->with('error', 'Resource not found.');
        }

        if ($exception instanceof HttpException) {
            switch ($exception->getStatusCode()) {
                case 401:
                    return redirect()
                        ->route('login')
                        ->with('error', 'You must be authenticated to access this page.');

                case 403:
                    return redirect()
                        ->route('home')
                        ->with('error', 'Access denied.');

                case 404:
                    return redirect()
                        ->route('home')
                        ->with('error', 'Page not found.');

                case 500:
                    return redirect()
                        ->route('home')
                        ->with('error', 'Internal server error. Please try again later.');
            }
            return redirect()
                        ->route('home')
                        ->with('error', 'Exception error. Please try again later.');
        }

        return redirect()
            ->route('home')
            ->with('error', 'An unexpected error occurred. Please try again later.');

        // return parent::render($request, $exception);
    }
}
