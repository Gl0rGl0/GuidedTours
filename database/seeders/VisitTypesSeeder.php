<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Place;
use App\Models\VisitType;

class VisitTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $place1 = Place::where('name', 'Castello del Buonconsiglio')->first();
        $place2 = Place::where('name', 'Museo MUSE')->first();

        if (!$place1 || !$place2) {
            $this->command->error('Places not found. Run PlacesSeeder first.');
            return;
        }

        VisitType::updateOrCreate(
            ['place_id' => $place1->place_id, 'title' => 'Storia del Castello'],
            [
                'description' => 'Un viaggio attraverso le epoche del castello, dalle origini medievali al Concilio di Trento.',
                'meeting_point' => 'Ingresso principale del Castello',
                'period_start' => '2025-01-01',
                'period_end' => '2025-12-31',
                'start_time' => '10:00:00',
                'duration_minutes' => 90,
                'requires_ticket' => true,
                'min_participants' => 5,
                'max_participants' => 25
            ]
        );

        VisitType::updateOrCreate(
            ['place_id' => $place1->place_id, 'title' => 'Giardini Segreti'],
            [
                'description' => 'Esplorazione dei giardini e delle logge rinascimentali.',
                'meeting_point' => 'Cortile interno, vicino alla fontana',
                'period_start' => '2025-04-01',
                'period_end' => '2025-10-31',
                'start_time' => '15:00:00',
                'duration_minutes' => 60,
                'requires_ticket' => true,
                'min_participants' => 3,
                'max_participants' => 15
            ]
        );

        VisitType::updateOrCreate(
            ['place_id' => $place2->place_id, 'title' => 'Percorso Evolutivo MUSE'],
            [
                'description' => 'Dalle vette alpine ai segreti della foresta tropicale.',
                'meeting_point' => 'Biglietteria MUSE',
                'period_start' => '2025-01-01',
                'period_end' => '2025-12-31',
                'start_time' => '11:00:00',
                'duration_minutes' => 120,
                'requires_ticket' => true,
                'min_participants' => 8,
                'max_participants' => 30
            ]
        );

        VisitType::updateOrCreate(
            ['place_id' => $place1->place_id, 'title' => 'Architettura del Castello'],
            [
                'description' => 'Analisi degli stili architettonici dal medievale al barocco.',
                'meeting_point' => 'Piazza principale del Castello',
                'period_start' => '2025-01-01',
                'period_end' => '2025-12-31',
                'start_time' => '14:00:00',
                'duration_minutes' => 75,
                'requires_ticket' => true,
                'min_participants' => 4,
                'max_participants' => 20
            ]
        );

        VisitType::updateOrCreate(
            ['place_id' => $place2->place_id, 'title' => 'MUSE per Famiglie'],
            [
                'description' => 'Un tour interattivo pensato per bambini e genitori.',
                'meeting_point' => 'Area Accoglienza MUSE',
                'period_start' => '2025-01-01',
                'period_end' => '2025-12-31',
                'start_time' => '16:00:00',
                'duration_minutes' => 90,
                'requires_ticket' => true,
                'min_participants' => 5,
                'max_participants' => 25
            ]
        );

         VisitType::updateOrCreate(
            ['place_id' => $place1->place_id, 'title' => 'Castello di Sera'],
            [
                'description' => 'Visita suggestiva al tramonto con focus sulle leggende locali.',
                'meeting_point' => 'Ponte levatoio',
                'period_start' => '2025-06-01',
                'period_end' => '2025-09-15',
                'start_time' => '19:00:00',
                'duration_minutes' => 60,
                'requires_ticket' => false,
                'min_participants' => 6,
                'max_participants' => 18
            ]
        );

         VisitType::updateOrCreate(
            ['place_id' => $place2->place_id, 'title' => 'Mostra Temporanea Dinosauri'],
            [
                'description' => 'Approfondimento sulla mostra temporanea dedicata ai dinosauri.',
                'meeting_point' => 'Ingresso Mostra Temporanea, Piano -1',
                'period_start' => '2025-05-01',
                'period_end' => '2025-08-31',
                'start_time' => '15:30:00',
                'duration_minutes' => 75,
                'requires_ticket' => true,
                'min_participants' => 5,
                'max_participants' => 20
            ]
        );
    }
}
