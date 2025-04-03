<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\VolunteerAvailability;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Import DB facade

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

        $request->validate([
            'available_days' => ['nullable', 'array'],
            // Ensure submitted days are actually integers (days of the month)
            'available_days.*' => ['integer', 'min:1', 'max:31'],
        ]);

        // 1. Get submitted day numbers as a collection
        $submitted_days = collect($request->input('available_days', []))->map(fn($day) => (int)$day);

        // 2. Get currently stored date strings for the month/user
        $currentDateStrings = VolunteerAvailability::where('user_id', $user->user_id)
                                ->where('month_year', $nextMonthYear)
                                ->pluck('available_date'); // Collection of 'YYYY-MM-DD' strings

        // 3. Get currently stored availability records (needed for IDs to delete)
         $currentAvailabilityRecords = VolunteerAvailability::where('user_id', $user->user_id)
                                    ->where('month_year', $nextMonthYear)
                                    ->get();


        $daysToInsertData = [];
        $idsToDelete = [];

        // 4. Determine Days to Insert
        foreach ($submitted_days as $day) {
            $dateStrToInsert = $nextMonthYear . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
            // Check if this date string is NOT already in the database for this user/month
            if (!$currentDateStrings->contains($dateStrToInsert)) {
                 // Check if date is valid for the month (e.g., not Feb 30th)
                 try {
                    $dateObj = Carbon::createFromFormat('Y-m-d', $dateStrToInsert);
                    if ($dateObj && $dateObj->format('Y-m') === $nextMonthYear) {
                        $daysToInsertData[] = [
                            'user_id' => $user->user_id,
                            'available_date' => $dateStrToInsert,
                            'month_year' => $nextMonthYear,
                            'declared_at' => now(),
                        ];
                    }
                 } catch (\Exception $e) { /* Ignore invalid dates */ }
            }
        }

        // 5. Determine IDs to Delete
        foreach ($currentAvailabilityRecords as $record) {
            $dayNumberInDb = (int)Carbon::parse($record->available_date)->format('j');
            // If a day stored in the DB is NOT in the submitted days, mark it for deletion
            if (!$submitted_days->contains($dayNumberInDb)) {
                $idsToDelete[] = $record->availability_id;
            }
        }

         // Start transaction
        DB::beginTransaction();
        try {
            // Perform database operations
            if (!empty($idsToDelete)) {
                VolunteerAvailability::whereIn('availability_id', $idsToDelete)->delete();
            }
            if (!empty($daysToInsert)) {
                // Ensure the structure is correct for bulk insert
                VolunteerAvailability::insert($daysToInsert);
            }

            // Commit transaction
            DB::commit();

            return redirect()->route('volunteer.availability.form')->with('status', 'Availability updated successfully!');

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            // Log the actual error for debugging
            // Log::error("Failed to update availability for user {$user->user_id}: " . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['general' => 'Failed to update availability. Please try again. Error: ' . $e->getMessage()]); // Temporarily show error message
        }
    }
}
