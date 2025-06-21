<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof HttpException) {
            switch ($exception->getStatusCode()) {
                case 401:
                    return redirect()
                        ->route('login')
                        ->with('error', 'Devi essere autenticato per accedere a questa pagina.');

                case 403:
                    return redirect()
                        ->route('home')
                        ->with('error', 'Accesso non consentito.');

                case 404:
                    return redirect()
                        ->route('home')
                        ->with('error', 'Pagina non trovata.');

                case 500: // Internal Server Error
                    return redirect()
                        ->route('home')
                        ->with('error', 'Errore interno del server. Riprova più tardi.');
            }
        }

        return parent::render($request, $exception);
    }
}
