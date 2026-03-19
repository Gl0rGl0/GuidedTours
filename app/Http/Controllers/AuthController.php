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
use Illuminate\Support\Facades\Log; 

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            Auth::login($user, $request->filled('remember'));

            return redirect()->route('home')->with('status', __('messages.auth.login.login_success'));
        }

        return back()
            ->with('error', __('messages.auth.login.invalid_credentials'))
            ->withInput(); // Mantiene l'email scritta dall'utente nel form
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', __('messages.common.logout_success'));
    }

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user->assignRole('Customer');

            return redirect()->route('login')->with('status', __('messages.user.dashboard.registration_success'));

        } catch (\Exception $e) {
            Log::error("Registration failed: " . $e->getMessage());
            return back()->withInput()->withErrors(['email' => __('messages.user.dashboard.registration_failed')]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | API Authentication Methods (Sanctum)
    |--------------------------------------------------------------------------
    */

    /**
     * Handle an API login request.
     */
    public function apiLogin(Request $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Create a token for the user
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'Login successful',
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    /**
     * Handle an API logout request.
     */
    public function apiLogout(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
