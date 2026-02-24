<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\VolunteerAvailability;
use Carbon\Carbon;

class VolunteerAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing availability to prevent duplicates and ensure clean state
        VolunteerAvailability::query()->delete();

        $volunteers = User::role('Guide')->get();
        
        // Start from 1 month ago and go until end of 2 months from now
        $start = Carbon::now()->subMonth()->startOfMonth();
        $end = Carbon::now()->addMonths(2)->endOfMonth();

        foreach ($volunteers as $volunteer) {
            $current = $start->copy();
            while ($current->lte($end)) {
                // Randomly assign availability (e.g., ~60% chance)
                if (rand(1, 100) <= 60) { 
                    // Check if already exists to avoid dupes if re-running
                    $exists = VolunteerAvailability::where('user_id', $volunteer->user_id)
                        ->where('available_date', $current->toDateString())
                        ->exists();
                    
                    if (!$exists) {
                        VolunteerAvailability::create([
                            'user_id' => $volunteer->user_id,
                            'available_date' => $current->toDateString(),
                            'month_year' => $current->format('Y-m'),
                        ]);
                    }
                }
                $current->addDay();
            }
        }
    }
}