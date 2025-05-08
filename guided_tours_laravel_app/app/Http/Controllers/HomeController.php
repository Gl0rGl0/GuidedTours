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
        $proposed_visits = collect(); // Default to empty collection
        $confirmed_visits = collect(); // Default to empty collection
        $error_message = null;

        try {
            // Fetch proposed visits
            $proposed_visits = Visit::with(['visitType.place', 'registrations'])
                ->whereIn('status', [Visit::STATUS_PROPOSED, Visit::STATUS_COMPLETE])
                ->whereDate('visit_date', '>=', Carbon::today())
                ->orderBy('visit_date')
                ->get();

            // Fetch confirmed visits
            $confirmed_visits = Visit::with(['visitType.place', 'registrations'])
                ->where('status', Visit::STATUS_CONFIRMED)
                ->whereDate('visit_date', '>=', Carbon::today())
                ->orderBy('visit_date')
                ->get();

        } catch (\Exception $e) {
            // Log error
            Log::error("Error fetching visits for home page: " . $e->getMessage());
            $error_message = "Sorry, we couldn't retrieve the tour list at this time.";
        }

        // Pass data to the view
        return view('home', [
            'proposed_visits' => $proposed_visits,
            'confirmed_visits' => $confirmed_visits,
            'error_message' => $error_message // Pass error message if any
        ]);
    }
}
