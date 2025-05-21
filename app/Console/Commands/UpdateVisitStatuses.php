<?php

namespace App\Console\Commands;

use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateVisitStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-visit-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates visit statuses daily: confirms/cancels upcoming visits and completes past ones.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting visit status update process...');
        Log::info('UpdateVisitStatuses command started.');

        $today = Carbon::today();
        $threeDaysFromNow = Carbon::today()->addDays(3)->toDateString();

        // 1. Process visits that are 3 days away from today
        $this->info("Processing visits for date: {$threeDaysFromNow}");
        $upcomingVisits = Visit::with(['registrations', 'visitType'])
            ->whereIn('status', [Visit::STATUS_PROPOSED, Visit::STATUS_COMPLETE])
            ->whereDate('visit_date', $threeDaysFromNow)
            ->get();

        foreach ($upcomingVisits as $visit) {
            $registrationsCount = $visit->registrations->sum('num_participants');
            $minParticipants = $visit->visitType->min_participants;

            if($visit->assignedVolunteer()){
                if ($registrationsCount >= $minParticipants) {
                    $visit->status = Visit::STATUS_CONFIRMED;
                    $this->line("Visit ID {$visit->visit_id} confirmed. Registrations: {$registrationsCount}, Min required: {$minParticipants}");
                    Log::info("Visit ID {$visit->visit_id} confirmed.");
                } else {
                    $visit->status = Visit::STATUS_CANCELLED;
                    $this->line("Visit ID {$visit->visit_id} cancelled. Registrations: {$registrationsCount}, Min required: {$minParticipants}");
                    Log::info("Visit ID {$visit->visit_id} cancelled due to insufficient registrations. Registrations: {$registrationsCount}, Min required: {$minParticipants}");
                }
            }else{
                $visit->status = Visit::STATUS_CANCELLED;
                $this->line("Visit ID {$visit->visit_id} cancelled. No volunteer assigned");
                Log::info("Visit ID {$visit->visit_id} cancelled due to unassigned volunteer.");
            }

            $visit->save();
        }
        $this->info(count($upcomingVisits) . " upcoming visits processed.");

        // 2. Process past confirmed visits to mark them as complete
        $this->info("Processing past confirmed visits to mark as complete (before {$today->toDateString()})...");
        $pastConfirmedVisits = Visit::where('status', Visit::STATUS_CONFIRMED)
            ->whereDate('visit_date', '>=', $today)
            ->get();

        foreach ($pastConfirmedVisits as $visit) {
            $visit->status = Visit::STATUS_EFFECTED;
            $visit->save();
            $this->line("Visit ID {$visit->visit_id} marked as effected.");
            Log::info("Visit ID {$visit->visit_id} marked as effected.");
        }
        $this->info(count($pastConfirmedVisits) . " past confirmed visits marked as complete.");

        Log::info('UpdateVisitStatuses command finished.');
        $this->info('Visit status update process finished.');
        return Command::SUCCESS;
    }
}
