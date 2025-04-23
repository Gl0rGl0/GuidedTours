<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\View\View; // Import View
use App\Models\Registration; // Import Registration model
use Illuminate\Support\Facades\Log; // Import Log facade

class FruitoreController extends Controller
{
    /**
     * Show the fruitore user's dashboard with their booked visits.
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        // Fetch the authenticated user's registrations, eager loading visit and visitType with place
        $bookings = Registration::where('user_id', $user->user_id) // Use user_id
                                ->with(['visit.visitType.place']) // Eager load nested relationships
                                ->get();

        // Log the user ID and the number of bookings retrieved
        Log::info("Fetching bookings for user ID: " . $user->user_id); // Log user_id
        Log::info("Number of bookings retrieved: " . $bookings->count());

        return view('user.dashboard', ['bookings' => $bookings]);
    }

    /**
     * Handle the cancellation of a booked visit.
     */
    public function cancelBooking(Registration $booking): \Illuminate\Http\RedirectResponse
    {
        // Ensure the authenticated user owns this booking
        if ($booking->user_id !== Auth::user()->user_id) {
             return redirect()->route('user.dashboard')->with('error_message', 'You can only cancel your own bookings.');
        }

        try {
            $booking->delete(); // Delete the booking

            // TODO: Implement logic to update visit status if needed (e.g., if it becomes 'proposed' again)

            return redirect()->route('user.dashboard')->with('status', 'Booking cancelled successfully.');
        } catch (\Exception $e) {
            Log::error("Booking cancellation failed for booking {$booking->registration_id} by user {$booking->user_id}: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('user.dashboard')->with('error_message', 'Failed to cancel booking. Please try again.');
        }
    }
}
