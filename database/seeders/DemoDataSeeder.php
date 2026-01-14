<?php

namespace Database\Seeders;

use App\Models\LocationHistory;
use App\Models\Machine;
use App\Models\MaintenanceEvent;
use App\Models\Mould;
use App\Models\Plant;
use App\Models\ProductionRun;      // ganti kalau model kamu beda
use App\Models\RunDefect;
use App\Models\SetupEvent;
use App\Models\TrialEvent;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = now();

            // ===== 0) Optional: Clear tables (HATI-HATI kalau sudah ada data nyata)
            // Kalau mau safe: comment bagian ini.
            $tables = [
                'run_defects',
                'production_runs',
                'setup_events',
                'trial_events',
                'maintenance_events',
                'location_histories',
                'moulds',
                'machines',
                'zones',
                'plants',
            ];

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            foreach ($tables as $t) {
                DB::table($t)->truncate();
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // ===== 1) Users (opsional, kalau sudah ada jangan bikin)
            // Pastikan minimal ada user admin/production/maintenance/qa/viewer
            // Kalau user sudah ada, skip.
            if (User::count() === 0) {
                $this->makeUser('Admin Demo', 'admin@demo.local', 'Admin');
                $this->makeUser('Prod Demo', 'prod@demo.local', 'Production');
                $this->makeUser('Maint Demo', 'maint@demo.local', 'Maintenance');
                $this->makeUser('QA Demo', 'qa@demo.local', 'QA');
                $this->makeUser('Viewer Demo', 'viewer@demo.local', 'Viewer');
            }

            // ===== 2) Plants
            $plants = collect([
                Plant::create(['id' => Str::uuid(), 'name' => 'Plant Jakarta']),
                Plant::create(['id' => Str::uuid(), 'name' => 'Plant Karawang']),
            ]);

            // ===== 3) Zones (per plant 2 zones)
            $zones = collect();
            foreach ($plants as $p) {
                $zones->push(Zone::create(['id' => Str::uuid(), 'plant_id' => $p->id, 'code' => 'Z1', 'name' => 'Zone 1']));
                $zones->push(Zone::create(['id' => Str::uuid(), 'plant_id' => $p->id, 'code' => 'Z2', 'name' => 'Zone 2']));
            }

            // ===== 4) Machines (per zone 3 mesin)
            $machines = collect();
            foreach ($zones as $z) {
                for ($i = 1; $i <= 3; $i++) {
                    $code = $z->plant->name === 'Plant Jakarta'
                        ? "JK-{$z->code}-M{$i}"
                        : "KR-{$z->code}-M{$i}";

                    $machines->push(Machine::create([
                        'id' => Str::uuid(),
                        'plant_id' => $z->plant_id,
                        'zone_id' => $z->id,
                        'code' => $code,
                        'name' => "Injection Machine {$i}",
                        'tonnage_t' => [120, 150, 180, 220][array_rand([120, 150, 180, 220])],
                    ]));
                }
            }

            // ===== 5) Moulds (buat 60 mould)
            $moulds = collect();
            $resins = ['PP', 'ABS', 'HIPS', 'PE'];
            $customers = ['ABC', 'XYZ', 'Kirana', 'Sinar', 'GlobalPack'];
            $names = ['Cup', 'Lid', 'Tray', 'Housing', 'Cover', 'Cap', 'Bottle', 'Handle'];

            for ($i = 1; $i <= 60; $i++) {
                $cav = [1, 2, 4, 8][array_rand([1, 2, 4, 8])];
                $minT = [100, 120, 150][array_rand([100, 120, 150])];
                $maxT = $minT + [30, 60, 80][array_rand([30, 60, 80])];

                $moulds->push(Mould::create([
                    'id' => Str::uuid(),
                    'code' => 'M-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                    'name' => $names[array_rand($names)].' '.rand(100, 999).'ml',
                    'cavities' => $cav,
                    'customer' => $customers[array_rand($customers)],
                    'resin' => $resins[array_rand($resins)],
                    'min_tonnage_t' => $minT,
                    'max_tonnage_t' => $maxT,
                    'pm_interval_shot' => [20000, 30000, 50000, 80000][array_rand([20000, 30000, 50000, 80000])],
                    'pm_interval_days' => [15, 30, 45][array_rand([15, 30, 45])],
                    'commissioned_at' => now()->subDays(rand(60, 720))->toDateString(),
                    'status' => 'AVAILABLE',
                ]));
            }

            // ===== 6) Location History (set lokasi current + histori singkat)
            // Sebagian di TOOL_ROOM, sebagian di WAREHOUSE, sebagian di MACHINE, sebagian IN_TRANSIT
            foreach ($moulds as $m) {
                $p = $plants->random();
                $locType = ['TOOL_ROOM', 'WAREHOUSE', 'MACHINE', 'MACHINE', 'MACHINE', 'IN_TRANSIT'][array_rand(['a', 'b', 'c', 'd', 'e', 'f'])];

                $machineId = null;
                if ($locType === 'MACHINE') {
                    // pick machine within same plant
                    $machineId = $machines->where('plant_id', $p->id)->random()->id;
                }

                // buat satu histori lama (closed)
                LocationHistory::create([
                    'id' => Str::uuid(),
                    'mould_id' => $m->id,
                    'plant_id' => $p->id,
                    'machine_id' => $machineId,
                    'location' => $locType,
                    'start_ts' => $now->copy()->subDays(rand(10, 60))->subHours(rand(0, 10)),
                    'end_ts' => $now->copy()->subDays(rand(1, 9)),
                ]);

                // current open row
                LocationHistory::create([
                    'id' => Str::uuid(),
                    'mould_id' => $m->id,
                    'plant_id' => $p->id,
                    'machine_id' => $machineId,
                    'location' => $locType,
                    'start_ts' => $now->copy()->subDays(rand(0, 7))->subHours(rand(0, 8)),
                    'end_ts' => null,
                ]);

                // status mould ikut lokasi
                $m->status = match ($locType) {
                    'MACHINE' => 'IN_RUN',
                    'IN_TRANSIT' => 'IN_TRANSIT',
                    default => 'AVAILABLE',
                };
                $m->save();
            }

            // ===== 7) Production Runs
            // - 8 mould dibuat "active run" (end_ts null)
            // - sisanya punya histori 10–40 runs selama 60 hari
            $activeMoulds = $moulds->random(8);

            foreach ($activeMoulds as $m) {
                $loc = LocationHistory::where('mould_id', $m->id)->whereNull('end_ts')->first();
                $machineId = $loc?->machine_id ?: $machines->random()->id;

                ProductionRun::create([
                    'id' => Str::uuid(),
                    'mould_id' => $m->id,
                    'machine_id' => $machineId,
                    'cavities_snapshot' => (int) $m->cavities, // ✅ wajib
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

                    // NG rate random 0.2% - 6%
                    $ng = (int) round($partTotal * (rand(2, 60) / 1000));
                    $ok = $partTotal - $ng;

                    $machineId = $machines->random()->id;

                    $run = ProductionRun::create([
                        'id' => Str::uuid(),
                        'mould_id' => $m->id,
                        'machine_id' => $machineId,
                        'start_ts' => $start,
                        'end_ts' => $end,
                        'shot_total' => $shot,
                        'ok_part' => $ok,
                        'ng_part' => $ng,
                        'cycle_time_avg_sec' => rand(14, 40) + (rand(0, 99) / 100),
                        'operator_name' => ['Budi', 'Andi', 'Rizky', 'Sari', 'Dewi', 'Wawan'][array_rand(['a', 'b', 'c', 'd', 'e', 'f'])],
                        'notes' => rand(0, 10) > 8 ? 'Minor issue, adjusted settings.' : null,
                    ]);

                    // defects breakdown (kalau ng>0)
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

            // ===== 8) Setup Events (buat 1–4 setup per mould selama 60 hari)
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

            // ===== 9) Trial + RMP (sekitar 35% mould punya trial approved)
            $trialMoulds = $moulds->random(20);
            foreach ($trialMoulds as $m) {
                $end = $now->copy()->subDays(rand(1, 90));
                $start = $end->copy()->subMinutes(rand(20, 120));
                $approved = rand(0, 10) > 3; // 70% approved

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

            // ===== 10) Maintenance Events + next due (PM/CM)
            // Sebagian mould dibuat overdue PM
            foreach ($moulds as $m) {
                $n = rand(1, 4);
                for ($i = 0; $i < $n; $i++) {
                    $type = rand(0, 10) > 7 ? 'CM' : 'PM';
                    $end = $now->copy()->subDays(rand(0, 60))->subHours(rand(0, 22));
                    $start = $end->copy()->subMinutes(rand(20, 240));

                    $downtime = (int) $start->diffInMinutes($end);
                    $cost = $type === 'CM' ? rand(200000, 2500000) : rand(0, 500000);

                    // auto fill plant/machine dari location saat itu (histori)
                    $locAtTime = LocationHistory::where('mould_id', $m->id)
                        ->where('start_ts', '<=', $end)
                        ->where(function ($q) use ($end) {
                            $q->whereNull('end_ts')->orWhere('end_ts', '>=', $end);
                        })
                        ->orderByDesc('start_ts')
                        ->first();

                    $plantId = $locAtTime?->plant_id;
                    $machineId = ($locAtTime && $locAtTime->location === 'MACHINE') ? $locAtTime->machine_id : null;

                    // next due: sebagian dibuat overdue
                    $nextDueDate = $end->copy()->addDays($m->pm_interval_days ?? 30)->toDateString();
                    if (rand(0, 10) > 8) { // 20% overdue by date
                        $nextDueDate = $now->copy()->subDays(rand(1, 10))->toDateString();
                    }

                    MaintenanceEvent::create([
                        'id' => Str::uuid(),
                        'mould_id' => $m->id,
                        'machine_id' => $machineId, // nullable
                        'plant_id' => $plantId,     // nullable
                        'start_ts' => $start,
                        'end_ts' => $end,
                        'type' => $type,
                        'description' => $type === 'PM' ? 'Preventive maintenance' : 'Corrective maintenance - issue found',
                        'parts_used' => $type === 'CM' ? 'Seal, pin, spring' : null,
                        'downtime_min' => $downtime,
                        'cost' => $cost,
                        'next_due_shot' => rand(0, 10) > 6 ? rand(30000, 120000) : null,
                        'next_due_date' => $nextDueDate,
                        'performed_by' => ['Tooling A', 'Tooling B', 'Vendor'][array_rand(['a', 'b', 'c'])],
                        'notes' => rand(0, 10) > 8 ? 'Photo attached in real scenario.' : null,
                    ]);
                }
            }
        });
    }

    private function makeUser(string $name, string $email, string $role): void
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt('password'),
            ]
        );

        if (method_exists($user, 'assignRole')) {
            $user->assignRole($role);
        }
    }
}
