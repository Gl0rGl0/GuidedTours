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
        $fruitori = User::role('fruitore')->take(3)->get();

        $proposedVisits = Visit::where('status', 'proposed')->take(1)->get();
        $confirmedVisits = Visit::where('status', 'confirmed')->take(2)->get();
        $cancelledVisits = Visit::where('status', 'cancelled')->take(1)->get();
        $effectedVisits = Visit::where('status', 'effected')->take(1)->get();

        if ($fruitori->count() < 1 || $proposedVisits->count() < 1 || $confirmedVisits->count() < 1) {
            $this->command->warn('Could not find enough Users (role: fruitore) or Visits (proposed/confirmed) to seed registrations. Skipping.');
            return;
        }

        $user1 = $fruitori->get(0);
        $user2 = $fruitori->get(1) ?? $user1; // ?? è fallback

        $visit_prop1 = $proposedVisits->first();
        $visit_conf1 = $confirmedVisits->get(0);
        $visit_conf2 = $confirmedVisits->get(1) ?? $visit_conf1;
        $visit_canc1 = $cancelledVisits->first();
        $visit_effect = $effectedVisits->first();

        $generateBookingCode = function($visitId, $userId) {
            return 'BK' . str_pad($visitId, 4, '0', STR_PAD_LEFT) . 'U' . str_pad($userId, 3, '0', STR_PAD_LEFT);
        };

        // User 1 books 2 for proposed visit 1
        Registration::updateOrCreate(
            [
                'visit_id' => $visit_prop1->visit_id,
                'user_id' => $user1->user_id
            ],
            [
                'num_participants' => 2,
                'booking_code' => $generateBookingCode($visit_prop1->visit_id, $user1->user_id)
            ]
        );

        // User 2 books 3 for confirmed visit 1
        Registration::updateOrCreate(
            [
                'visit_id' => $visit_conf1->visit_id,
                'user_id' => $user2->user_id
            ],
            [
                'num_participants' => $visit_conf1->visitType->max_participants,
                'booking_code' => $generateBookingCode($visit_conf1->visit_id, $user2->user_id)
            ]
        );

        // User 2 books min for confirmed visit 2
        Registration::updateOrCreate(
            [
                'visit_id' => $visit_conf2->visit_id,
                'user_id' => $user2->user_id
            ],
            [
                'num_participants' => $visit_conf2->visitType->min_participants,
                'booking_code' => $generateBookingCode($visit_conf2->visit_id, $user2->user_id)
            ]
        );

        // User 2 books min - 1 for cancelled visit 1
        Registration::updateOrCreate(
            [
                'visit_id' => $visit_canc1->visit_id,
                'user_id' => $user2->user_id
            ],
            [
                'num_participants' => $visit_canc1->visitType->min_participants - 1,
                'booking_code' => $generateBookingCode($visit_canc1->visit_id, $user2->user_id)
            ]
        );

        // User 1 books min + 1 for effected visit 1
        Registration::updateOrCreate(
            [
                'visit_id' => $visit_effect->visit_id,
                'user_id' => $user1->user_id
            ],
            [
                'num_participants' => $visit_effect->visitType->min_participants + 1,
                'booking_code' => $generateBookingCode($visit_effect->visit_id, $user1->user_id)
            ]
        );

        $this->command->info('RegistrationsSeeder completed successfully.');
    }
}
