<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting; // Import the Setting model

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['setting_key' => 'territorial_scope'],
            [
                'setting_value' => 'Comune di Trento e dintorni',
                'description' => 'Geographical area covered by the organization'
            ]
        );

        Setting::updateOrCreate(
            ['setting_key' => 'max_registration_size'],
            [
                'setting_value' => '4',
                'description' => 'Max people per single user registration'
            ]
        );
    }
}
