<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\VisitType;

class VolunteersVisitTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guides = User::role('Guide')->get();
        $visitTypes = VisitType::all();

        if ($guides->isEmpty() || $visitTypes->isEmpty()) {
            $this->command->error('Required Users or VisitTypes not found. Run UsersSeeder and VisitTypesSeeder first.');
            return;
        }

        // Keep specific attachments for Anna and Marco just in case they're relied upon specifically
        $volAnna = $guides->where('email', 'anna@example.com')->first();
        $volMarco = $guides->where('email', 'marco@example.com')->first();
        
        $vtStoria = $visitTypes->where('title', 'Storia del Castello')->first();
        $vtEvolutivo = $visitTypes->where('title', 'Percorso Evolutivo MUSE')->first();
        $vtGiardini = $visitTypes->where('title', 'Giardini Segreti')->first();

        if ($volAnna && $vtStoria && $vtEvolutivo) {
            $volAnna->visitTypes()->syncWithoutDetaching([$vtStoria->visit_type_id, $vtEvolutivo->visit_type_id]);
        }
        
        if ($volMarco && $vtStoria && $vtGiardini) {
            $volMarco->visitTypes()->syncWithoutDetaching([$vtStoria->visit_type_id, $vtGiardini->visit_type_id]);
        }

        // For all guides (including Anna and Marco, to give them more variety)
        foreach ($guides as $guide) {
            // Pick a random number of visit types between 2 and 4
            $numVisitTypes = rand(2, 4);
            $randomVisitTypes = $visitTypes->random(min($numVisitTypes, $visitTypes->count()))->pluck('visit_type_id')->toArray();
            
            $guide->visitTypes()->syncWithoutDetaching($randomVisitTypes);
        }
    }
}
