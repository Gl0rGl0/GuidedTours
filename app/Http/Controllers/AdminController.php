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

    public function showConfigurator(): View
    {
        $fetch_error = null;
        $places = collect();
        $visit_types = collect();
        $users_by_role = [
            'Admin' => collect(),
            'Guide' => collect(),
            'Customer' => collect(),
        ];

        try {
            $places = Place::orderBy('name')->get();


            $visit_types = VisitType::with('place')
                ->orderBy('title')
                ->get();

            // Fetch Users
            $all_users = User::orderBy('email')->get(['user_id', 'email', 'first_name', 'last_name']);

            foreach ($all_users as $user) {
                if ($user->hasRole('Admin')) {
                     $users_by_role['Admin']->push($user);
                } elseif ($user->hasRole('Guide')) {
                    $users_by_role['Guide']->push($user);
                } elseif ($user->hasRole('Customer')) {
                    $users_by_role['Customer']->push($user);
                }
            }
            // Fetch Monthly User Stats (Last 6 Months)
            $monthlyStats = collect();
            for ($i = 6; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subMonths($i);
                $monthName = $date->format('F');
                $year = $date->format('Y');
                
                $count = User::role('Customer')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                    
                $monthlyStats->push([
                    'month' => $monthName,
                    'count' => $count
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Admin Configurator Fetch Error: " . $e->getMessage());
            $fetch_error = "An error occurred while fetching data for the admin panel.";
            session()->flash('error', $fetch_error);

            $users_by_role = [
                'Admin' => collect(),
                'Guide' => collect(),
                'Customer' => collect(),
            ];
            $monthlyStats = collect(); // Empty stats on error
        }

        return view('admin.configurator', [
            'places' => $places,
            'visit_types' => $visit_types,
            'users_by_role' => $users_by_role,
            'monthlyStats' => $monthlyStats
        ]);
    }

    public function addUser(StoreUserRequest $request): RedirectResponse
    {
        $request->validate([
            'role' => ['required', \Illuminate\Validation\Rule::in(['Admin', 'Guide'])],
        ]);

        return $this->handleAdminOperation(
            function () use ($request) {
                $user = User::create([
                    'email' => $request->email,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
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

    public function removeUser(User $user): RedirectResponse
    {
        // Prevent removing self or other configurators
        if ($user->user_id === Auth::id()) {
             return back()->withErrors(['general' => 'You cannot remove yourself.']);
        }
        // Prevent removing other Admins using Spatie's hasRole()
        if ($user->hasRole('Admin')) {
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

    public function enhanceContent(\Illuminate\Http\Request $request, \App\Services\AiContentService $aiService): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'title' => 'required|string',
            'location' => 'required|string',
        ]);

        $enhanced = $aiService->enhance($request->title, $request->location);

        return response()->json(['description' => $enhanced]);
    }
}
