<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MouldStatus;
use App\Models\LocationHistory;
use App\Models\Machine;
use App\Models\Mould;
use App\Models\Plant;
use App\Models\ProductionRun;
use App\Models\RunDefect;
use App\Models\SetupEvent;
use App\Models\TrialEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $plants = Plant::all();
        $machines = Machine::all();
        $moulds = Mould::all();

        if ($moulds->isEmpty() || $machines->isEmpty()) {
            return;
        }

        // ===== 6) Location History
        foreach ($moulds as $m) {
            $p = $plants->random();
            $locType = ['TOOL_ROOM', 'WAREHOUSE', 'MACHINE', 'MACHINE', 'MACHINE', 'IN_TRANSIT'][array_rand(['a', 'b', 'c', 'd', 'e', 'f'])];

            $machineId = null;
            if ($locType === 'MACHINE') {
                $machineId = $machines->where('plant_id', $p->id)->random()->id;
            }

            LocationHistory::create([
                'id' => Str::uuid(),
                'mould_id' => $m->id,
                'plant_id' => $p->id,
                'machine_id' => $machineId,
                'location' => $locType,
                'start_ts' => $now->copy()->subDays(rand(10, 60))->subHours(rand(0, 10)),
                'end_ts' => $now->copy()->subDays(rand(1, 9)),
            ]);

            LocationHistory::create([
                'id' => Str::uuid(),
                'mould_id' => $m->id,
                'plant_id' => $p->id,
                'machine_id' => $machineId,
                'location' => $locType,
                'start_ts' => $now->copy()->subDays(rand(0, 7))->subHours(rand(0, 8)),
                'end_ts' => null,
            ]);

            $m->status = match ($locType) {
                'MACHINE' => MouldStatus::IN_RUN,
                'IN_TRANSIT' => MouldStatus::IN_TRANSIT,
                default => MouldStatus::AVAILABLE,
            };
            $m->save();
        }

        // ===== 7) Production Runs
        $activeMoulds = $moulds->random(min(8, $moulds->count()));

        foreach ($activeMoulds as $m) {
            $loc = LocationHistory::where('mould_id', $m->id)->whereNull('end_ts')->first();
            $machineId = $loc?->machine_id ?: $machines->random()->id;

            ProductionRun::create([
                'id' => Str::uuid(),
                'mould_id' => $m->id,
                'machine_id' => $machineId,
                'cavities_snapshot' => (int) $m->cavities,
                'start_ts' => $now->copy()->subMinutes(rand(15, 420)),
                'end_ts' => null,
                'operator_name' => ['Budi', 'Andi', 'Rizky', 'Sari'][array_rand(['a', 'b', 'c', 'd'])],
            ]);
        }

        $histMoulds = $moulds->diff($activeMoulds);

        foreach ($histMoulds as $m) {
            $runsCount = rand(10, 40);
            $cav = (int) $m->cavities;

            for ($i = 0; $i < $runsCount; $i++) {
                $end = $now->copy()->subDays(rand(0, 60))->subHours(rand(0, 22))->subMinutes(rand(0, 59));
                $durMin = rand(30, 360);
                $start = $end->copy()->subMinutes($durMin);

                $shot = rand(200, 2500);
                $partTotal = $shot * $cav;
                $ng = (int) round($partTotal * (rand(2, 60) / 1000));
                $ok = $partTotal - $ng;

                $run = ProductionRun::create([
                    'id' => Str::uuid(),
                    'mould_id' => $m->id,
                    'machine_id' => $machines->random()->id,
                    'start_ts' => $start,
                    'end_ts' => $end,
                    'shot_total' => $shot,
                    'ok_part' => $ok,
                    'ng_part' => $ng,
                    'cycle_time_avg_sec' => rand(14, 40) + (rand(0, 99) / 100),
                    'operator_name' => ['Budi', 'Andi', 'Rizky', 'Sari', 'Dewi', 'Wawan'][array_rand(['a', 'b', 'c', 'd', 'e', 'f'])],
                    'notes' => rand(0, 10) > 8 ? 'Minor issue, adjusted settings.' : null,
                ]);

                if ($ng > 0) {
                    $defects = ['SHORT_SHOT', 'FLASH', 'BURN_MARK', 'BLACK_DOT', 'SINK_MARK', 'WARP'];
                    $left = $ng;
                    $rows = rand(1, 3);

                    for ($d = 0; $d < $rows; $d++) {
                        $qty = ($d === $rows - 1) ? $left : rand(1, max(1, (int) ($left / 2)));
                        $left -= $qty;

                        RunDefect::create([
                            'id' => Str::uuid(),
                            'run_id' => $run->id,
                            'defect_code' => $defects[array_rand($defects)],
                            'qty' => $qty,
                        ]);
                    }
                }
            }
        }

        // ===== 8) Setup Events
        foreach ($moulds as $m) {
            $n = rand(1, 4);
            for ($i = 0; $i < $n; $i++) {
                $end = $now->copy()->subDays(rand(0, 60))->subHours(rand(0, 22));
                $actual = rand(15, 120);
                $start = $end->copy()->subMinutes($actual);

                SetupEvent::create([
                    'id' => Str::uuid(),
                    'mould_id' => $m->id,
                    'machine_id' => $machines->random()->id,
                    'start_ts' => $start,
                    'end_ts' => $end,
                    'target_min' => [30, 45, 60, 90][array_rand([30, 45, 60, 90])],
                    'actual_min' => $actual,
                    'loss_reason' => rand(0, 10) > 7 ? 'Material delay' : null,
                    'operator_name' => ['Budi', 'Andi', 'Rizky', 'Sari'][array_rand(['a', 'b', 'c', 'd'])],
                    'notes' => rand(0, 10) > 8 ? 'Changeover took longer due to cleaning.' : null,
                ]);
            }
        }

        // ===== 9) Trial + RMP
        $trialMoulds = $moulds->random(min(20, $moulds->count()));
        foreach ($trialMoulds as $m) {
            $end = $now->copy()->subDays(rand(1, 90));
            $start = $end->copy()->subMinutes(rand(20, 120));
            $approved = rand(0, 10) > 3;

            TrialEvent::create([
                'id' => Str::uuid(),
                'mould_id' => $m->id,
                'machine_id' => $machines->random()->id,
                'start_ts' => $start,
                'end_ts' => $end,
                'purpose' => ['New mould validation', 'Parameter tuning', 'Material change', 'Customer trial'][array_rand(['a', 'b', 'c', 'd'])],
                'approved' => $approved,
                'approved_by' => $approved ? ['QA Indah', 'QA Raka', 'PE Toni'][array_rand(['a', 'b', 'c'])] : null,
                'notes' => $approved ? 'Approved for mass production.' : 'Needs rework / adjustment.',
            ]);

            if ($approved) {
                $m->rmp_last_at = $end;
                $m->rmp_approved_by = 'QA/PE';
                $m->save();
            }
        }
    }
}
