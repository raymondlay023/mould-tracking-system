<?php

namespace App\Actions\Maintenance;

use App\Models\MaintenanceEvent;
use App\Models\Mould;
use Illuminate\Support\Facades\DB;

class CompleteWorkOrderAction
{
    /**
     * Complete a maintenance work order.
     *
     * @param MaintenanceEvent $event
     * @param array $data (downtime_min, cost, parts_used, performed_by, notes)
     * @return MaintenanceEvent
     */
    public function execute(MaintenanceEvent $event, array $data): MaintenanceEvent
    {
        return DB::transaction(function () use ($event, $data) {
            // Update Event
            $event->update([
                'status' => 'COMPLETED',
                'end_ts' => now(), // Or provided date
                'downtime_min' => $data['downtime_min'] ?? 0,
                'cost' => $data['cost'] ?? 0,
                'parts_used' => $data['parts_used'] ?? null,
                'performed_by' => $data['performed_by'] ?? auth()->user()?->name,
                'notes' => $data['notes'] ?? null,
            ]);

            // Update Mould Counters if it was a PM
            if ($event->type === 'PM') {
                $mould = $event->mould;
                
                // PM Reset Logic:
                // last_pm_at_shot = current total_shots
                $mould->last_pm_at_shot = $mould->total_shots ?? 0;
                $mould->last_pm_at_ts = now();
                
                // If mould was in IN_MAINTENANCE status, free it
                if ($mould->status === \App\Enums\MouldStatus::IN_MAINTENANCE) {
                    $mould->status = \App\Enums\MouldStatus::AVAILABLE;
                }
                
                $mould->save();
            }

            return $event;
        });
    }
}
