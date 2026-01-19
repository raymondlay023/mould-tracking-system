<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\LocationHistory;
use App\Models\MaintenanceEvent;
use App\Models\Mould;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $moulds = Mould::all();

        if ($moulds->isEmpty()) {
            return;
        }

        // ===== 10) Maintenance Events + next due (PM/CM)
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
    }
}
