<?php

namespace App\Http\Controllers;

use Illuminate\View\View; 
use Carbon\Carbon; 
use App\Models\Visit; 
use Illuminate\Support\Facades\Log; 

class HomeController extends Controller
{
    public function index(): View
    {
        $proposed_visits = collect();
        $confirmed_visits = collect();
        $error_message = null;

        try {
            $proposed_visits = Visit::with(['visitType.place', 'registrations'])
                ->whereIn('status', [Visit::STATUS_PROPOSED, Visit::STATUS_COMPLETE])
                ->whereDate('visit_date', '>=', Carbon::today())
                ->orderBy('visit_date')
                ->get();

            $confirmed_visits = Visit::with(['visitType.place', 'registrations'])
                ->where('status', Visit::STATUS_CONFIRMED)
                ->whereDate('visit_date', '>=', Carbon::today())
                ->orderBy('visit_date')
                ->get();

        } catch (\Exception $e) {
            Log::error("Error fetching visits for home page: " . $e->getMessage());
            $error_message = "Sorry, we couldn't retrieve the tour list at this time.";
        }

        return view('home', [
            'proposed_visits' => $proposed_visits,
            'confirmed_visits' => $confirmed_visits,
            'error_message' => $error_message
        ]);
    }

    public function terms(): View
    {
        return view('footer.terms');
    }
    
    public function careers(): View
    {
        return view('footer.careers');
    }
}
