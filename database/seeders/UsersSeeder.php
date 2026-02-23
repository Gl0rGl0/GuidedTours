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
        $password = Hash::make('password123');

        $configAdmin = User::updateOrCreate(
            ['username' => 'config_admin'],
            [
                'password' => $password,
                'first_name' => 'Config',
                'last_name' => 'Admin',
                'email' => 'admin@example.com'
            ] 
        );
        $configAdmin->assignRole('Admin');

        $configManager = User::updateOrCreate(
            ['username' => 'config_manager'],
            [
                'password' => $password,
                'first_name' => 'Config',
                'last_name' => 'Manager',
                'email' => 'manager@example.com'
            ] 
        );
        $configManager->assignRole('Admin');

        // Volunteers
        $volunteerAnna = User::updateOrCreate(
            ['username' => 'volunteer_anna'],
            [
                'password' => $password,
                'first_name' => 'Anna',
                'last_name' => 'Volunteer',
                'email' => 'anna@example.com'
            ] 
        );
        $volunteerAnna->assignRole('Guide');

        $volunteerMarco = User::updateOrCreate(
            ['username' => 'volunteer_marco'],
            [
                'password' => $password,
                'first_name' => 'Marco',
                'last_name' => 'Volunteer',
                'email' => 'marco@example.com'
            ] 
        );
        $volunteerMarco->assignRole('Guide');

        // Fruitori
        $userPaolo = User::updateOrCreate(
            ['username' => 'user_paolo'],
            [
                'password' => $password,
                'first_name' => 'Paolo',
                'last_name' => 'User',
                'email' => 'paolo@example.com'
            ] 
        );
        $userPaolo->assignRole('Customer');

        $userElena = User::updateOrCreate(
            ['username' => 'user_elena'],
            [
                'password' => $password,
                'first_name' => 'Elena',
                'last_name' => 'User',
                'email' => 'elena@example.com'
            ] 
        );
        $userElena->assignRole('Customer');

        $userLuca = User::updateOrCreate(
            ['username' => 'user_luca'],
            [
                'password' => $password,
                'first_name' => 'Luca',
                'last_name' => 'User',
                'email' => 'luca@example.com'
            ] 
        );
        $userLuca->assignRole('Customer');
    }
}
