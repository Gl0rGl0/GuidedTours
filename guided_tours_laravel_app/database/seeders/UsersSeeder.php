<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Import User model
use Illuminate\Support\Facades\Hash; // Import Hash facade

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password123'); // Hash the common password once

        // Configurators (Mapped to 'configurator' role)
        $configAdmin = User::updateOrCreate(
            ['username' => 'config_admin'],
            ['password' => $password, 'first_login' => false] // Removed 'role'
        );
        $configAdmin->assignRole('configurator');

        $configManager = User::updateOrCreate(
            ['username' => 'config_manager'],
            ['password' => $password, 'first_login' => false] // Removed 'role'
        );
        $configManager->assignRole('configurator');

        // Volunteers
        $volunteerAnna = User::updateOrCreate(
            ['username' => 'volunteer_anna'],
            ['password' => $password, 'first_login' => false] // Removed 'role'
        );
        $volunteerAnna->assignRole('volunteer');

        $volunteerMarco = User::updateOrCreate(
            ['username' => 'volunteer_marco'],
            ['password' => $password, 'first_login' => false] // Removed 'role'
        );
        $volunteerMarco->assignRole('volunteer');

        // Fruitori (Users)
        $userPaolo = User::updateOrCreate(
            ['username' => 'user_paolo'],
            ['password' => $password, 'first_login' => false] // Removed 'role'
        );
        $userPaolo->assignRole('fruitore');

        $userElena = User::updateOrCreate(
            ['username' => 'user_elena'],
            ['password' => $password, 'first_login' => false] // Removed 'role'
        );
        $userElena->assignRole('fruitore');

        $userLuca = User::updateOrCreate(
            ['username' => 'user_luca'],
            ['password' => $password, 'first_login' => false] // Removed 'role'
        );
        $userLuca->assignRole('fruitore');
    }
}
