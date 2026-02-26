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
    public function showAvailabilityForm(): View
    {
        $user = Auth::user();
        $nextMonth = Carbon::now()->addMonth();
        $nextMonthYear = $nextMonth->format('Y-m');
        $daysInNextMonth = $nextMonth->daysInMonth;

        $existingAvailability = VolunteerAvailability::where('user_id', $user->user_id)
                                    ->where('month_year', $nextMonthYear)
                                    ->pluck('available_date')
                                    ->map(function ($date) {
                                        return Carbon::parse($date)->format('j');
                                    })
                                    ->flip();

        return view('volunteer.availability', [
            'monthName' => $nextMonth->format('F Y'),
            'monthYear' => $nextMonthYear,
            'daysInMonth' => $daysInNextMonth,
            'existingAvailability' => $existingAvailability,
        ]);
    }

    public function storeAvailability(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $nextMonth = Carbon::now()->addMonth();
        $nextMonthYear = $nextMonth->format('Y-m');
        $daysInNextMonth = $nextMonth->daysInMonth;

        $request->validate([
            'available_days' => ['nullable', 'array'],
            'available_days.*' => ['integer', 'min:1', 'max:31'],
        ]);

        $submitted_days = collect($request->input('available_days', []))->map(fn($day) => (int)$day);

        $datesToInsert = [];

        foreach ($submitted_days as $day) {
            $dateStr = $nextMonthYear . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
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
             } catch (\Exception $e) { }
        }

         // Start transaction
        DB::beginTransaction();
        try {
            VolunteerAvailability::where('user_id', $user->user_id)
                ->where('month_year', $nextMonthYear)
                ->delete();

            if (!empty($datesToInsert)) {
                VolunteerAvailability::insert($datesToInsert);
            }

            // Commit transaction
            DB::commit();

            return redirect()->route('volunteer.availability.form')->with('status', 'Availability updated successfully!');

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error("Failed to update availability for user {$user->user_id}: " . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['general' => 'Failed to update availability. Please try again.']);
        }
    }
}
