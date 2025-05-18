<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Place;
use App\Http\Requests\StorePlaceRequest; 
use App\Http\Requests\UpdatePlaceRequest; 
use App\Http\Controllers\Traits\HandlesAdminOperations;

class PlaceController extends Controller
{
    use HandlesAdminOperations;

    /**
     * Handle the request to remove a place (by admin).
     */
    public function removePlace(Place $place): RedirectResponse
    {
        return $this->handleAdminOperation(
            function () use ($place) {
                $place->delete(); // Deleting a place will cascade delete related visit types due to DB constraint
            },
            'Place and associated visit types removed successfully!',
            'Failed to remove place.',
            'admin.configurator'
        );
    }

    /**
     * Show the form for creating a new place.
     */
    public function create(): View
    {
        // We'll create this view next
        return view('admin.places.create');
    }

    /**
     * Store a newly created place in storage.
     */
    public function store(StorePlaceRequest $request): RedirectResponse
    {
        return $this->handleAdminOperation(
            function () use ($request) {
                Place::create($request->validated());
            },
            'Place created successfully!',
            'Failed to create place.',
            'admin.configurator'
        );
    }

     /**
     * Show the form for editing the specified place.
     */
    public function edit(Place $place): View
    {
        return view('admin.places.edit', ['place' => $place]);
    }

    /**
     * Update the specified place in storage.
     */
    public function update(UpdatePlaceRequest $request, Place $place): RedirectResponse
    {
         return $this->handleAdminOperation(
            function () use ($request, $place) {
                $place->update($request->validated());
            },
            'Place updated successfully!',
            'Failed to update place.',
            'admin.configurator'
        );
    }
}
