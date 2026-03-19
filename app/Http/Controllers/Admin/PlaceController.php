<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use App\Models\Place;
use App\Http\Requests\StorePlaceRequest; 
use App\Http\Requests\UpdatePlaceRequest; 
use App\Http\Controllers\Traits\HandlesAdminOperations;

class PlaceController extends Controller
{
    use HandlesAdminOperations;

    public function removePlace(string $locale, Place $place): RedirectResponse
    {
        return $this->handleAdminOperation(
            function () use ($place) {
                $place->delete(); // Effetto a cascata causa vincoli del DB
                Cache::forget('places_list');
            },
            __('messages.admin.places.remove_success'),
            __('messages.admin.places.remove_failed'),
            'admin.configurator'
        );
    }

    public function create(): View
    {
        return view('admin.places.create');
    }

    public function store(StorePlaceRequest $request): RedirectResponse
    {
        return $this->handleAdminOperation(
            function () use ($request) {
                Place::create($request->validated());
                Cache::forget('places_list');
            },
            __('messages.admin.places.create_success'),
            __('messages.admin.places.create_failed'),
            'admin.configurator'
        );
    }

    public function edit(string $locale, Place $place): View
    {
        return view('admin.places.edit', ['place' => $place]);
    }

    public function update(string $locale, UpdatePlaceRequest $request, Place $place): RedirectResponse
    {
         return $this->handleAdminOperation(
            function () use ($request, $place) {
                $place->update($request->validated());
                Cache::forget('places_list');
            },
            __('messages.admin.places.update_success'),
            __('messages.admin.places.update_failed'),
            'admin.configurator'
        );
    }
}
