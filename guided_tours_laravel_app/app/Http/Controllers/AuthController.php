<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Redirect; // Import Redirect facade
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash; // Import Hash facade
use Illuminate\Validation\Rules\Password; // Import Password rule
use App\Models\User; // Import User model

class AuthController extends Controller
{
    /**
     * Display the login form.
     */
    public function showLoginForm(): View
    {
        // We'll create this view later in Phase 2, Step 8
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'], // Use 'username' as per our schema/model
            'password' => ['required'],
        ]);

        // Attempt to log the user in
        // Note: Auth::attempt expects 'password', not 'password_hash'
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Re-fetch the user after successful authentication to ensure the model is fresh
            $user = Auth::user();
            Auth::login($user, $request->filled('remember'));

            // Redirect to intended page or home
            return Redirect::intended(route('home'));
        }

        // If authentication fails, redirect back with error
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.', // Generic error
        ])->onlyInput('username');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Redirect to home page after logout
    }

    /**
     * Display the registration form.
     */
    public function showRegistrationForm(): View
    {
        // We'll create this view later in Phase 2, Step 8
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'], // Max length from schema
            'password' => ['required', 'confirmed', Password::min(6)], // Use Password rule, confirm matches password_confirmation field
        ]);

        // Create the user
        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                // Removed 'role' as roles are managed by Spatie
                'first_login' => false, // Assuming they don't need to change password immediately
            ]);

            // Assign the 'fruitore' role using Spatie
            $user->assignRole('fruitore');

            // Optionally log the user in after registration
            // Auth::login($user);

            // Redirect to login page with success message
            return redirect()->route('login')->with('status', 'Registration successful! Please log in.');

        } catch (\Exception $e) {
            // Log error (implement proper logging later)
            // Log::error("Registration failed: " . $e->getMessage());
            return back()->withInput()->withErrors(['username' => 'Registration failed. Please try again.']);
        }
    }

    // Redundant logout method removed. The first one (lines 48-58) is kept.
}
