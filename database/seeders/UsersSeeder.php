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
            ['username' => 'admin@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Config',
                'last_name' => 'configurator'
            ], $getRandomDates())
        );
        $configAdmin->assignRole('configurator');

        $configManager = User::updateOrCreate(
            ['username' => 'manager@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Config',
                'last_name' => 'Manager'
            ], $getRandomDates())
        );
        $configManager->assignRole('configurator');

        // Volunteers
        $volunteerAnna = User::updateOrCreate(
            ['username' => 'anna@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Anna',
                'last_name' => 'Volunteer'
            ], $getRandomDates())
        );
        $volunteerAnna->assignRole('volunteer');

        $volunteerMarco = User::updateOrCreate(
            ['username' => 'marco@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Marco',
                'last_name' => 'Volunteer'
            ], $getRandomDates())
        );
        $volunteerMarco->assignRole('volunteer');

        // Fruitori
        $userPaolo = User::updateOrCreate(
            ['username' => 'paolo@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Paolo',
                'last_name' => 'User'
            ], $getRandomDates())
        );
        $userPaolo->assignRole('fruitore');

        $userElena = User::updateOrCreate(
            ['username' => 'elena@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Elena',
                'last_name' => 'User'
            ], $getRandomDates())
        );
        $userElena->assignRole('fruitore');

        $userLuca = User::updateOrCreate(
            ['username' => 'luca@example.com'],
            array_merge([
                'password' => $password,
                'first_name' => 'Luca',
                'last_name' => 'User'
            ], $getRandomDates())
        );
        $userLuca->assignRole('fruitore');

        $userGiorgio = User::updateOrCreate(
            ['username' => 'g.felappi004@studenti.unibs.it'],
            array_merge([
                'password' => $password,
                'first_name' => 'Giorgio',
                'last_name' => 'Felappi'
            ], $getRandomDates())
        );
        $userGiorgio->assignRole('fruitore');

        // Generate 10 extra Guides
        for ($i = 0; $i < 10; $i++) {
            $guide = User::create(array_merge([
                'username' => $faker->unique()->safeEmail(),
                'password' => $password,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
            ], $getRandomDates()));
            $guide->assignRole('volunteer');
        }

        // Generate 20 extra Customers
        for ($i = 0; $i < 20; $i++) {
            $customer = User::create(array_merge([
                'username' => $faker->unique()->safeEmail(),
                'password' => $password,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
            ], $getRandomDates()));
            $customer->assignRole('fruitore');
        }
    }
}
