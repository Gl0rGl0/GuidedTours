<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourSchedule;
use App\Models\VisitType;

class TourSchedulesSeeder extends Seeder
{
    public function run(): void
    {
        $visitTypes = VisitType::all();

        if ($visitTypes->isEmpty()) {
             $this->command->error('VisitTypes not found. Skipping TourSchedulesSeeder.');
             return;
        }

        $commonStartTimes = ['09:30:00', '10:00:00', '11:00:00', '14:00:00', '15:30:00', '16:00:00', '17:30:00'];

        foreach ($visitTypes as $vt) {
            // Generate between 1 and 3 schedules per visit type
            $numSchedules = rand(1, 4);
            $daysUsed = []; // Track days to prevent multiple schedules on the same day for the same VT (optional, but cleaner)

            for ($i = 0; $i < $numSchedules; $i++) {
                $dayOfWeek = rand(1, 7); // 1 = Monday, 7 = Sunday
                
                // Keep trying if day already used for this VT
                while (in_array($dayOfWeek, $daysUsed)) {
                    $dayOfWeek = rand(1, 7);
                }
                $daysUsed[] = $dayOfWeek;

                $startTime = $commonStartTimes[array_rand($commonStartTimes)];

                TourSchedule::updateOrCreate(
                    ['visit_type_id' => $vt->visit_type_id, 'day_of_week' => $dayOfWeek, 'start_time' => $startTime],
                    []
                );
            }
        }
    }
}
