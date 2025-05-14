<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\VolunteerAvailability;
use Carbon\Carbon;          // Import Carbon for date manipulation

class VolunteerAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        $nextMonth = Carbon::now()->addMonthNoOverflow();
        $daysInNextMonth = $nextMonth->daysInMonth;
        $monthYear = $nextMonth->format('Y-m');

        // Trova tutti gli utenti con ruolo 'volontario'
        $volunteers = User::role('volunteer')->get();

        foreach ($volunteers as $volunteer) {
            // Genera da 3 a 8 giorni di disponibilità casuali nel mese prossimo
            $numAvailabilities = rand(9, 15);
            $availableDays = collect(range(1, $daysInNextMonth))
                                ->shuffle()
                                ->take($numAvailabilities);

            foreach ($availableDays as $day) {
                VolunteerAvailability::create([
                    'user_id' => $volunteer->user_id,
                    'available_date' => $nextMonth->copy()->day($day)->toDateString(),
                    'month_year' => $monthYear,
                ]);
            }
        }
    }
}