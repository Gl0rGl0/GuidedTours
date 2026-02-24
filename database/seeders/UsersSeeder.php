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
            ['email' => 'admin@example.com'],
            [
                'password' => $password,
                'first_name' => 'Config',
                'last_name' => 'Admin'
            ] 
        );
        $configAdmin->assignRole('Admin');

        $configManager = User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'password' => $password,
                'first_name' => 'Config',
                'last_name' => 'Manager'
            ] 
        );
        $configManager->assignRole('Admin');

        // Volunteers
        $volunteerAnna = User::updateOrCreate(
            ['email' => 'anna@example.com'],
            [
                'password' => $password,
                'first_name' => 'Anna',
                'last_name' => 'Volunteer'
            ] 
        );
        $volunteerAnna->assignRole('Guide');

        $volunteerMarco = User::updateOrCreate(
            ['email' => 'marco@example.com'],
            [
                'password' => $password,
                'first_name' => 'Marco',
                'last_name' => 'Volunteer'
            ] 
        );
        $volunteerMarco->assignRole('Guide');

        // Fruitori
        $userPaolo = User::updateOrCreate(
            ['email' => 'paolo@example.com'],
            [
                'password' => $password,
                'first_name' => 'Paolo',
                'last_name' => 'User'
            ] 
        );
        $userPaolo->assignRole('Customer');

        $userElena = User::updateOrCreate(
            ['email' => 'elena@example.com'],
            [
                'password' => $password,
                'first_name' => 'Elena',
                'last_name' => 'User'
            ] 
        );
        $userElena->assignRole('Customer');

        $userLuca = User::updateOrCreate(
            ['email' => 'luca@example.com'],
            [
                'password' => $password,
                'first_name' => 'Luca',
                'last_name' => 'User'
            ] 
        );
        $userLuca->assignRole('Customer');

        $userGiorgio = User::updateOrCreate(
            ['email' => 'g.felappi004@studenti.unibs.it'],
            [
                'password' => $password,
                'first_name' => 'Giorgio',
                'last_name' => 'Felappi'
            ] 
        );
        $userGiorgio->assignRole('Customer');
        // Generate 10 extra Guides
        $faker = \Faker\Factory::create('it_IT');

        for ($i = 0; $i < 10; $i++) {
            $guide = User::create([
                'email' => $faker->unique()->safeEmail,
                'password' => $password,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
            ]);
            $guide->assignRole('Guide');
        }

        // Generate 20 extra Customers
        for ($i = 0; $i < 20; $i++) {
            $customer = User::create([
                'email' => $faker->unique()->safeEmail,
                'password' => $password,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
            ]);
            $customer->assignRole('Customer');
        }
    }
}
