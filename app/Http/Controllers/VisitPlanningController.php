<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Models\VolunteerAvailability;
use App\Models\Visit;
use App\Models\VisitType;
use App\Models\User;

class VisitPlanningController extends Controller
{
    public function index(): View
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonth()->endOfMonth();


        $volunteerAvailabilities = VolunteerAvailability::with('volunteer')
            ->whereBetween('available_date', [$startDate, $endDate])
            ->orderBy('available_date')
            ->get()
            ->groupBy('available_date');


        $plannedVisits = Visit::with(['visitType.place', 'assignedVolunteer'])
            ->whereIn('status', [Visit::STATUS_PROPOSED, Visit::STATUS_COMPLETE])
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->orderBy('visit_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('visit_date');

        $volunteers = User::role('volunteer')->orderBy('username')->get();

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

    /**
     * Display a listing of past (completed or cancelled) visits.
     */
    public function showPastVisits(): View
    {
        $pastVisits = Visit::with(['visitType.place', 'assignedVolunteer', 'registrations'])
            ->whereIn('status', [Visit::STATUS_CANCELLED, Visit::STATUS_EFFECTED])
            ->orderBy('visit_date', 'desc')
            ->get();

        return view('tours.past_visits', [
            'pastVisits' => $pastVisits,
        ]);
    }
}
