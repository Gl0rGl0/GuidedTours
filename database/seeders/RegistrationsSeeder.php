<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Visit;
use App\Models\Registration;

class RegistrationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::role('Customer')->get();
        $visits = Visit::with('visitType')->get();

        if ($customers->isEmpty() || $visits->isEmpty()) {
            $this->command->warn('Could not find enough Users (role: Customer) or Visits to seed registrations. Skipping.');
            return;
        }

        $generateBookingCode = function($visitId, $userId) {
            return 'BK' . str_pad($visitId, 4, '0', STR_PAD_LEFT) . 'U' . str_pad($userId, 3, '0', STR_PAD_LEFT) . \Illuminate\Support\Str::random(4);
        };

        foreach ($visits as $visit) {
            $vt = $visit->visitType;
            $min = $vt->min_participants;
            $max = $visit->max_capacity ?? $vt->max_participants;
            
            $targetParticipants = 0;

            if ($visit->status === 'effected' || $visit->status === 'confirmed') {
                // Must have at least min_participants
                $targetParticipants = rand($min, $max);
            } elseif ($visit->status === 'cancelled') {
                // Must have strictly less than min_participants (or no participants)
                $targetParticipants = rand(0, max(0, $min - 1));
            } elseif ($visit->status === 'proposed') {
                // Anything goes (0 to max)
                $targetParticipants = rand(0, $max);
            }

            if ($targetParticipants === 0) continue;

            $currentParticipants = 0;
            // Shuffle customers so different people join different tours
            $shuffledCustomers = $customers->shuffle();

            foreach ($shuffledCustomers as $customer) {
                if ($currentParticipants >= $targetParticipants) break;

                // Randomly decide how many people this customer books for (usually 1 or 2)
                $numToBook = rand(1, min(4, $targetParticipants - $currentParticipants));

                Registration::create([
                    'visit_id' => $visit->visit_id,
                    'user_id' => $customer->user_id,
                    'num_participants' => $numToBook,
                    'booking_code' => $generateBookingCode($visit->visit_id, $customer->user_id)
                ]);

                $currentParticipants += $numToBook;
            }
        }

        $this->command->info('RegistrationsSeeder completed successfully.');
    }
}
