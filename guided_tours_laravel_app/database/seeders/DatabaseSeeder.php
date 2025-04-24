<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role; // Import Role model

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create(); // Keep default factory calls commented/removed

        // Create Roles first
        Role::create(['name' => 'configurator']); // Add the configurator role
        Role::create(['name' => 'volunteer']);
        Role::create(['name' => 'fruitore']); // Assuming 'fruitore' is the standard user role

        $this->call([
            SettingsSeeder::class,
            UsersSeeder::class, // Users must exist before being referenced, now roles also exist
            PlacesSeeder::class, // Places must exist before VisitTypes
            VisitTypesSeeder::class, // VisitTypes must exist before Visits/Pivot
            VolunteersVisitTypesSeeder::class, // Pivot table links Users and VisitTypes
            VisitsSeeder::class, // Visits reference Users and VisitTypes
            RegistrationsSeeder::class, // Registrations reference Users and Visits
        ]);
    }
}
