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
        $faker = \Faker\Factory::create('it_IT');

        $getRandomDates = function () use ($faker) {
            $createdAt = $faker->dateTimeBetween('-6 months', 'now');
            return [
                'birth_date' => $faker->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        };

        $configAdmin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Config',
                'last_name' => 'Admin'
            ], $getRandomDates())
        );
        $configAdmin->assignRole('Admin');

        $configManager = User::updateOrCreate(
            ['email' => 'manager@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Config',
                'last_name' => 'Manager'
            ], $getRandomDates())
        );
        $configManager->assignRole('Admin');

        // Volunteers
        $volunteerAnna = User::updateOrCreate(
            ['email' => 'anna@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Anna',
                'last_name' => 'Volunteer'
            ], $getRandomDates())
        );
        $volunteerAnna->assignRole('Guide');

        $volunteerMarco = User::updateOrCreate(
            ['email' => 'marco@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Marco',
                'last_name' => 'Volunteer'
            ], $getRandomDates())
        );
        $volunteerMarco->assignRole('Guide');

        // Fruitori
        $userPaolo = User::updateOrCreate(
            ['email' => 'paolo@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Paolo',
                'last_name' => 'User'
            ], $getRandomDates())
        );
        $userPaolo->assignRole('Customer');

        $userElena = User::updateOrCreate(
            ['email' => 'elena@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Elena',
                'last_name' => 'User'
            ], $getRandomDates())
        );
        $userElena->assignRole('Customer');

        $userLuca = User::updateOrCreate(
            ['email' => 'luca@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Luca',
                'last_name' => 'User'
            ], $getRandomDates())
        );
        $userLuca->assignRole('Customer');

        $userGiorgio = User::updateOrCreate(
            ['email' => 'g.felappi004@studenti.unibs.it'],
            array_merge([
                'password' => $password,
                'first_name' => 'Giorgio',
                'last_name' => 'Felappi'
            ], $getRandomDates())
        );
        $userGiorgio->assignRole('Customer');

        // Generate 10 extra Guides
        for ($i = 0; $i < 10; $i++) {
            $guide = User::create(array_merge([
                'email' => $faker->unique()->safeEmail,
                'password' => $password,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
            ], $getRandomDates()));
            $guide->assignRole('Guide');
        }

        // Generate 20 extra Customers
        for ($i = 0; $i < 20; $i++) {
            $customer = User::create(array_merge([
                'email' => $faker->unique()->safeEmail,
                'password' => $password,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
            ], $getRandomDates()));
            $customer->assignRole('Customer');
        }
    }
}
