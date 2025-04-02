<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB facade for the query
use Illuminate\View\View; // Import View
use Carbon\Carbon; // Import Carbon

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
            // Fetch proposed and confirmed visits using Query Builder
            // Replicating the original SQL logic
            $available_tours = DB::table('visits as v')
                ->join('visit_types as vt', 'v.visit_type_id', '=', 'vt.visit_type_id')
                ->join('places as p', 'vt.place_id', '=', 'p.place_id')
                ->select(
                    'v.visit_id',
                    'v.visit_date',
                    'v.status',
                    'vt.title as visit_type_title',
                    'vt.description as visit_type_description',
                    'vt.meeting_point',
                    'vt.start_time',
                    'vt.duration_minutes',
                    'vt.requires_ticket',
                    'p.name as place_name',
                    'p.location as place_location'
                )
                ->whereIn('v.status', ['proposed', 'confirmed'])
                ->whereDate('v.visit_date', '>=', Carbon::today()) // Use Carbon for today's date
                ->orderBy('v.visit_date')
                ->orderBy('vt.start_time')
                ->get();

        } catch (\Exception $e) {
            // Log error
            // Log::error("Error fetching available tours on home page: " . $e->getMessage());
            $error_message = "Sorry, we couldn't retrieve the tour list at this time.";
        }

        // Pass data to the view
        return view('home', [
            'available_tours' => $available_tours,
            'error_message' => $error_message // Pass error message if any
        ]);
    }
}
