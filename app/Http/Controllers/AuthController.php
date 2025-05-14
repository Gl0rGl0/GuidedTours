<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Redirect; 
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Validation\Rules\Password; 
use App\Models\User; 

class AuthController extends Controller
{
    /**
     * Display the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
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
            // Uses: when in home user click to view a visit, his intended route is the visit
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

        return redirect()->route('home')->with('status', 'Logout effected.');
    }

    /**
     * Display the registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users,username'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        // Create the user
        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password)
            ]);

            // Assign the 'fruitore' role
            $user->assignRole('fruitore');

            // Redirect to login page with success message
            return redirect()->route('login')->with('status', 'Registration successful! Please log in.');

        } catch (\Exception $e) {
            Log::error("Registration failed: " . $e->getMessage());
            return back()->withInput()->withErrors(['username' => 'Registration failed. Please try again.']);
        }
    }
}
