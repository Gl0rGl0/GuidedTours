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

// Authentication Controller
use App\Http\Controllers\Auth\PasswordResetController;

// --- Root Route (Language Auto-Detector) ---
Route::get('/', function (Illuminate\Http\Request $request) {
    // Detect preferred language from English and Italian
    $preferred = $request->getPreferredLanguage(['en', 'it']);
    return redirect()->route('home', ['locale' => $preferred]);
});

// --- Localized Routes ---
Route::prefix('{locale}')->where(['locale' => 'en|it'])->middleware('setlocale')->group(function () {
    
    // --- Public Routes ---
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    Route::get('/about-us', [HomeController::class, 'about'])->name('about');
    Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
    Route::get('/careers', [HomeController::class, 'careers'])->name('careers');

    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rate-limited auth POST routes (max 5 attempts per minute)
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
    });

    // Default route, error and back to the home
    Route::fallback(function () {
        return redirect()->route('home', ['locale' => app()->getLocale()])->with('error', 'Pagina non trovata.');
    });

    // --- Authenticated Routes ---
    Route::middleware('auth')->group(function () {
        // Profile & Password Change
        Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');
        Route::get('/change-password', [UserController::class, 'showChangePasswordForm'])->name('change-password.form');
        Route::post('/change-password', [UserController::class, 'changePassword'])->name('change-password.update');
        
        // Edit profile
        Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
        
        // Tour Registration
        Route::get('/visits/{visit}/register', [RegistrationController::class, 'showTourRegistrationForm'])->name('visits.register.form');
        Route::post('/visits/{visit}/register', [RegistrationController::class, 'registerForTour'])->name('visits.register.submit');
        
        // Ticket Download
        Route::get('/tickets/{code}/download', [App\Http\Controllers\TicketController::class, 'download'])->name('tickets.download');

        // --- Admin (Admin) Routes ---
        Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/configurator', [AdminController::class, 'showConfigurator'])->name('configurator');
            
            // User Management
            Route::post('/users', [AdminController::class, 'addUser'])->name('users.add');      
            Route::delete('/users/{user}', [AdminController::class, 'removeUser'])->name('users.destroy');
            
            Route::delete('/places/{place}', [PlaceController::class, 'removePlace'])->name('places.destroy');
            Route::delete('/visit-types/{visit_type}', [VisitTypeController::class, 'removeVisitType'])->name('visit-types.destroy');
            

            Route::post('/ai/enhance', [AdminController::class, 'enhanceContent'])->name('ai.enhance');

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
        
        // --- Guide Routes ---
        Route::middleware('role:Guide')->prefix('volunteer')->name('volunteer.')->group(function () {
            Route::get('/availability', [VolunteerController::class, 'showAvailabilityForm'])->name('availability.form');
            Route::post('/availability', [VolunteerController::class, 'storeAvailability'])->name('availability.store');
            
            // Past Visits Page (volunteer)
            Route::get('/past-visits', [VisitController::class, 'showAssignedVisits'])->name('visits.past');
        });
        
        
        // --- Customer Routes ---
        Route::middleware('role:Customer')->prefix('user')->name('user.')->group(function () {
            Route::get('/dashboard', [FruitoreController::class, 'dashboard'])->name('dashboard');
            // Route for cancelling a booking
            Route::delete('/bookings/{booking}', [FruitoreController::class, 'cancelBooking'])->name('bookings.cancel');
            // Past Visits Page (fruitore)
            Route::get('/past-visits', [VisitController::class, 'showMyPastVisits'])->name('visits.past');
        });
    });
}); // <---- End of Localized Route Group ---->


Route::get('/test-error', function () {
    throw new \Exception('Errore di test!');
});

Route::get('/test-email', function () {
    $html = '<p>Congrats on sending your <strong>first email</strong>!</p>';

    \Illuminate\Support\Facades\Mail::html($html, function (\Illuminate\Mail\Message $message) {
        $message->to('g.felappi004@studenti.unibs.it')
                ->subject('Hello World');
    });

    return 'Successfully sent email via Resend!';
});