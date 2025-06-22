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
        // Get Volunteers
        $vol1 = User::where('username', 'volunteer_anna')->first();
        $vol2 = User::where('username', 'volunteer_marco')->first();

        // Get Visit Types
        $vt1 = VisitType::where('title', 'Storia del Castello')->first();
        $vt2 = VisitType::where('title', 'Giardini Segreti')->first();
        $vt3 = VisitType::where('title', 'Percorso Evolutivo MUSE')->first();

        if (!$vol1 || !$vol2 || !$vt1 || !$vt2 || !$vt3) {
            $this->command->error('Required Users or VisitTypes not found. Run UsersSeeder and VisitTypesSeeder first.');
            return;
        }

        $now = Carbon::now();

        // Proposed
        Visit::updateOrCreate(
            ['visit_type_id' => $vt1->visit_type_id, 'visit_date' => $now->copy()->addDays(10)->toDateString()],
            ['assigned_volunteer_id' => $vol1->user_id, 'status' => 'proposed']
        );
        Visit::updateOrCreate(
            ['visit_type_id' => $vt2->visit_type_id, 'visit_date' => $now->copy()->addDays(12)->toDateString()],
            ['assigned_volunteer_id' => $vol2->user_id, 'status' => 'proposed']
        );
        
        // Visit::updateOrCreate(
        //     ['visit_type_id' => $vt3->visit_type_id, 'visit_date' => $now->copy()->addDays(14)->toDateString()],
        //     ['assigned_volunteer_id' => $vol1->user_id, 'status' => 'proposed']
        // );
        // Visit::updateOrCreate(
        //     ['visit_type_id' => $vt1->visit_type_id, 'visit_date' => $now->copy()->addDays(17)->toDateString()],
        //     ['assigned_volunteer_id' => $vol2->user_id, 'status' => 'proposed']
        // );
        //  Visit::updateOrCreate(
        //     ['visit_type_id' => $vt3->visit_type_id, 'visit_date' => $now->copy()->addDays(20)->toDateString()],
        //     ['assigned_volunteer_id' => $vol1->user_id, 'status' => 'proposed']
        // );


        // Confirmed
        Visit::updateOrCreate(
            ['visit_type_id' => $vt1->visit_type_id, 'visit_date' => $now->copy()->addDays(4)->toDateString()],
            [
                'assigned_volunteer_id' => $vol2->user_id,
                'status' => 'confirmed',
                'status_updated_at' => $now->copy()->subDay() // Set status update time
            ]
        );
        Visit::updateOrCreate(
            ['visit_type_id' => $vt3->visit_type_id, 'visit_date' => $now->copy()->addDays(5)->toDateString()],
            [
                'assigned_volunteer_id' => $vol1->user_id,
                'status' => 'confirmed',
                'status_updated_at' => $now->copy()->subDays(2) // Set status update time
            ]
        );

        // Cancelled
        Visit::updateOrCreate(
            ['visit_type_id' => $vt2->visit_type_id, 'visit_date' => $now->copy()->subDays(6)->toDateString()],
            [
                'assigned_volunteer_id' => $vol2->user_id,
                'status' => 'cancelled',
                'status_updated_at' => $now->copy()->subDays(3) // Set status update time
            ]
        );

        // Effected
        // Cancelled
        Visit::updateOrCreate(
            ['visit_type_id' => $vt1->visit_type_id, 'visit_date' => $now->copy()->subDays(7)->toDateString()],
            [
                'assigned_volunteer_id' => $vol1->user_id,
                'status' => 'effected',
                'status_updated_at' => $now->copy()->subDays(3) // Set status update time
            ]
        );
    }
}
