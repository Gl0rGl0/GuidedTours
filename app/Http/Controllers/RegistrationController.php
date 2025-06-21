<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterTourRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Visit;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:fruitore']);
    }

    protected function checkRegistrationEligibility(Visit $visit, User $user, bool $isSubmission = false): ?array
    {
        if ($visit->status === Visit::STATUS_COMPLETE) {
            return ['type' => 'already_full', 'message' => 'This visit is marked as complete and no longer open for registration.'];
        }

        if (in_array($visit->status, [Visit::STATUS_CANCELLED, Visit::STATUS_EFFECTED])) {
            return ['type' => 'unavailable', 'message' => 'This visit is no longer available for registration.'];
        }

        if (Carbon::parse($visit->visit_date)->lt(Carbon::today()->addDays(3))) {
            return ['type' => 'deadline_passed', 'message' => 'Registration for this visit is closed as the deadline has passed.'];
        }

        if (Registration::where('user_id', $user->user_id)->where('visit_id', $visit->visit_id)->exists()) {
            return ['type' => 'already_registered', 'message' => 'You have already registered for this visit.'];
        }

        if ($isSubmission && $visit->status !== Visit::STATUS_PROPOSED) {
            return ['type' => 'not_open', 'message' => 'Registration is not currently open for this visit status.'];
        }

        return null;
    }

    public function showTourRegistrationForm(Visit $visit): View|RedirectResponse
    {
        $user = Auth::user();
        $error = $this->checkRegistrationEligibility($visit, $user, false);

        if ($error) {
            if ($error['type'] === 'already_registered') {
                return redirect()->route('user.dashboard')->with('warning', $error['message']);
            }
            return redirect()->route('home')->with('error_message', $error['message']);
        }

        $visit->load(['visitType', 'registrations']);
        return view('tours.register', compact('visit'));
    }

    public function registerForTour(RegisterTourRequest $request, Visit $visit): RedirectResponse
    {
        $user = Auth::user();
        Log::info($user);
        Log::info($user->user_id);
        $error = $this->checkRegistrationEligibility($visit, $user, true);

        if ($error) {
            if ($error['type'] === 'already_registered') {
                return redirect()->route('user.dashboard')->with('status', $error['message']);
            }
            return back()->withErrors(['general' => $error['message']])->withInput();
        }

        $participants = $request->input('num_participants');
        $remaining = $visit->visitType->max_participants - $visit->registrations->sum('num_participants');
        if ($participants > $remaining) {
            return back()->withErrors(['num_participants' => "Only {$remaining} spots left."])->withInput();
        }

        $code = 'BK'.$visit->visit_id.'U'.$user->user_id.Str::random(4);
        try {
            Registration::create([
                'visit_id' => $visit->visit_id,
                'user_id' => $user->user_id,
                'num_participants' => $participants,
                'booking_code' => $code,
                'registered_at' => now(),
            ]);

            $visit->refresh();
            if ($visit->visitType->max_participants <= $visit->registrations()->sum('num_participants')
                && $visit->status === Visit::STATUS_PROPOSED) {
                $visit->status = Visit::STATUS_COMPLETE;
                $visit->save();
                Log::info("Visit {$visit->visit_id} marked complete after reaching capacity.");
            }

            return redirect()->route('user.dashboard')
                             ->with('status', 'Registration successful! Code: '.$code);
        } catch (\Exception $e) {
            Log::error('Registration failed: '.$e->getMessage(), ['user' => $user->user_id, 'visit' => $visit->visit_id]);
            return back()->withErrors(['general' => 'Registration failed, please try again.'])->withInput();
        }
    }
}
