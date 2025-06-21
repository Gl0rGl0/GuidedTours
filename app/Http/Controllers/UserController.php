<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View; 
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse; 

class UserController extends Controller
{
    public function showProfile(): View
    {
        $user = Auth::user();
        return view('user.profile', ['user' => $user]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'nullable|string|max:50',
            'last_name'  => 'nullable|string|max:50',
            'birth_date' => 'nullable|date|before:today',
        ]);

        Auth::user()->update($data);
        return redirect()->back()->with('success', 'Profile updated correctly.');
    }

    public function showChangePasswordForm(): View
    {
        return view('user.change-password');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The provided current password does not match your actual password.');
                }
            }],
            'new_password' => ['required', 'confirmed', Password::min(6)],
        ]);

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
