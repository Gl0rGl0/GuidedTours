<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Import User model
use App\Models\VisitType; // Import VisitType model

class VolunteersVisitTypesSeeder extends Seeder
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

        // Attach Visit Types to Volunteers
        // Anna: Castello History, MUSE Evolution
        $vol1->visitTypes()->syncWithoutDetaching([$vt1->visit_type_id, $vt3->visit_type_id]);

        // Marco: Castello History, Gardens
        $vol2->visitTypes()->syncWithoutDetaching([$vt1->visit_type_id, $vt2->visit_type_id]);
    }
}
