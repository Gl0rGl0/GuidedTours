<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View; // Import View
use Carbon\Carbon; // Import Carbon
use App\Models\Visit; // Import Visit model
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Support\Facades\Session; // Import Session facade
use Illuminate\Http\RedirectResponse; // Import RedirectResponse

class HomeController extends Controller
{
    /**
     * Show the application dashboard (home page with available tours).
     */
    public function index(): View
    {
        $available_tours = collect(); // Default to empty collection
        $error_message = null;

        try {
            // Fetch proposed and confirmed visits using Eloquent with eager loading
            $available_tours = Visit::with(['visitType.place', 'registrations']) // Eager load visitType (with place) and registrations
                ->whereIn('status', ['proposed', 'confirmed'])
                // Use the custom time if set, otherwise use Carbon::today()
                ->whereDate('visit_date', '>=', $this->getCurrentTime()->startOfDay())
                ->orderBy('visit_date')
                // Removed orderBy('visitType.start_time') due to potential issues
                ->get();

        } catch (\Exception $e) {
            // Log error
            Log::error("Error fetching available tours on home page: " . $e->getMessage());
            $error_message = "Sorry, we couldn't retrieve the tour list at this time.";
        }

        // Pass data to the view
        return view('home', [
            'available_tours' => $available_tours,
            'error_message' => $error_message // Pass error message if any
        ]);
    }

    /**
     * Set a custom time in the session for testing.
     */
    public function setCustomTime(Request $request): RedirectResponse
    {
        $request->validate([
            'custom_time' => ['required', 'date_format:Y-m-d H:i:s'],
        ]);

        Session::put('custom_time', $request->input('custom_time'));

        return back()->with('status', 'Custom time set successfully!');
    }

    /**
     * Remove the custom time from the session.
     */
    public function resetCustomTime(): RedirectResponse
    {
        Session::forget('custom_time');

        return back()->with('status', 'Custom time reset successfully!');
    }

    /**
     * Get the current time, considering the custom time in the session.
     */
    protected function getCurrentTime(): Carbon
    {
        return Session::has('custom_time')
            ? Carbon::parse(Session::get('custom_time'))
            : Carbon::now();
    }
}
