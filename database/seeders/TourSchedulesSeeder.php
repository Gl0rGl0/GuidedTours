<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourSchedule;
use App\Models\VisitType;

class TourSchedulesSeeder extends Seeder
{
    public function run(): void
    {
        $vt1 = VisitType::where('title', 'Storia del Castello')->first();
        $vt2 = VisitType::where('title', 'Percorso Evolutivo MUSE')->first();

        if (!$vt1 || !$vt2) {
             $this->command->error('VisitTypes not found. Skipping TourSchedulesSeeder.');
             return;
        }

        // Storia del Castello: Every Monday at 10:00
        TourSchedule::updateOrCreate(
            ['visit_type_id' => $vt1->visit_type_id, 'day_of_week' => 1, 'start_time' => '10:00:00'],
            []
        );

         // Storia del Castello: Every Friday at 14:00
        TourSchedule::updateOrCreate(
            ['visit_type_id' => $vt1->visit_type_id, 'day_of_week' => 5, 'start_time' => '14:00:00'],
            []
        );

        // MUSE: Every Saturday at 11:00
        TourSchedule::updateOrCreate(
             ['visit_type_id' => $vt2->visit_type_id, 'day_of_week' => 6, 'start_time' => '11:00:00'],
             []
        );
        
        // MUSE: Every Tuesday at 11:00
        TourSchedule::updateOrCreate(
             ['visit_type_id' => $vt2->visit_type_id, 'day_of_week' => 2, 'start_time' => '11:00:00'],
             []
        );

    }
}
