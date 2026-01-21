<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreVisitRequest;
use App\Models\Visit;
use App\Models\VisitType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Http\Controllers\Traits\HandlesAdminOperations;
use App\Models\VolunteerAvailability;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    use HandlesAdminOperations;

    public function create(): View
    {
        $startOfNextMonth = Carbon::now()->startOfMonth()->addMonth();
        $endOfNextMonth = Carbon::now()->startOfMonth()->addMonths(2)->subDay();

        $visitTypes = VisitType::where(function ($query) use ($startOfNextMonth, $endOfNextMonth) {
            $query->whereBetween('period_start', [$startOfNextMonth, $endOfNextMonth])
                ->orWhereBetween('period_end', [$startOfNextMonth, $endOfNextMonth])
                ->orWhere(function ($q) use ($startOfNextMonth, $endOfNextMonth) {
                    $q->where('period_start', '<', $startOfNextMonth)
                        ->where('period_end', '>', $endOfNextMonth);
                });
        })->get();

        return view('admin.visits.create', compact('visitTypes'));
    }

    public function store(StoreVisitRequest $request): RedirectResponse
    {
        return $this->handleAdminOperation(
            function () use ($request) {
                Visit::create($request->validated());
            },
            'Visit created successfully!',
            'Failed to create visit.',
            'admin.configurator'
        );
    }

    public function getAvailableVolunteers(Request $request)
    {
        $request->validate([
            'visit_date' => ['required', 'date'],
        ]);

        $visitDate = \Carbon\Carbon::parse($request->visit_date)->toDateString();

        $volunteers = User::role('Guide')
            ->whereHas('volunteerAvailabilities', function ($query) use ($visitDate) {
                $query->whereDate('available_date', $visitDate);
            })
            ->select('user_id', 'username')
            ->get();
            
        return response()->json($volunteers);
    }

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

        $volunteers = User::role('Guide')->orderBy('username')->get();

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

    public function showvisits(): View
    {
        $visits = Visit::with(['visitType.place', 'assignedVolunteer', 'registrations'])
            ->whereIn('status', [Visit::STATUS_CANCELLED, Visit::STATUS_EFFECTED])
            ->orderBy('visit_date', 'desc')
            ->get();

        return view('tours.customized_visits', [
            'visits' => $visits,
        ]);
    }

    public function showAssignedVisits(): View
    {
        $visits = Visit::with(['visitType.place', 'assignedVolunteer', 'registrations'])
            ->whereIn('status', [Visit::STATUS_PROPOSED, Visit::STATUS_COMPLETE])
            ->where('assigned_volunteer_id', Auth::user()->user_id)
            ->orderBy('visit_date', 'desc')
            ->get();

        return view('tours.customized_visits', [
            'visits' => $visits,
        ]);
    }

    public function showMyPastVisits(): View
    {
        $userId = Auth::user()->user_id;

        $visits = Visit::with(['visitType.place', 'assignedVolunteer', 'registrations'])
            ->whereIn('status', [Visit::STATUS_CANCELLED, Visit::STATUS_EFFECTED])
            ->whereHas('registrations', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orderBy('visit_date', 'desc')
            ->get();

        return view('tours.customized_visits', [
            'visits' => $visits,
        ]);
    }
}
