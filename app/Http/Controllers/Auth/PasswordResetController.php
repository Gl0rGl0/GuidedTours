<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;
        $token = Str::random(64);

        // Delete any existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Insert the new token
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token), // Hash the token in the DB
            'created_at' => Carbon::now()
        ]);

        // Send the email (always to the hardcoded test address, but with the user's email identifying the request)
        $testEmailAddress = 'g.felappi004@studenti.unibs.it';
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);

        Mail::to($testEmailAddress)->send(new PasswordResetMail($resetUrl));

        return back()->with('status', 'We have emailed your password reset link!');
    }

    /**
     * Display the password reset view for the given token.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Validate token exists and matches (using Hash::check because we hashed it)
        if (!$tokenData || !Hash::check($request->token, $tokenData->token)) {
            return back()->withInput($request->only('email'))
                         ->withErrors(['email' => 'This password reset token is invalid.']);
        }

        // Validate token has not expired (e.g. 1 hour)
        if (Carbon::parse($tokenData->created_at)->addMinutes(60)->isPast()) {
             return back()->withInput($request->only('email'))
                         ->withErrors(['email' => 'This password reset token has expired.']);
        }

        // Find user and update password
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withInput($request->only('email'))
                         ->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset!');
    }
}
