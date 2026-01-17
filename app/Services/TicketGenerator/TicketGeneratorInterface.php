<?php

namespace App\Services\TicketGenerator;

use App\Models\Registration;

interface TicketGeneratorInterface
{
    public function generate(Registration $registration): string;
}
