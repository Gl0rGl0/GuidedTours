<?php

namespace App\Http\Controllers;

use Illuminate\View\View; 
use Carbon\Carbon; 
use App\Models\Visit; 
use Illuminate\Support\Facades\Log; 

class HomeController extends Controller
{
    /**
     * Show the application dashboard (home page with available tours).
     */
    public function index(): View
    {
        $proposed_visits = collect();
        $confirmed_visits = collect();
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
