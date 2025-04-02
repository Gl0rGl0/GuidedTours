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

        // Configurators
        User::updateOrCreate(
            ['username' => 'config_admin'],
            ['password' => $password, 'role' => 'configurator', 'first_login' => false]
        );
        User::updateOrCreate(
            ['username' => 'config_manager'],
            ['password' => $password, 'role' => 'configurator', 'first_login' => false]
        );

        // Volunteers
        User::updateOrCreate(
            ['username' => 'volunteer_anna'],
            ['password' => $password, 'role' => 'volunteer', 'first_login' => false]
        );
        User::updateOrCreate(
            ['username' => 'volunteer_marco'],
            ['password' => $password, 'role' => 'volunteer', 'first_login' => false]
        );

        // Fruitori (Users)
        User::updateOrCreate(
            ['username' => 'user_paolo'],
            ['password' => $password, 'role' => 'fruitore', 'first_login' => false]
        );
        User::updateOrCreate(
            ['username' => 'user_elena'],
            ['password' => $password, 'role' => 'fruitore', 'first_login' => false]
        );
        User::updateOrCreate(
            ['username' => 'user_luca'],
            ['password' => $password, 'role' => 'fruitore', 'first_login' => false]
        );
    }
}
