<?php

namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Http\Requests\RegisterTourRequest;
    use Illuminate\View\View;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Support\Facades\Auth;
    use App\Models\User; // Import User model for type hinting
    use App\Models\Visit;
    use App\Models\Registration;
    use Carbon\Carbon;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Log;

    class RegistrationController extends Controller
    {
        /**
         * Create a new controller instance.
         */
        public function __construct()
        {
            $this->middleware(['auth', 'role:fruitore']);
        }

        /**
         * Check common registration eligibility conditions.
         *
         * @param Visit $visit The visit to check.
         * @param User $user The authenticated user.
         * @param bool $isFormSubmissionContext True if checking during form submission, false for form display.
         * @return array|null An array with 'type' and 'message' if not eligible, null otherwise.
         */
        protected function checkRegistrationEligibility(Visit $visit, User $user, bool $isFormSubmissionContext = false): ?array
        {
            // This check for STATUS_COMPLETE might be too strict if it's used interchangeably with CONFIRMED (full).
            // The original code had this for showTourRegistrationForm.
            // If STATUS_COMPLETE means "event over", then this is fine.
            // If it means "full but not yet over", then it should be checked alongside CONFIRMED.
            // For now, keeping it as per original logic for showTourRegistrationForm,
            // but registerForTour has a more specific check for PROPOSED.
            // The user's feedback mentioned "This visit is already full" for STATUS_COMPLETE.
            // Let's assume STATUS_COMPLETE means it's not available for new registrations.
            if ($visit->status === Visit::STATUS_COMPLETE) {
                 // For showTourRegistrationForm, the original redirect was back()->withErrors().
                 // For consistency with feedback (redirect home/dashboard), we'll return a specific message.
                return ['type' => 'already_full_completed', 'message' => 'This visit is marked as complete and no longer open for registration.'];
            }

            if (in_array($visit->status, [Visit::STATUS_CANCELLED, Visit::STATUS_EFFECTED])) {
                return ['type' => 'unavailable', 'message' => 'This visit is no longer available for registration (cancelled or already effected).'];
            }

            // Registration closes 3 days before the visit date.
            if (Carbon::parse($visit->visit_date)->lt(Carbon::today()->addDays(3))) {
                return ['type' => 'deadline_passed', 'message' => 'Registration for this visit is closed as the deadline has passed.'];
            }

            // Check for existing registration
            if (Registration::where('user_id', Auth::id())->where('visit_id', $visit->visit_id)->exists()) {
                return ['type' => 'already_registered', 'message' => 'You have already registered for this visit.'];
            }

            // Specific check for form submission: must be PROPOSED to create a new registration
            if ($isFormSubmissionContext && $visit->status !== Visit::STATUS_PROPOSED) {
                return ['type' => 'not_proposed_for_submission', 'message' => 'Registration is not currently open for this visit status. Only proposed visits can be registered for.'];
            }
            
            // If the visit is CONFIRMED, new registrations are not allowed (it's full).
            // This check is implicitly handled by `not_proposed_for_submission` for submissions.
            // For showing the form, if it's confirmed, they can't register, but might view details.
            // The original showTourRegistrationForm didn't explicitly block CONFIRMED from view,
            // it relied on the "already_full" message for STATUS_COMPLETE.
            // We'll let the view handle the "View Details" for confirmed tours.

            return null; // Eligible
        }


        /**
         * Show the form for registering for a specific tour visit.
         */
        public function showTourRegistrationForm(Request $request): View|RedirectResponse
        {
            $visit_id = $request->input('visit_id');
            /** @var Visit $visit */
            $visit = Visit::with(['visitType', 'registrations'])->findOrFail($visit_id);
            /** @var User $user */
            $user = Auth::user();

            $eligibilityError = $this->checkRegistrationEligibility($visit, $user, false);

            if ($eligibilityError) {
                if ($eligibilityError['type'] === 'already_registered') {
                    // Consistent with feedback: redirect to dashboard with an info message
                    return redirect()->route('user.dashboard')->with('warning', $eligibilityError['message']);
                }
                // For other errors when just trying to view the form, redirect to home with a general error message.
                // The original code used back()->withErrors() which is less user-friendly for GET requests.
                return redirect()->route('home')->with('error_message', $eligibilityError['message']);
            }

            return view('tours.register', ['visit' => $visit]);
        }

        /**
         * Handle the submission of the tour registration form.
         */
        public function registerForTour(RegisterTourRequest $request): RedirectResponse
        {
            /** @var Visit $visit */
            $visit = Visit::with(['visitType','registrations'])->findOrFail($request->visit_id);
            /** @var User $user */
            $user = Auth::user();

            // Perform common eligibility checks relevant to form submission
            $eligibilityError = $this->checkRegistrationEligibility($visit, $user, true);
            if ($eligibilityError) {
                 // For 'already_registered', redirect to dashboard with status (info)
                if ($eligibilityError['type'] === 'already_registered') {
                    return redirect()->route('user.dashboard')->with('status', $eligibilityError['message']);
                }
                // For other submission errors, go back with errors
                return back()->withErrors(['general' => $eligibilityError['message']])->withInput();
            }

            // Capacity check (remains here as it depends on num_participants from the request)
            $num_participants = $request->input('num_participants');
            $remainingCapacity = $visit->visitType->max_participants - $visit->registrations->sum('num_participants');
            if ($num_participants > $remainingCapacity) {
                return back()
                    ->withErrors(['num_participants' => "Not enough capacity for {$num_participants} participants. Remaining capacity: {$remainingCapacity}."])
                    ->withInput();
            }

            $bookingCode = 'BK' . $visit->visit_id . 'U' . Auth::id() . Str::random(4);

            try {
                Log::info("Creating registration for user ID: " . Auth::id());
                Registration::create([
                    'visit_id' => $visit->visit_id,
                    'user_id' => Auth::id(),
                    'num_participants' => $num_participants,
                    'booking_code' => $bookingCode,
                    'registered_at' => now(),
                ]);

            $visit->refresh();
            // After successful registration, check if the visit is now full and update status
            $currentSubscribers = $visit->registrations()->sum('num_participants');
            if ($visit->visitType && $currentSubscribers >= $visit->visitType->max_participants) {
                if ($visit->status === Visit::STATUS_PROPOSED) {
                    $visit->status = Visit::STATUS_COMPLETE;
                    $visit->save();
                    Log::info("Visit ID {$visit->visit_id} status updated to CONFIRMED as it reached maximum capacity.");
                }
            }

            return redirect()->route('user.dashboard')
                            ->with('status', 'Registration successful! Your booking code is: ' . $bookingCode);
            } catch (\Exception $e) {
                Log::error("Tour registration failed for user " . Auth::id() . ": " . $e->getMessage(), ['exception' => $e]);
                return back()
                    ->withInput()
                    ->withErrors(['general' => 'Registration failed. Please try again.']);
            }
        }
    }
