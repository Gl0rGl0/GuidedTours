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
        $visitTypes = VisitType::with('volunteers')->get()->where('price', '!=', 10.00);

        if ($visitTypes->isEmpty()) {
            $this->command->error('No VisitTypes found. Run VisitTypesSeeder first.');
            return;
        }

        $now = Carbon::now();

        // Generate 20 Past Visits (Effected / Cancelled)
        for ($i = 0; $i < 20; $i++) {
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
                'assigned_volunteer_id' => $volunteer->user_id,
                'status' => $status,
                'status_updated_at' => clone $visitDate, // Pretend it was updated on the day of the visit
            ]);
        }

        // Generate 50 Future Visits (Proposed)
        for ($i = 0; $i < 50; $i++) {
            $vt = $visitTypes->random();
            $volunteers = $vt->volunteers;
            
            if ($volunteers->isEmpty()) continue;

            $volunteer = $volunteers->random();
            $visitDate = $now->copy()->addDays(rand(2, 45));

            Visit::create([
                'visit_type_id' => $vt->visit_type_id,
                'visit_date' => $visitDate->toDateString(),
                'assigned_volunteer_id' => $volunteer->user_id,
                'status' => 'proposed',
                'status_updated_at' => $now,
            ]);
        }

        // Generate 5 Confirmed Visits
        for ($i = 0; $i < 5; $i++) {
            $vt = $visitTypes->random();
            $volunteers = $vt->volunteers;
            
            if ($volunteers->isEmpty()) continue;

            $volunteer = $volunteers->random();
            $visitDate = $now->copy()->addDays(rand(1, 3));

            Visit::create([
                'visit_type_id' => $vt->visit_type_id,
                'visit_date' => $visitDate->toDateString(),
                'assigned_volunteer_id' => $volunteer->user_id,
                'status' => 'confirmed',
                'status_updated_at' => $now,
            ]);
        }

        $volunteer = $vt->volunteers->random();
        $visitDate = $now->copy()->addDays(9);

        $visitaTorre = VisitType::with('volunteers')->get()->where('price', '==', 10.00)->first();
        Visit::create([
            'visit_type_id' => $visitaTorre->visit_type_id,
            'visit_date' => $visitDate->toDateString(),
            'assigned_volunteer_id' => $volunteer->user_id,
            'status' => 'proposed',
            'status_updated_at' => $now,
        ]);
    }
}
