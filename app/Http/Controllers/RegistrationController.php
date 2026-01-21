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
use Illuminate\Support\Facades\Log;
use App\Services\BookingService;
use App\Data\BookingData;

class RegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Customer']);
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

    public function registerForTour(RegisterTourRequest $request, Visit $visit, BookingService $bookingService): RedirectResponse
    {
        $user = Auth::user();
        
        $error = $this->checkRegistrationEligibility($visit, $user, true);
        if ($error) {
            if ($error['type'] === 'already_registered') {
                return redirect()->route('user.dashboard')->with('status', $error['message']);
            }
            return back()->withErrors(['general' => $error['message']])->withInput();
        }

        // Create DTO
        $bookingData = new BookingData(
            visit_id: $visit->visit_id,
            user_id: $user->user_id,
            num_participants: (int) $request->input('num_participants')
        );

        try {
            // Delegate to Service
            $registration = $bookingService->book($bookingData);

            return redirect()->route('user.dashboard')
                             ->with('status', 'Registration successful! Ticket generating... Code: ' . $registration->booking_code);
        } catch (\Exception $e) {
            Log::error('Booking failed: '.$e->getMessage());
            return back()->withErrors(['general' => $e->getMessage()])->withInput();
        }
    }
}
