<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Models\VolunteerAvailability; // Import VolunteerAvailability model
use App\Models\Visit; // Import Visit model
use App\Models\VisitType; // Import VisitType model
use App\Models\User; // Import User model (for volunteers)

class VisitPlanningController extends Controller
{
    /**
     * Show the visit planning interface for configurators.
     */
    public function index(): View
    {
        // Determine the date range for planning (current and next month)
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonth()->endOfMonth();

        // Fetch volunteer availability within the date range
        $volunteerAvailabilities = VolunteerAvailability::with('volunteer') // Eager load the volunteer relationship
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get()
            ->groupBy('date'); // Group availability by date

        // Fetch planned visits within the date range
        $plannedVisits = Visit::with(['visitType.place', 'assignedVolunteer']) // Eager load relationships
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->orderBy('visit_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('visit_date'); // Group visits by date

        // Fetch all volunteers (users with the 'volunteer' role)
        $volunteers = User::role('volunteer')->orderBy('username')->get();

        // Fetch all visit types
        $visitTypes = VisitType::orderBy('title')->get();


        return view('admin.visit-planning', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'volunteerAvailabilities' => $volunteerAvailabilities,
            'plannedVisits' => $plannedVisits,
            'volunteers' => $volunteers,
            'visitTypes' => $visitTypes,
        ]);
    }
}
