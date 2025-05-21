<?php

use App\Http\Controllers\Admin\VisitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\PlaceController;
use App\Http\Controllers\Admin\VisitTypeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\FruitoreController;

// --- Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home'); // Map '/' and '?page=home'

// Authentication Routes (mimicking ?page=login/register & action=login/register)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // Map ?action=logout

// Default route, error and back to the home
Route::fallback(function () {
    return redirect()->route('home')->with('error', 'Pagina non trovata.');
});

// --- Authenticated Routes ---
Route::middleware('auth')->group(function () {
    // Profile & Password Change (mimicking ?page=profile/change_password)
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');
    Route::get('/change-password', [UserController::class, 'showChangePasswordForm'])->name('change-password.form');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('change-password.update');

    // Edit profile
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    
    // Tour Registration
    Route::get('/visits/{visit}/register', [RegistrationController::class, 'showTourRegistrationForm'])->name('visits.register.form');
    Route::post('/visits/{visit}/register', [RegistrationController::class, 'registerForTour'])->name('visits.register.submit');
    
    // --- Admin (Configurator) Routes ---
    Route::middleware('role:configurator')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/configurator', [AdminController::class, 'showConfigurator'])->name('configurator'); // Map ?page=admin_configurator

        // User Management
        Route::post('/users', [AdminController::class, 'addUser'])->name('users.add');      
        Route::delete('/users/{user}', [AdminController::class, 'removeUser'])->name('users.destroy');

        Route::delete('/places/{place}', [PlaceController::class, 'removePlace'])->name('places.destroy');
        Route::delete('/visit-types/{visit_type}', [VisitTypeController::class, 'removeVisitType'])->name('visit-types.destroy');

        // Add routes for Place CRUD
        Route::resource('places', PlaceController::class)->except([
            'destroy' // Define destroy separately
        ]);

        // Add routes for Visit Type CRUD
         Route::resource('visit-types', VisitTypeController::class)->except([
            'destroy' // Define destroy separately
        ]);

        // Add routes for Visits CRUD
        Route::resource('visits', VisitController::class);

        // Past Visits Page (configurator)
        Route::get('/past-visits', [VisitController::class, 'showvisits'])->name('visits.past');

        // Add other admin routes here (e.g., settings, visit planning)
        Route::get('/visit-planning', [VisitController::class, 'index'])->name('visit-planning.index');

        Route::get('/volunteers/available', [VisitController::class, 'getAvailableVolunteers'])->name('volunteers.available');
    });

    // --- Volunteer Routes ---
    Route::middleware('role:volunteer')->prefix('volunteer')->name('volunteer.')->group(function () {
        Route::get('/availability', [VolunteerController::class, 'showAvailabilityForm'])->name('availability.form');
        Route::post('/availability', [VolunteerController::class, 'storeAvailability'])->name('availability.store');

        // Past Visits Page (volunteer)
        Route::get('/past-visits', [VisitController::class, 'showAssignedVisits'])->name('visits.past');
    });


    // --- Fruitore Routes ---
    Route::middleware('role:fruitore')->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [FruitoreController::class, 'dashboard'])->name('dashboard');
        // Route for cancelling a booking
        Route::delete('/bookings/{booking}', [FruitoreController::class, 'cancelBooking'])->name('bookings.cancel');
        // Past Visits Page (fruitore)
        Route::get('/past-visits', [VisitController::class, 'showMyPastVisits'])->name('visits.past');
    });
});
