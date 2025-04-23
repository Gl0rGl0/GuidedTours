<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Exception;

trait HandlesAdminOperations
{
    /**
     * Execute an admin operation with common error handling and redirection.
     *
     * @param callable $operation The core logic to execute.
     * @param string $successMessage The message to flash on success.
     * @param string $failureMessage The message to flash on failure.
     * @param string $redirectRoute The route to redirect to on success or failure.
     * @param array $redirectParams Parameters for the redirect route.
     * @return RedirectResponse
     */
    protected function handleAdminOperation(
        callable $operation,
        string $successMessage,
        string $failureMessage,
        string $redirectRoute,
        array $redirectParams = []
    ): RedirectResponse {
        try {
            $operation();
            return Redirect::route($redirectRoute, $redirectParams)->with('status', $successMessage);
        } catch (Exception $e) {
            Log::error("Admin Operation Failed: " . $e->getMessage(), ['exception' => $e]);
            return Redirect::back()->withInput()->withErrors(['general' => $failureMessage . ' Error: ' . $e->getMessage()]);
        }
    }
}
