<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Hash; // Import Hash facade
use Illuminate\Validation\Rules\Password; // Import Password rule
use Illuminate\View\View; // Import View
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Http\RedirectResponse; // Import RedirectResponse

class UserController extends Controller
{
    /**
     * Show the user's profile page.
     * (Placeholder - implement actual profile view later)
     */
    public function showProfile(): View
    {
        $user = Auth::user();
        return view('user.profile', ['user' => $user]);
    }

    /**
     * Show the form for changing the password.
     */
    public function showChangePasswordForm(): View
    {
        return view('user.change-password');
    }

    /**
     * Handle the request to change the user's password.
     */
    public function changePassword(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validate input
        $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The provided current password does not match your actual password.');
                }
            }],
            'new_password' => ['required', 'confirmed', Password::min(6)],
        ]);

        // Update the password
        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            return back()->with('status', 'Password updated successfully!');

        } catch (\Exception $e) {
            Log::error("Password change failed for user {$user->user_id}: " . $e->getMessage());
            return back()->withErrors(['current_password' => 'Failed to update password. Please try again.']);
        }
    }
}
