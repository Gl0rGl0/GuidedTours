<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\LoginController;

// Public routes
Route::middleware('throttle:5,1')->post('/login', [LoginController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

