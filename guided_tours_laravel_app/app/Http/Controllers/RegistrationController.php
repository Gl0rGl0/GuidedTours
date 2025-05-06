<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View; // Import View
use Illuminate\Http\RedirectResponse; // Import RedirectResponse
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Validator; // Import Validator facade
use App\Models\Visit; // Import Visit model
use App\Models\Registration; // Import Registration model
use Carbon\Carbon; // Import Carbon
use Illuminate\Support\Str; // Import Str facade for booking code
use Illuminate\Support\Facades\Log; // Import Log facade

class RegistrationController extends Controller
{
    /**
     * Show the form for registering for a specific tour visit.
     */
    public function showTourRegistrationForm(Request $request): View|RedirectResponse
    {
        // Ensure only authenticated 'fruitori' can access this page
        // Using Spatie's hasRole() method
        if (Auth::guest() || !Auth::user()->hasRole('fruitore')) {
            return redirect()->route('home')->with('error_message', 'You must be logged in as a User to register for tours.');
        }

        // Get visit_id from request or route parameter
        // Using route parameter is generally preferred for resource-like URLs
        $visit_id = $request->input('visit_id'); // Assuming visit_id is passed as query parameter for now

        // Fetch visit details and eager load relationships
        $visit = Visit::with(['visitType', 'registrations'])
                      ->findOrFail($visit_id); // Use findOrFail to show 404 if visit not found

        // Check visit status and date for form display
        if (in_array($visit->status, [Visit::STATUS_CANCELLED, Visit::STATUS_COMPLETE, Visit::STATUS_EFFECTED])) {
            return redirect()->route('home')->with('error_message', 'This visit is no longer available for registration.');
        }

        // Registration closes 3 days before the visit date.
        // Registration closes 3 days before the visit date.
        // If visit_date is less than 3 days from today (i.e. visit is today, tomorrow, or day after), registration is closed.
        if (Carbon::parse($visit->visit_date)->lt(Carbon::today()->addDays(3))) {
            return redirect()->route('home')->with('error_message', 'Registration for this visit is closed as the deadline has passed.');
        }


        // Check if the user has already registered for this visit
        $existingRegistration = Registration::where('user_id', Auth::user()->user_id) // Use user_id
                                            ->where('visit_id', $visit->visit_id)
                                            ->first();

        if ($existingRegistration) {
            // Redirect to a page showing their existing registration or home with a message
            return redirect()->route('user.dashboard')->with('status', 'You have already registered for this visit.');
        }


        // Return the view with visit details
        return view('tours.register', ['visit' => $visit]);
    }

     /**
     * Handle the submission of the tour registration form.
     */
    public function registerForTour(Request $request): RedirectResponse
    {
        // Ensure only authenticated 'fruitori' can submit this form
        // Using Spatie's hasRole() method
        if (Auth::guest() || !Auth::user()->hasRole('fruitore')) {
            return redirect()->route('home')->with('error_message', 'You must be logged in as a User to register for tours.');
        }

        $user = Auth::user();

        // Validate input
        $validator = Validator::make($request->all(), [
            'visit_id' => ['required', 'exists:visits,visit_id'],
            'num_participants' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $visit_id = $request->input('visit_id');
        $num_participants = $request->input('num_participants');

        // Fetch the visit again to check capacity and status
        $visit = Visit::with(['visitType','registrations'])->findOrFail($visit_id); // Eager load visitType for max_participants

        // Registration eligibility checks
        if (in_array($visit->status, [Visit::STATUS_CANCELLED, Visit::STATUS_COMPLETE, Visit::STATUS_EFFECTED])) {
            return back()->withErrors(['general' => 'Registration is closed for this visit as it is cancelled or has already occurred.'])->withInput();
        }

        // Registration closes 3 days before the visit date.
        // This means if the visit is on D, D+1, or D+2, registration is closed.
        // Carbon::parse($visit->visit_date) gives the start of that day.
        // Carbon::today()->addDays(3) gives the start of the day 3 days from now.
        // If visit_date is strictly less than today + 3 days, it's too late.
        if (Carbon::parse($visit->visit_date)->lt(Carbon::today()->addDays(3))) {
            return back()->withErrors(['general' => 'The registration deadline for this visit has passed.'])->withInput();
        }

        // If status is not 'proposed' or 'confirmed', it's not open (e.g. if a new status is added later)
        if (!in_array($visit->status, [Visit::STATUS_PROPOSED, Visit::STATUS_CONFIRMED])) {
            return back()->withErrors(['general' => 'Registration is not currently open for this visit status.'])->withInput();
        }

        // Check if the user has already registered for this visit
        $existingRegistration = Registration::where('user_id', $user->user_id) // Use user_id
                                            ->where('visit_id', $visit->visit_id)
                                            ->first();

        if ($existingRegistration) {
            return back()->withErrors(['general' => 'You have already registered for this visit.'])->withInput();
        }

        // Check if there is enough capacity
        $currentSubscribers = $visit->registrations->sum('num_participants');
        $remainingCapacity = $visit->visitType->max_participants - $currentSubscribers;

        if ($num_participants > $remainingCapacity) {
            return back()->withErrors(['num_participants' => "Not enough capacity for {$num_participants} participants. Remaining capacity: {$remainingCapacity}."])->withInput();
        }

        // Create booking code (simple example: BK + Visit ID + User ID + Random String)
        $bookingCode = 'BK' . $visit->visit_id . 'U' . $user->user_id . Str::random(4); // Use user_id

        // Save the registration
        try {
            // Log the user ID before creating the registration
            Log::info("Attempting to create registration for user ID: " . $user->user_id); // Log user_id

            Registration::create([
                'visit_id' => $visit->visit_id,
                'user_id' => $user->user_id, // Use user_id
                'num_participants' => $num_participants,
                'booking_code' => $bookingCode,
                'registered_at' => now(), // Use the registered_at timestamp
            ]);

            // Redirect to the fruitore dashboard page showing their bookings
            return redirect()->route('user.dashboard')->with('status', 'Registration successful! Your booking code is: ' . $bookingCode);
        } catch (\Exception $e) {
            // Log error
            Log::error("Tour registration failed for user {$user->user_id} and visit {$visit->visit_id}: " . $e->getMessage(), ['exception' => $e]); // Log user_id
            return back()->withInput()->withErrors(['general' => 'Registration failed. Please try again.']);
        }
    }
}
