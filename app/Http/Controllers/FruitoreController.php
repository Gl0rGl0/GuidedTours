<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Registration;
use App\Models\Visit;
use Illuminate\Support\Facades\Log;

class FruitoreController extends Controller
{
    /**
     * Show the fruitore user's dashboard with their booked visits.
     */
    public function dashboard(): View
    {
        $user = Auth::user();

        // Fetch the authenticated user's registrations, eager loading visit and visitType with place
        $bookings = Registration::where('user_id', $user->user_id)
                                ->with(['visit.visitType.place'])
                                ->whereHas('visit', function($q) {
                                    $q->whereIn('status', [
                                        Visit::STATUS_PROPOSED,
                                        Visit::STATUS_COMPLETE,
                                    ]);
                                })
                                ->get();

        // Log the user ID and the number of bookings retrieved
        Log::info("Fetching bookings for user ID: " . $user->user_id);
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
            $visit = $booking->visit()->with('visitType', 'registrations')->first();

            if (in_array($visit->status, [Visit::STATUS_CANCELLED, Visit::STATUS_CONFIRMED])) {
                //return redirect()->route('home')->with('error_message', 'This visit is no longer available for cancellation.');
                return back()->withErrors(['general' => 'This visit is no longer available for cancellation.'])->withInput();
            }
            $booking->delete();
            
            if ($visit) {
                // Reload visit data after deleting the registration
                $visit->refresh();
                $currentSubscribers = $visit->registrations()->sum('num_participants');

                // If the visit was confirmed (full) and now has space, revert to proposed
                if ($visit->status === Visit::STATUS_COMPLETE && 
                    $visit->visitType && 
                    $currentSubscribers < $visit->visitType->max_participants) {
                    $visit->status = Visit::STATUS_PROPOSED;
                    $visit->save();
                    Log::info("Visit ID {$visit->visit_id} status updated to PROPOSED due to cancellation, making space available.");
                }
            }

            return redirect()->route('user.dashboard')->with('status', 'Booking cancelled successfully.');
        } catch (\Exception $e) {
            Log::error("Booking cancellation failed for booking {$booking->registration_id} by user {$booking->user_id}: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('user.dashboard')->with('error_message', 'Failed to cancel booking. Please try again.');
        }
    }
}
