<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RegistrationController;

// --- Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home'); // Map '/' and '?page=home'

// Authentication Routes (mimicking ?page=login/register & action=login/register)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // Map ?action=logout

// --- Authenticated Routes ---
Route::middleware('auth')->group(function () {
    // Profile & Password Change (mimicking ?page=profile/change_password)
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');
    Route::get('/change-password', [UserController::class, 'showChangePasswordForm'])->name('change-password.form');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('change-password.update');

    // Tour Registration (Placeholder - mimicking ?page=register_tour)
    // This will likely need more specific routes like /visits/{visit}/register
    Route::get('/register-tour', [RegistrationController::class, 'showTourRegistrationForm'])->name('register-tour.form'); // Placeholder
    Route::post('/register-tour', [RegistrationController::class, 'registerForTour'])->name('register-tour.submit'); // Placeholder

    // --- Admin (Configurator) Routes ---
    Route::middleware('can:configurator')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/configurator', [AdminController::class, 'showConfigurator'])->name('configurator'); // Map ?page=admin_configurator

        // User Management (mimicking action=add_user / action=remove_user)
        Route::post('/users', [AdminController::class, 'addUser'])->name('users.add');
        // Change DELETE to POST as a workaround for the MethodNotAllowed issue
        Route::post('/users/{user}/delete', [AdminController::class, 'removeUser'])->name('users.remove');

        // Add POST routes for Place and VisitType deletion
        Route::post('/places/{place}/delete', [AdminController::class, 'removePlace'])->name('places.remove');
        Route::post('/visit-types/{visit_type}/delete', [AdminController::class, 'removeVisitType'])->name('visit-types.remove');

        // Add routes for Place CRUD
        Route::get('/places/create', [AdminController::class, 'createPlace'])->name('places.create');
        Route::post('/places', [AdminController::class, 'storePlace'])->name('places.store');
        Route::get('/places/{place}/edit', [AdminController::class, 'editPlace'])->name('places.edit');
        Route::put('/places/{place}', [AdminController::class, 'updatePlace'])->name('places.update');

        // Add routes for Visit Type CRUD
        Route::get('/visit-types/create', [AdminController::class, 'createVisitType'])->name('visit-types.create');
        Route::post('/visit-types', [AdminController::class, 'storeVisitType'])->name('visit-types.store');
        Route::get('/visit-types/{visit_type}/edit', [AdminController::class, 'editVisitType'])->name('visit-types.edit');
        Route::put('/visit-types/{visit_type}', [AdminController::class, 'updateVisitType'])->name('visit-types.update');

        // Add other admin routes here (e.g., settings, visit planning)
    });

    // Add Volunteer routes here if needed
    // Add Fruitore specific routes here if needed (e.g., view my registrations)
});
