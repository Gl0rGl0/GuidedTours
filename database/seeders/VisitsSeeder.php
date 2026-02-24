<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;        // Import User model
use App\Models\VisitType;   // Import VisitType model
use App\Models\Visit;       // Import Visit model
use Carbon\Carbon;          // Import Carbon for date manipulation

class VisitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $visitTypes = VisitType::with('volunteers')->get();

        if ($visitTypes->isEmpty()) {
            $this->command->error('No VisitTypes found. Run VisitTypesSeeder first.');
            return;
        }

        $now = Carbon::now();

        // Generate 10 Past Visits (Effected / Cancelled)
        for ($i = 0; $i < 10; $i++) {
            $vt = $visitTypes->random();
            $volunteers = $vt->volunteers;
            
            if ($volunteers->isEmpty()) continue;

            $volunteer = $volunteers->random();
            $visitDate = $now->copy()->subDays(rand(1, 30));

            // Ensure availability on that past date (mostly optional for seeding but good practice)
            $isAvailable = \App\Models\VolunteerAvailability::where('user_id', $volunteer->user_id)
                ->where('available_date', $visitDate->toDateString())
                ->exists();
            if (!$isAvailable && rand(1, 10) > 2) continue; // 80% strictly follow availability

            // Randomly pick status
            $status = rand(1, 100) <= 80 ? 'effected' : 'cancelled';

            Visit::create([
                'visit_type_id' => $vt->visit_type_id,
                'visit_date' => $visitDate->toDateString(),
                'start_time' => sprintf('%02d:%02d:00', rand(9, 17), [0, 15, 30, 45][rand(0, 3)]),
                'assigned_volunteer_id' => $volunteer->user_id,
                'status' => $status,
                'status_updated_at' => clone $visitDate, // Pretend it was updated on the day of the visit
            ]);
        }

        // Generate 30 Future Visits (Proposed / Confirmed)
        for ($i = 0; $i < 30; $i++) {
            $vt = $visitTypes->random();
            $volunteers = $vt->volunteers;
            
            if ($volunteers->isEmpty()) continue;

            $volunteer = $volunteers->random();
            $visitDate = $now->copy()->addDays(rand(1, 45));

            Visit::create([
                'visit_type_id' => $vt->visit_type_id,
                'visit_date' => $visitDate->toDateString(),
                'start_time' => sprintf('%02d:%02d:00', rand(9, 17), [0, 15, 30, 45][rand(0, 3)]),
                'assigned_volunteer_id' => $volunteer->user_id,
                'status' => 'proposed',
                'status_updated_at' => $now,
            ]);
        }
    }
}
