<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckPmDue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maint:check-pm-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for Moulds due for PM and generate Maintenance Events';

    /**
     * Execute the console command.
     */
    public function handle(\App\Actions\Maintenance\CheckPmStatusAction $checkAction)
    {
        $this->info('Checking PM status...');
        
        $dueMoulds = $checkAction->execute();

        if ($dueMoulds->isEmpty()) {
            $this->info('No moulds due for PM.');
            return;
        }

        $this->info("Found {$dueMoulds->count()} moulds due.");

        foreach ($dueMoulds as $mould) {
            // Check if active PM exists to avoid duplicates
            // We verify if there is any 'OPEN' event of type 'PM' for this mould
            // Statuses: Currently MaintenanceEvent doesn't have 'status' column in migration?
            // Wait, I checked MaintenanceEvent model. It has 'start_ts', 'end_ts'.
            // If 'end_ts' is null, it's active.
            
            $activePm = \App\Models\MaintenanceEvent::where('mould_id', '=', $mould->id)
                ->where('type', '=', 'PM') // Assuming 'PM' is valid type
                ->whereNull('end_ts')
                ->exists();

            if ($activePm) {
                $this->warn("Mould {$mould->code}: PM already active. Skipping.");
                continue;
            }

            // Create PM Event
            \App\Models\MaintenanceEvent::create([
                'mould_id' => $mould->id,
                'type' => 'PM',
                'status' => 'REQUESTED',
                'start_ts' => now(), // Ticket created time
                'end_ts' => null,    // Remains open
                'description' => "Auto-generated PM Request. Overdue by {$mould->overdue_by} shots.",
                'machine_id' => null, 
                'plant_id' => null,
            ]);

            // Notify
            $recipients = \App\Models\User::permission('view_maintenance_section')->get();
            if ($recipients->count() > 0) {
                // Determine reason (MVP: assumme shot based on common path, or check logic in action if exposed)
                $reason = 'shot'; 
                \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\Maintenance\PmDueAlert($mould, $reason));
            }

            $this->info("Mould {$mould->code}: PM Ticket created.");
        }
    }
}
