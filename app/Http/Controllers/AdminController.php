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
            // OPTIMIZATION: Eager load 'roles' to prevent N+1 queries when checking hasRole
            $all_users = User::with('roles')->orderBy('email')->get(['user_id', 'email', 'first_name', 'last_name']);

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
            // OPTIMIZATION: Use a single aggregated query to fetch counts by month, reducing 14 queries down to 1
            $startDate = \Carbon\Carbon::now()->subMonths(6)->startOfMonth();

            $connection = \Illuminate\Support\Facades\DB::connection()->getDriverName();
            if ($connection === 'sqlite') {
                $selectRaw = 'COUNT(user_id) as count, STRFTIME("%Y", created_at) as year, STRFTIME("%m", created_at) as month';
            } elseif ($connection === 'pgsql') {
                $selectRaw = 'COUNT(user_id) as count, EXTRACT(YEAR FROM created_at) as year, EXTRACT(MONTH FROM created_at) as month';
            } else { // mysql / mariadb
                $selectRaw = 'COUNT(user_id) as count, YEAR(created_at) as year, MONTH(created_at) as month';
            }

            $stats = User::role('Customer')
                ->where('created_at', '>=', $startDate)
                ->selectRaw($selectRaw)
                ->groupBy('year', 'month')
                ->get()
                ->keyBy(function ($item) {
                    return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                });

            $monthlyStats = collect();
            for ($i = 6; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subMonths($i);
                $monthName = $date->translatedFormat('F');
                $key = $date->format('Y-m');
                
                $count = isset($stats[$key]) ? $stats[$key]->count : 0;
                    
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
            __('messages.admin.configurator.users.add_success'),
            __('messages.admin.configurator.users.add_failed'),
            'admin.configurator'
        );
    }

    public function removeUser(User $user): RedirectResponse
    {
        // Prevent removing self or other configurators
        if ($user->user_id === Auth::id()) {
             return back()->withErrors(['general' => __('messages.admin.configurator.users.cannot_remove_self')]);
        }
        // Prevent removing other Admins using Spatie's hasRole()
        if ($user->hasRole('Admin')) {
             return back()->withErrors(['general' => __('messages.admin.configurator.users.cannot_remove_admin')]);
        }

        return $this->handleAdminOperation(
            function () use ($user) {
                $user->delete();
            },
            __('messages.admin.configurator.users.remove_success'),
            __('messages.admin.configurator.users.remove_failed'),
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
