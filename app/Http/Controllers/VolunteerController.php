<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\VolunteerAvailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 

class VolunteerController extends Controller
{
    /**
     * Show the form for declaring availability for the next month.
     */
    public function showAvailabilityForm(): View
    {
        $user = Auth::user();
        $nextMonth = Carbon::now()->addMonth();
        $nextMonthYear = $nextMonth->format('Y-m');
        $daysInNextMonth = $nextMonth->daysInMonth;

        // Get existing availability for the next month for the current user
        $existingAvailability = VolunteerAvailability::where('user_id', $user->user_id)
                                    ->where('month_year', $nextMonthYear)
                                    ->pluck('available_date')
                                    ->map(function ($date) {
                                        // Extract just the day part for easy checking in the view
                                        return Carbon::parse($date)->format('j');
                                    })
                                    ->flip(); // Flip keys/values for easy isset() check

        // We'll create this view next
        return view('volunteer.availability', [
            'monthName' => $nextMonth->format('F Y'),
            'monthYear' => $nextMonthYear,
            'daysInMonth' => $daysInNextMonth,
            'existingAvailability' => $existingAvailability,
        ]);
    }

    /**
     * Store the volunteer's availability for the next month.
     */
    public function storeAvailability(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $nextMonth = Carbon::now()->addMonth();
        $nextMonthYear = $nextMonth->format('Y-m');
        $daysInNextMonth = $nextMonth->daysInMonth;

        $request->validate([
            'available_days' => ['nullable', 'array'],
            // Ensure submitted days are actually integers (days of the month)
            'available_days.*' => ['integer', 'min:1', 'max:31'],
        ]);

        // Get submitted day numbers as a collection
        $submitted_days = collect($request->input('available_days', []))->map(fn($day) => (int)$day);

        $datesToInsert = [];

        // Determine Dates to Insert
        foreach ($submitted_days as $day) {
            $dateStr = $nextMonthYear . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
            // Basic check for valid date within the month (optional, validation rule helps)
             try {
                $dateObj = Carbon::createFromFormat('Y-m-d', $dateStr);
                if ($dateObj && $dateObj->format('Y-m') === $nextMonthYear) {
                    $datesToInsert[] = [
                        'user_id' => $user->user_id,
                        'available_date' => $dateStr,
                        'month_year' => $nextMonthYear,
                        'declared_at' => now(),
                    ];
                }
             } catch (\Exception $e) { /* Ignore invalid dates */ }
        }

         // Start transaction
        DB::beginTransaction();
        try {
            // 1. Delete all existing availability for this user and month
            VolunteerAvailability::where('user_id', $user->user_id)
                ->where('month_year', $nextMonthYear)
                ->delete();

            // 2. Insert the newly submitted availability dates
            if (!empty($datesToInsert)) {
                VolunteerAvailability::insert($datesToInsert);
            }

            // Commit transaction
            DB::commit();

            return redirect()->route('volunteer.availability.form')->with('status', 'Availability updated successfully!');

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            // Log the actual error for debugging
            Log::error("Failed to update availability for user {$user->user_id}: " . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['general' => 'Failed to update availability. Please try again. Error: ' . $e->getMessage()]); // Temporarily show error message
        }
    }
}
