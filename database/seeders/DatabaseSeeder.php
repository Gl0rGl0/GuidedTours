<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create(); // Keep default factory calls commented/removed

        // Create Roles first
        Role::create(['name' => 'configurator']);
        Role::create(['name' => 'volunteer']);
        Role::create(['name' => 'fruitore']);

        $this->call([
            SettingsSeeder::class,
            UsersSeeder::class,
            PlacesSeeder::class,
            VisitTypesSeeder::class,
            VolunteersVisitTypesSeeder::class,
            VisitsSeeder::class,
            RegistrationsSeeder::class,
            VolunteerAvailabilitySeeder::class,
        ]);
    }
}
