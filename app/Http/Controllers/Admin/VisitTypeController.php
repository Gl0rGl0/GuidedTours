<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Place;
use App\Models\VisitType;
use App\Http\Requests\StoreVisitTypeRequest; 
use App\Http\Requests\UpdateVisitTypeRequest; 
use App\Http\Controllers\Traits\HandlesAdminOperations;

class VisitTypeController extends Controller
{
    use HandlesAdminOperations;

    /**
     * Handle the request to remove a visit type (by admin).
     */
    public function removeVisitType(VisitType $visit_type): RedirectResponse
    {
        return $this->handleAdminOperation(
            function () use ($visit_type) {
                $visit_type->delete();
            },
            'Visit type removed successfully!',
            'Failed to remove visit type.',
            'admin.configurator'
        );
    }

    /**
     * Show the form for creating a new visit type.
     */
    public function create(): View
    {
        $places = Place::orderBy('name')->pluck('name', 'place_id');
        return view('admin.visit-types.create', ['places' => $places]);
    }

    /**
     * Store a newly created visit type in storage.
     */
    public function store(StoreVisitTypeRequest $request): RedirectResponse
    {
        return $this->handleAdminOperation(
            function () use ($request) {
                VisitType::create($request->validated());
            },
            'Visit Type created successfully!',
            'Failed to create visit type.',
            'admin.configurator'
        );
    }

    /**
     * Show the form for editing the specified visit type.
     */
    public function edit(VisitType $visit_type): View
    {
        $places = Place::orderBy('name')->pluck('name', 'place_id');
        return view('admin.visit-types.edit', [
            'visit_type' => $visit_type,
            'places' => $places
        ]);
    }

    /**
     * Update the specified visit type in storage.
     */
    public function update(UpdateVisitTypeRequest $request, VisitType $visit_type): RedirectResponse
    {
         return $this->handleAdminOperation(
            function () use ($request, $visit_type) {
                $visit_type->update($request->validated());
            },
            'Visit Type updated successfully!',
            'Failed to update visit type.',
            'admin.configurator'
        );
    }
}
