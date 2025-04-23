<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View; // Import View
use Illuminate\Http\RedirectResponse; // Import RedirectResponse
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Validator; // Import Validator facade
use App\Models\Visit; // Import Visit model
use App\Models\Registration; // Import Registration model
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
        if (Auth::guest() || Auth::user()->role !== 'fruitore') {
            return redirect()->route('home')->with('error_message', 'You must be logged in as a User to register for tours.');
        }

        // Get visit_id from request or route parameter
        // Using route parameter is generally preferred for resource-like URLs
        $visit_id = $request->input('visit_id'); // Assuming visit_id is passed as query parameter for now

        // Fetch visit details and eager load relationships
        $visit = Visit::with(['visitType', 'registrations'])
                      ->findOrFail($visit_id); // Use findOrFail to show 404 if visit not found

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
        if (Auth::guest() || Auth::user()->role !== 'fruitore') {
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
        $visit = Visit::with('registrations')->findOrFail($visit_id);

        // Check if registration is open
        if ($visit->status !== 'proposed') {
             return back()->withErrors(['general' => 'Registration is not open for this visit.'])->withInput();
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
