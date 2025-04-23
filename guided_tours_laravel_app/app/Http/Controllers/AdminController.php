<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest; // Import StoreUserRequest
use App\Models\User; // Import User model
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Hash; // Import Hash facade
use Illuminate\Validation\Rules\Password; // Import Password rule
use Illuminate\Validation\Rule; // Import Rule for role validation
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Models\Place; // Import Place model
use App\Models\VisitType; // Import VisitType model
use App\Http\Controllers\Traits\HandlesAdminOperations; // Import the trait

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
        $users_by_role = ['configurator' => collect(), 'volunteer' => collect(), 'fruitore' => collect()];

        try {
            // Fetch Places
            $places = Place::orderBy('name')->get(['place_id', 'name']);

            // Fetch Visit Types
            $visit_types = VisitType::orderBy('title')->get(['visit_type_id', 'title']);

            // Fetch Users and group by role
            $all_users = User::orderBy('role')->orderBy('username')->get(['user_id', 'username', 'role']);
            $users_by_role = $all_users->groupBy('role'); // Group the collection by role

        } catch (\Exception $e) {
            // Log error
            Log::error("Admin Configurator Fetch Error: " . $e->getMessage());
            $fetch_error = "An error occurred while fetching data for the admin panel.";
            // Flash error to session to display in view
            session()->flash('error', $fetch_error);
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

        // Validation is handled by StoreUserRequest

        return $this->handleAdminOperation(
            function () use ($request) {
                User::create([
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                    'first_login' => false, // Or true if they should change password
                ]);
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
        // Authorization check (already handled by middleware in routes/web.php)

        // Prevent removing self or other configurators
        if ($user->user_id === Auth::id()) {
             return back()->withErrors(['general' => 'You cannot remove yourself.']);
        }
        if ($user->role === 'configurator') {
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
