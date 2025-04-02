<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;        // Import User model
use App\Models\Visit;       // Import Visit model
use App\Models\Registration; // Import Registration model
use Carbon\Carbon;          // Import Carbon for date comparison

class RegistrationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Users
        $user1 = User::where('username', 'user_paolo')->first();
        $user2 = User::where('username', 'user_elena')->first();
        $user3 = User::where('username', 'user_luca')->first();

        // Get Visits (find based on status and relative date)
        $now = Carbon::now();
        $visit_prop1 = Visit::where('status', 'proposed')
                            ->where('visit_date', $now->copy()->addDays(10)->toDateString())
                            ->first();
        $visit_conf1 = Visit::where('status', 'confirmed')
                            ->where('visit_date', $now->copy()->addDays(4)->toDateString())
                            ->first();
        $visit_conf2 = Visit::where('status', 'confirmed')
                            ->where('visit_date', $now->copy()->addDays(5)->toDateString())
                            ->first();


        if (!$user1 || !$user2 || !$user3 || !$visit_prop1 || !$visit_conf1 || !$visit_conf2) {
            $this->command->error('Required Users or Visits not found. Run UsersSeeder and VisitsSeeder first.');
            return;
        }

        // Helper function to generate booking code (mimics SQL logic)
        $generateBookingCode = function($visitId, $userId) {
            return 'BK' . str_pad($visitId, 4, '0', STR_PAD_LEFT) . 'U' . str_pad($userId, 3, '0', STR_PAD_LEFT);
        };

        // Paolo books 2 for proposed visit 1
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

        // Elena books 3 for confirmed visit 1
        Registration::updateOrCreate(
            [
                'visit_id' => $visit_conf1->visit_id,
                'user_id' => $user2->user_id
            ],
            [
                'num_participants' => 3,
                'booking_code' => $generateBookingCode($visit_conf1->visit_id, $user2->user_id)
            ]
        );

        // Luca books 1 for confirmed visit 1
        Registration::updateOrCreate(
            [
                'visit_id' => $visit_conf1->visit_id,
                'user_id' => $user3->user_id
            ],
            [
                'num_participants' => 1,
                'booking_code' => $generateBookingCode($visit_conf1->visit_id, $user3->user_id)
            ]
        );

        // Elena books 4 for confirmed visit 2
        Registration::updateOrCreate(
            [
                'visit_id' => $visit_conf2->visit_id,
                'user_id' => $user2->user_id
            ],
            [
                'num_participants' => 4,
                'booking_code' => $generateBookingCode($visit_conf2->visit_id, $user2->user_id)
            ]
        );
    }
}
