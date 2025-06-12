<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;        // Import User model
use App\Models\Visit;       // Import Visit model
use App\Models\Registration; // Import Registration model
// Remove Carbon import if no longer needed for date calculation
// use Carbon\Carbon;

class RegistrationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users with the 'fruitore' role
        $fruitori = User::role('fruitore')->take(3)->get(); // Get up to 3 users with the role

        // Get some proposed and confirmed visits
        $proposedVisits = Visit::where('status', 'proposed')->take(1)->get();
        $confirmedVisits = Visit::where('status', 'confirmed')->take(2)->get();
        $cancelledVisits = Visit::where('status', 'cancelled')->take(1)->get();

        // Check if we found enough data
        if ($fruitori->count() < 1 || $proposedVisits->count() < 1 || $confirmedVisits->count() < 1) {
            $this->command->warn('Could not find enough Users (role: fruitore) or Visits (proposed/confirmed) to seed registrations. Skipping.');
            return; // Exit gracefully if not enough data
        }

        // Assign users and visits for seeding
        $user1 = $fruitori->get(0); // First fruitore
        $user2 = $fruitori->get(1) ?? $user1; // Second fruitore, or fallback to first
        $user3 = $fruitori->get(2) ?? $user1; // Third fruitore, or fallback to first

        $visit_prop1 = $proposedVisits->first();
        $visit_conf1 = $confirmedVisits->get(0);
        $visit_conf2 = $confirmedVisits->get(1) ?? $visit_conf1; // Second confirmed, or fallback to first
        $visit_canc1 = $cancelledVisits->first();

        // Helper function to generate booking code
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

        $this->command->info('RegistrationsSeeder completed successfully.');
    }
}
