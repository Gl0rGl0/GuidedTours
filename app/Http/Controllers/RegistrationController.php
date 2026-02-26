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
            return ['type' => 'already_full', 'message' => __('messages.tours_views.register.error_complete')];
        }

        if (in_array($visit->status, [Visit::STATUS_CANCELLED, Visit::STATUS_EFFECTED])) {
            return ['type' => 'unavailable', 'message' => __('messages.tours_views.register.error_unavailable')];
        }

        if (Carbon::parse($visit->visit_date)->lt(Carbon::today()->addDays(3))) {
            return ['type' => 'deadline_passed', 'message' => __('messages.tours_views.register.error_deadline')];
        }

        if (Registration::where('user_id', $user->user_id)->where('visit_id', $visit->visit_id)->exists()) {
            return ['type' => 'already_registered', 'message' => __('messages.tours_views.register.error_already_registered')];
        }

        if ($isSubmission && $visit->status !== Visit::STATUS_PROPOSED) {
            return ['type' => 'not_open', 'message' => __('messages.tours_views.register.error_not_open')];
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
                             ->with('status', __('messages.user.dashboard.registration_success'));
        } catch (\Exception $e) {
            Log::error('Booking failed: '.$e->getMessage());
            return back()->withErrors(['general' => __('messages.tours_views.register.error_booking_failed')])->withInput();
        }
    }
}
