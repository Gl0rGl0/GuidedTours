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
            'visit_date' => 'required|date',
        ]);

        $volunteers = User::role('volunteer')
            ->whereHas('volunteerAvailabilities', function ($query) use ($request) {
                $query->where('available_date', $request->visit_date);
            })
            ->select('user_id', 'username')
            ->get();

        return response()->json($volunteers);
    }
}
