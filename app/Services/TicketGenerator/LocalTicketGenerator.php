<?php

namespace App\Services\TicketGenerator;

use App\Models\Registration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LocalTicketGenerator implements TicketGeneratorInterface
{
    public function generate(Registration $registration): string
    {
        // Simulate processing time
        sleep(1);

        $ticketContent = "TICKET CONFIRMATION\n";
        $ticketContent .= "Code: " . $registration->booking_code . "\n";
        $ticketContent .= "Visit: " . $registration->visit->visitType->title . "\n";
        $ticketContent .= "Participants: " . $registration->num_participants . "\n";
        
        $fileName = 'tickets/ticket_' . $registration->booking_code . '.txt';
        
        // Ensure directory exists (Mock S3 upload)
        Storage::disk('public')->put($fileName, $ticketContent);

        Log::info("Ticket generated and uploaded to: " . $fileName);

        return $fileName;
    }
}
