<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View; // Import View
use Carbon\Carbon; // Import Carbon
use App\Models\Visit; // Import Visit model
use Illuminate\Support\Facades\Log; // Import Log facade

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
                ->whereDate('visit_date', '>=', Carbon::today()) // Use Carbon for today's date
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
}
