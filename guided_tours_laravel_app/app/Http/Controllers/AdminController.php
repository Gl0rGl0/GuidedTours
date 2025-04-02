<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Import User model
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Hash; // Import Hash facade
use Illuminate\Validation\Rules\Password; // Import Password rule
use Illuminate\Validation\Rule; // Import Rule for role validation
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Place; // Import Place model
use App\Models\VisitType; // Import VisitType model

class AdminController extends Controller
{
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
            // Log::error("Admin Configurator Fetch Error: " . $e->getMessage());
            $fetch_error = "An error occurred while fetching data for the admin panel.";
            // Flash error to session to display in view
            session()->flash('error', $fetch_error);
        }

        // Pass all fetched data (or empty collections on error) to the view
        return view('admin.configurator', [
            'places' => $places,
            'visit_types' => $visit_types,
            'users_by_role' => $users_by_role,
            // 'fetch_error' is handled via session flash now
        ]);
    }

    /**
     * Handle the request to add a new user (by admin).
     */
    public function addUser(Request $request): RedirectResponse
    {
        // Authorization check (already handled by middleware in routes/web.php)

        $allowed_roles = ['configurator', 'volunteer', 'fruitore'];

        $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'], // Email wasn't in final index.php logic
            'password' => ['required', 'confirmed', Password::min(6)],
            'role' => ['required', Rule::in($allowed_roles)],
        ]);

        try {
            User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'first_login' => false, // Or true if they should change password
            ]);

            return redirect()->route('admin.configurator')->with('status', 'User added successfully!');

        } catch (\Exception $e) {
            // Log error
            // Log::error("Admin add user failed: " . $e->getMessage());
            return back()->withInput()->withErrors(['username' => 'Failed to add user. Please try again.']);
        }
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

        try {
            $user->delete();
            return redirect()->route('admin.configurator')->with('status', 'User removed successfully!');

        } catch (\Exception $e) {
            // Log error
            // Log::error("Admin remove user failed for user {$user->user_id}: " . $e->getMessage());
            return back()->withErrors(['general' => 'Failed to remove user. Please try again.']);
        }
    }

    /**
     * Handle the request to remove a place (by admin).
     */
    public function removePlace(Place $place): RedirectResponse
    {
        // Authorization check (already handled by middleware)
        try {
            $place->delete(); // Deleting a place will cascade delete related visit types due to DB constraint
            return redirect()->route('admin.configurator')->with('status', 'Place and associated visit types removed successfully!');
        } catch (\Exception $e) {
            // Log error
            // Log::error("Admin remove place failed for place {$place->place_id}: " . $e->getMessage());
            return back()->withErrors(['general' => 'Failed to remove place. Please try again.']);
        }
    }

    /**
     * Handle the request to remove a visit type (by admin).
     */
    public function removeVisitType(VisitType $visit_type): RedirectResponse
    {
        // Authorization check (already handled by middleware)
        try {
            $visit_type->delete();
            return redirect()->route('admin.configurator')->with('status', 'Visit type removed successfully!');
        } catch (\Exception $e) {
            // Log error
            // Log::error("Admin remove visit type failed for visit type {$visit_type->visit_type_id}: " . $e->getMessage());
            return back()->withErrors(['general' => 'Failed to remove visit type. Please try again.']);
        }
    }
}
