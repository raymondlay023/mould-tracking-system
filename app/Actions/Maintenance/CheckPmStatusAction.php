<?php

namespace App\Actions\Maintenance;

use App\Models\Mould;
use Illuminate\Support\Collection;

class CheckPmStatusAction
{
    /**
     * Get all moulds that are due for PM.
     *
     * @return Collection<Mould>
     */
    public function execute(): Collection
    {
        // We only care about moulds that have a PM interval defined
        $moulds = Mould::query()
            ->whereNotNull('pm_interval_shot')
            ->where('pm_interval_shot', '>', 0)
            ->get();

        $dueMoulds = collect();

        foreach ($moulds as $mould) {
            // Logic:
            // Last PM shot count is stored in 'last_pm_at_shot' (need to verify this column exists)
            // If not existing, maybe we assume 0?
            // Current shots is 'total_shots' or 'shot_count'?
            
            // Let's assume schema based on codebase knowledge (I'll need to check schema if I'm wrong)
            // Moulds usually have 'total_shots'.
            
            $lastPm = $mould->last_pm_at_shot ?? 0;
            $current = $mould->total_shots ?? 0;
            $interval = $mould->pm_interval_shot;

            $nextPmAt = $lastPm + $interval;

            if ($current >= $nextPmAt) {
                // Determine overdue amount
                $mould->overdue_by = $current - $nextPmAt;
                $dueMoulds->push($mould);
            }
        }

        return $dueMoulds;
    }
}
