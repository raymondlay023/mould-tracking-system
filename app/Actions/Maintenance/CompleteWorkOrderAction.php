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
            $isPpm = $event->type === 'PM' && $event->pm_subtype === 'PPM';
            
            // Auto-Generate CM for NG items
            $ngTasks = [];
            if ($isPpm && is_array($event->checklist_data)) {
                foreach ($event->checklist_data as $item) {
                    if (isset($item['status']) && $item['status'] === 'NG') {
                        $ngTasks[] = $item['task'] ?? 'Unknown Task';
                    }
                }
            }

            if (!empty($ngTasks)) {
                MaintenanceEvent::create([
                    'mould_id' => $event->mould_id,
                    'type' => 'CM',
                    'start_ts' => now(),
                    'status' => 'REQUESTED',
                    'machine_id' => $event->machine_id,
                    'plant_id' => $event->plant_id,
                    'description' => \Illuminate\Support\Str::limit('Auto-CM: Failed PPM checks - ' . implode(', ', $ngTasks), 255),
                ]);
            }

            $newStatus = $isPpm ? 'IN_REVIEW' : 'COMPLETED';

            // Update Event
            $event->update([
                'status' => $newStatus,
                'end_ts' => now(), // Or provided date
                'downtime_min' => $data['downtime_min'],
                'cost' => $data['cost'] ?? 0,
                'parts_used' => $data['parts_used'] ?? null,
                'performed_by' => $data['performed_by'] ?? auth()->user()?->name,
                'notes' => $data['notes'] ?? null,
            ]);

            return $event;
        });
    }
}
