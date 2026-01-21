<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    public function download($code)
    {
        $registration = Registration::with(['visit.visitType.place', 'user'])
            ->where('booking_code', $code)
            ->firstOrFail();

        // Authorization: Admin, Guide, or the Owner
        $user = Auth::user();
        if ($user->user_id !== $registration->user_id && !$user->hasAnyRole(['Admin', 'Guide'])) {
            abort(403, 'Unauthorized');
        }

        // Generate QR Code (SVG)
        $qrCode = QrCode::format('svg')->size(120)->generate($registration->booking_code);

        // Generate PDF
        $pdf = Pdf::loadView('tickets.pdf', compact('registration', 'qrCode'));

        return $pdf->download('ticket_' . $registration->booking_code . '.pdf');
    }
}
