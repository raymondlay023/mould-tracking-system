<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\Plant;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlantSeeder extends Seeder
{
    public function run(): void
    {
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
        foreach ($zones as $z) {
            for ($i = 1; $i <= 3; $i++) {
                $code = $z->plant->name === 'Plant Jakarta'
                    ? "JK-{$z->code}-M{$i}"
                    : "KR-{$z->code}-M{$i}";

                Machine::create([
                    'id' => Str::uuid(),
                    'plant_id' => $z->plant_id,
                    'zone_id' => $z->id,
                    'code' => $code,
                    'name' => "Injection Machine {$i}",
                    'tonnage_t' => [120, 150, 180, 220][array_rand([120, 150, 180, 220])],
                ]);
            }
        }
    }
}
