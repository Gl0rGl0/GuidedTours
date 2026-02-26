<?php

namespace App\Services;

use App\Data\BookingData;
use App\Models\Registration;
use App\Models\Visit;
use App\Events\VisitBooked;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingService
{
    public function book(BookingData $data): Registration
    {
        return DB::transaction(function () use ($data) {
            $visit = Visit::lockForUpdate()->find($data->visit_id);

            if (!$visit) {
                throw new Exception("Visit not found.");
            }

            // Check capacity
            $currentParticipants = $visit->registrations()->sum('num_participants');
            $maxParticipants = $visit->effective_max_capacity;

            if (($currentParticipants + $data->num_participants) > $maxParticipants) {
                throw new Exception("Not enough capacity. Only " . ($maxParticipants - $currentParticipants) . " spots left.");
            }

            // Create Registration
            $registration = Registration::create([
                'visit_id' => $data->visit_id,
                'user_id' => $data->user_id,
                'num_participants' => $data->num_participants,
                'booking_code' => strtoupper(Str::random(20)),
            ]);

            // Fire Event
            event(new VisitBooked($registration));

            return $registration;
        });
    }
}
