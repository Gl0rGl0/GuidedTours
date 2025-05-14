<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log; 
use App\Models\Place; 
use App\Models\VisitType; 
use App\Http\Controllers\Traits\HandlesAdminOperations; 

class AdminController extends Controller
{
    use HandlesAdminOperations;

    /**
     * Show the main admin configurator page.
     */
    public function showConfigurator(): View
    {
        $fetch_error = null;
        $places = collect();
        $visit_types = collect();
        $users_by_role = [
            'configurator' => collect(),
            'volunteer' => collect(),
            'fruitore' => collect(),
        ];

        try {
            // Fetch Places
            $places = Place::orderBy('name')->get(['place_id', 'name']);

            // Fetch Visit Types
            $visit_types = VisitType::orderBy('title')->get(['visit_type_id', 'title']);

            // Fetch Users
            $all_users = User::orderBy('username')->get(['user_id', 'username']);

            // Group users by their roles
            foreach ($all_users as $user) {
                // Assign user to the collection of their primary role for display
                if ($user->hasRole('configurator')) {
                     $users_by_role['configurator']->push($user);
                } elseif ($user->hasRole('volunteer')) {
                    $users_by_role['volunteer']->push($user);
                } elseif ($user->hasRole('fruitore')) {
                    $users_by_role['fruitore']->push($user);
                }
                // Users with no assigned role will not appear in these lists
            }

        } catch (\Exception $e) {
            // Log error
            Log::error("Admin Configurator Fetch Error: " . $e->getMessage());
            $fetch_error = "An error occurred while fetching data for the admin panel.";
            // Flash error to session to display in view
            session()->flash('error', $fetch_error);

            // ReInitialize empty collections on error to prevent view errors
            $users_by_role = [
                'configurator' => collect(),
                'volunteer' => collect(),
                'fruitore' => collect(),
            ];
        }

        // Pass all fetched data (or empty collections on error) to the view
        return view('admin.configurator', [
            'places' => $places,
            'visit_types' => $visit_types,
            'users_by_role' => $users_by_role
        ]);
    }

    /**
     * Handle the request to add a new user (by admin).
     */
    public function addUser(StoreUserRequest $request): RedirectResponse
    {
        // Authorization check (handled by middleware and Form Request authorize method)

        // Validation for username and password handled by StoreUserRequest.
        // Add validation for the role field here.
        $request->validate([
            'role' => ['required', \Illuminate\Validation\Rule::in(['configurator', 'volunteer'])],
        ]);

        return $this->handleAdminOperation(
            function () use ($request) {
                $user = User::create([
                    'username' => $request->username,
                    'password' => Hash::make($request->password)
                ]);

                // Assign the validated role using Spatie
                $user->assignRole($request->role);
            },
            'User added successfully!',
            'Failed to add user.',
            'admin.configurator'
        );
    }

    /**
     * Handle the request to remove a user (by admin).
     * Uses Route Model Binding for the $user parameter.
     */
    public function removeUser(User $user): RedirectResponse
    {
        // Prevent removing self or other configurators
        if ($user->user_id === Auth::id()) {
             return back()->withErrors(['general' => 'You cannot remove yourself.']);
        }
        // Prevent removing other configurators using Spatie's hasRole()
        if ($user->hasRole('configurator')) {
             return back()->withErrors(['general' => 'Configurator users cannot be removed.']);
        }

        return $this->handleAdminOperation(
            function () use ($user) {
                $user->delete();
            },
            'User removed successfully!',
            'Failed to remove user.',
            'admin.configurator'
        );
    }
}
