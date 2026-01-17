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
            'configurator' => collect(),
            'volunteer' => collect(),
            'fruitore' => collect(),
        ];

        try {
            $places = Place::orderBy('name')->get(['place_id', 'name']);

            $visit_types = VisitType::orderBy('title')->get(['visit_type_id', 'title']);

            // Fetch Users
            $all_users = User::orderBy('username')->get(['user_id', 'username']);

            foreach ($all_users as $user) {
                if ($user->hasRole('configurator')) {
                     $users_by_role['configurator']->push($user);
                } elseif ($user->hasRole('volunteer')) {
                    $users_by_role['volunteer']->push($user);
                } elseif ($user->hasRole('fruitore')) {
                    $users_by_role['fruitore']->push($user);
                }
            }

        } catch (\Exception $e) {
            Log::error("Admin Configurator Fetch Error: " . $e->getMessage());
            $fetch_error = "An error occurred while fetching data for the admin panel.";
            session()->flash('error', $fetch_error);

            $users_by_role = [
                'configurator' => collect(),
                'volunteer' => collect(),
                'fruitore' => collect(),
            ];
        }

        return view('admin.configurator', [
            'places' => $places,
            'visit_types' => $visit_types,
            'users_by_role' => $users_by_role
        ]);
    }

    public function addUser(StoreUserRequest $request): RedirectResponse
    {
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
