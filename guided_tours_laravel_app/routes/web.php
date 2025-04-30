<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\PlaceController; // Import Admin\PlaceController
use App\Http\Controllers\Admin\VisitTypeController; // Import Admin\VisitTypeController
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\VolunteerController; // Import VolunteerController
use App\Http\Controllers\FruitoreController; // Import FruitoreController
use App\Http\Controllers\VisitPlanningController; // Import VisitPlanningController

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
    Route::middleware('role:configurator')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/configurator', [AdminController::class, 'showConfigurator'])->name('configurator'); // Map ?page=admin_configurator

        // User Management (mimicking action=add_user / action=remove_user)
        Route::post('/users', [AdminController::class, 'addUser'])->name('users.add');
        Route::delete('/users/{user}', [AdminController::class, 'removeUser'])->name('users.destroy');

        Route::delete('/places/{place}', [PlaceController::class, 'removePlace'])->name('places.destroy');
        Route::delete('/visit-types/{visit_type}', [VisitTypeController::class, 'removeVisitType'])->name('visit-types.destroy');

        // Add routes for Place CRUD
        Route::resource('places', PlaceController::class)->except([
            'index', 'show', 'destroy' // Define destroy separately for clarity/consistency with users/visit-types
        ]);

        // Add routes for Visit Type CRUD
         Route::resource('visit-types', VisitTypeController::class)->except([
            'index', 'show', 'destroy' // Define destroy separately
        ]);

        // Add other admin routes here (e.g., settings, visit planning)
        Route::get('/visit-planning', [VisitPlanningController::class, 'index'])->name('visit-planning.index');
    });

    // --- Volunteer Routes ---
    Route::middleware('role:volunteer')->prefix('volunteer')->name('volunteer.')->group(function () {
        Route::get('/availability', [VolunteerController::class, 'showAvailabilityForm'])->name('availability.form');
        Route::post('/availability', [VolunteerController::class, 'storeAvailability'])->name('availability.store');
        // Add other volunteer routes here (e.g., view assigned visits)
    });

    // Add Fruitore specific routes here
    Route::middleware('role:fruitore')->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [FruitoreController::class, 'dashboard'])->name('dashboard');
        // Route for cancelling a booking
        Route::delete('/bookings/{booking}', [FruitoreController::class, 'cancelBooking'])->name('bookings.cancel');
        // Add other fruitore routes here
    });

    // Route to set custom time for testing
});
