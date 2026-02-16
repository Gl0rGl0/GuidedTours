<?php

namespace App\Http\Controllers;

use Illuminate\View\View; 
use Carbon\Carbon; 
use App\Models\Visit; 
use Illuminate\Support\Facades\Log; 

class HomeController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $proposed_visits = collect();
        $confirmed_visits = collect();
        $error_message = null;
        $places = \App\Models\Place::orderBy('name')->get();

        try {
            // Base Query for Proposed Visits
            $query = Visit::with(['visitType.place', 'registrations'])
                ->whereIn('status', [Visit::STATUS_PROPOSED, Visit::STATUS_COMPLETE])
                ->whereDate('visit_date', '>=', Carbon::today());

            // 1. Filter by Search (Title or Description)
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('visitType', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // 2. Filter by Place
            if ($request->filled('place')) {
                $query->whereHas('visitType', function ($q) use ($request) {
                    $q->where('place_id', $request->place);
                });
            }

            // 3. Filter by Price (Free)
            if ($request->filled('price') && $request->price === 'free') {
                $query->whereHas('visitType', function ($q) {
                    $q->where('requires_ticket', false);
                });
            }

            // 4. Sorting
            $sort = $request->input('sort', 'date_asc');
            switch ($sort) {
                case 'alpha_asc':
                    $query->join('visit_types', 'visits.visit_type_id', '=', 'visit_types.visit_type_id')
                          ->orderBy('visit_types.title', 'asc')
                          ->select('visits.*'); // Avoid column collision
                    break;
                case 'popularity':
                    $query->withCount('registrations')
                          ->orderBy('registrations_count', 'desc');
                    break;
                case 'date_desc':
                    $query->orderBy('visit_date', 'desc');
                    break;
                case 'date_asc':
                default:
                    $query->orderBy('visit_date', 'asc');
                    break;
            }

            // 5. Pagination
            $proposed_visits = $query->paginate(9)->withQueryString();

            // Confirmed Visits (Only show if participating)
            // if (\Illuminate\Support\Facades\Auth::check()) {
            //     $confirmed_visits = Visit::with(['visitType.place', 'registrations'])
            //         ->where('status', Visit::STATUS_CONFIRMED)
            //         ->whereDate('visit_date', '>=', Carbon::today())
            //         ->whereHas('registrations', function ($q) {
            //             $q->where('user_id', \Illuminate\Support\Facades\Auth::id());
            //         })
            //         ->orderBy('visit_date')
            //         ->limit(6)
            //         ->get();
            // } else {
            //     $confirmed_visits = collect();
            // }

        } catch (\Exception $e) {
            Log::error("Error fetching visits for home page: " . $e->getMessage());
            $error_message = "Sorry, we couldn't retrieve the tour list at this time.";
        }

        return view('home', [
            'proposed_visits' => $proposed_visits,
            //'confirmed_visits' => $confirmed_visits,
            'places' => $places,
            'error_message' => $error_message
        ]);
    }

    public function terms(): View
    {
        return view('footer.terms');
    }

    public function about(): View
    {
        return view('footer.about');
    }
    
    public function careers(): View
    {
        return view('footer.careers');
    }
}
