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
            ->whereIn('status', [Visit::STATUS_COMPLETE, Visit::STATUS_CANCELLED, Visit::STATUS_EFFECTED]) // Including 'effected' for now
            ->orderBy('visit_date', 'desc')
            // ->orderBy('visitType.start_time', 'desc') // Consider if start_time is available and needed for sorting
            ->get();

        // You might want to group them by status or year/month for better presentation
        // For example:
        // $groupedPastVisits = $pastVisits->groupBy('status');

        return view('tours.past_visits', [ // Assuming the view will be at resources/views/tours/past_visits.blade.php
            'pastVisits' => $pastVisits,
            // 'groupedPastVisits' => $groupedPastVisits,
        ]);
    }
}
