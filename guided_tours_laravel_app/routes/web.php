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

        // Add other admin routes here (e.g., settings, visit planning, add/edit place/visit_type)
    });

    // Add Volunteer routes here if needed
    // Add Fruitore specific routes here if needed (e.g., view my registrations)
});

// Fallback route for old ?page= style URLs (optional, can cause issues if not careful)
// Route::get('/index.php', function (Illuminate\Http\Request $request) {
//     $page = $request->input('page', 'home');
//     // Basic mapping - might need more complex logic
//     $routeMapping = [
//         'home' => 'home',
//         'login' => 'login',
//         'register' => 'register',
//         'profile' => 'profile',
//         'change_password' => 'change-password.form',
//         'admin_configurator' => 'admin.configurator',
//     ];
//     if (array_key_exists($page, $routeMapping)) {
//         return redirect()->route($routeMapping[$page]);
//     }
//     return redirect()->route('home'); // Default redirect
// });
