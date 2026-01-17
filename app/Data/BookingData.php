<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class BookingData extends Data
{
    public function __construct(
        public int $visit_id,
        public int $user_id,
        public int $num_participants,
    ) {}
}
