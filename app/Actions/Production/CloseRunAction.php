<?php

namespace App\Actions\Production;

use App\Models\ProductionRun;
use App\Models\RunDefect;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CloseRunAction
{
    /**
     * Close a production run.
     *
     * @param ProductionRun $run
     * @param array $data Input data: shot_total, ok_part, ng_part, cycle_time_avg_sec, notes, defects
     * @return ProductionRun
     * @throws ValidationException
     */
    public function execute(ProductionRun $run, array $data): ProductionRun
    {
        if ($run->end_ts) {
            throw ValidationException::withMessages(['run' => 'Run is already closed.']);
        }

        // 1. Calculate and Cross-Check
        $cav = (int) $run->cavities_snapshot;
        $shotTotal = (int) $data['shot_total'];
        $okPart = (int) $data['ok_part'];
        $ngPart = (int) $data['ng_part'];
        
        $partTotal = $shotTotal * $cav;

        // Rule: ok + ng == total parts
        if (($okPart + $ngPart) !== $partTotal) {
            throw ValidationException::withMessages([
                'ok_part' => "OK ($okPart) + NG ($ngPart) must equal Total Parts ($partTotal) [Shots * Cavity].",
            ]);
        }

        // 2. Filter Defects
        $defects = collect($data['defects'] ?? [])
            ->filter(fn($d) => !empty($d['defect_code']) || ($d['qty'] ?? 0) > 0)
            ->map(fn($d) => [
                'defect_code' => strtoupper(trim($d['defect_code'] ?? '')),
                'qty' => (int) ($d['qty'] ?? 0),
            ])
            ->values();

        // Rule: total defect qty == ng_part
        $sumDefects = $defects->sum('qty');
        if ($sumDefects !== $ngPart) {
            throw ValidationException::withMessages([
                'ng_part' => "Total defect quantity ($sumDefects) must match NG Part count ($ngPart).",
            ]);
        }

        // 3. Transaction
        return DB::transaction(function () use ($run, $data, $partTotal, $defects) {
            // Update Run
            $run->update([
                'end_ts' => now(),
                'shot_total' => $data['shot_total'],
                'part_total' => $partTotal,
                'ok_part' => $data['ok_part'],
                'ng_part' => $data['ng_part'],
                'cycle_time_avg_sec' => $data['cycle_time_avg_sec'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Replace Defects (Clear old if any, though usually none for active run)
            RunDefect::where('run_id', '=', $run->id)->delete();
            
            foreach ($defects as $d) {
                RunDefect::create([
                    'run_id' => $run->id,
                    'defect_code' => $d['defect_code'],
                    'qty' => $d['qty'],
                ]);
            }

            // Update Mould Status and Counters
            $mould = $run->mould;
            $mould->status = 'AVAILABLE';
            $mould->total_shots = ($mould->total_shots ?? 0) + $data['shot_total'];
            $mould->save();

            return $run;
        });
    }
}
