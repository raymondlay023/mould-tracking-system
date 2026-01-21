<?php

namespace App\Stats;

use App\Models\ProductionRun;

class OeeCalculator
{
    /**
     * Calculate OEE for a closed production run.
     * Returns array:
     * [
     *   'availability' => float (0-1),
     *   'performance' => float (0-1),
     *   'quality' => float (0-1),
     *   'oee' => float (0-1)
     * ]
     */
    public static function calculate(ProductionRun $run): array
    {
        // 1. Availability
        // Definition: (Planned Production Time - Stop Time) / Planned Production Time
        // Since we track 'Run Duration' as the actual effective time, we can assume:
        // Availability = 1.0 (unless we strictly logged 'Planned Duration' vs 'Actual Duration', 
        // but typically Availability deals with 'Down Time' which we might capture via MaintenanceEvents overlapping?)
        //
        // SIMPLIFICATION FOR PHASE 9:
        // We will treat Availability as 1.0 UNLESS there are explicit Downtime events linked to this run.
        // For now, let's use: (TotalTime - Downtime) / TotalTime
        
        $startTime = $run->start_ts;
        $endTime = $run->end_ts ?? now();
        $totalDurationMinutes = $startTime->diffInMinutes($endTime);

        if ($totalDurationMinutes <= 0) {
            return ['availability' => 0, 'performance' => 0, 'quality' => 0, 'oee' => 0];
        }

        // Ideally, we sum up 'downtime' from linked maintenance events.
        // But we don't strictly link them yet.
        // Let's assume Availability = 0.95 as a placeholder or implement logic if we had downtime logs.
        // Actually, let's base it on Utilization? No.
        // Let's just default to 100% Availability for this sprint unless we have data.
        $availability = 1.0; 


        // 2. Performance
        // Definition: (Total Count * Ideal Cycle Time) / Run Time
        $totalShots = $run->shot_total ?? 0;
        $idealCycleTimeSec = $run->mould->ideal_cycle_time ?? null;
        
        $runTimeSeconds = $startTime->diffInSeconds($endTime);

        if ($idealCycleTimeSec && $runTimeSeconds > 0) {
            $theoreticalMaxShots = $runTimeSeconds / $idealCycleTimeSec;
            $performance = $theoreticalMaxShots > 0 ? ($totalShots / $theoreticalMaxShots) : 0;
            // Cap at 1.0+? Sometimes yes, speed up. But usually cap at 1.1 or raw.
            // Let's keep raw.
        } else {
            $performance = 0;
        }

        // 3. Quality
        // Definition: Good Parts / Total Parts
        $totalParts = $run->part_total ?? 0; // or shot_total * cavities?
        $goodParts = $run->ok_part ?? 0;
        
        // If part_total is missing, fallback to shot * cavities
        if ($totalParts == 0 && $totalShots > 0) {
             $cavities = $run->cavities_snapshot ?? $run->mould->cavities ?? 1;
             $totalParts = $totalShots * $cavities;
        }

        if ($totalParts > 0) {
            $quality = $goodParts / $totalParts;
        } else {
            $quality = 1.0; // No production = no defects? Or 0? Let's say 0.
            if ($totalShots > 0) $quality = 0; // produced something but verified nothing?
        }
        
        return [
            'availability' => round($availability, 4),
            'performance' => round($performance, 4),
            'quality' => round($quality, 4),
            'oee' => round($availability * $performance * $quality, 4)
        ];
    }
}
