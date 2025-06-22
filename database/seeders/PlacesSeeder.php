<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Place;

class PlacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Place::updateOrCreate(
            ['name' => 'Castello del Buonconsiglio'],
            [
                'description' => 'Il più vasto e importante complesso monumentale della regione Trentino-Alto Adige.',
                'location' => 'Via Bernardo Clesio, 5, 38122 Trento TN'
            ]
        );

        Place::updateOrCreate(
            ['name' => 'Museo MUSE'],
            [
                'description' => 'Museo delle Scienze di Trento progettato da Renzo Piano.',
                'location' => 'Corso del Lavoro e della Scienza, 3, 38122 Trento TN'
            ]
        );
    }
}
