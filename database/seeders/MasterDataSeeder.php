<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\Mould;
use App\Models\Plant;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Clean (safe for dev)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Mould::truncate();
        Machine::truncate();
        Zone::truncate();
        Plant::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /* ============================
           PLANTS
        ============================ */
        $jakarta = Plant::create([
            'id' => Str::uuid(),
            'name' => 'Plant Jakarta',
        ]);

        $karawang = Plant::create([
            'id' => Str::uuid(),
            'name' => 'Plant Karawang',
        ]);

        /* ============================
           ZONES / LINES
        ============================ */
        $zones = [
            // Jakarta
            ['plant_id' => $jakarta->id, 'code' => 'JZ1', 'name' => 'Injection Zone A'],
            ['plant_id' => $jakarta->id, 'code' => 'JZ2', 'name' => 'Injection Zone B'],
            ['plant_id' => $jakarta->id, 'code' => 'JZ3', 'name' => 'Assembly Zone'],

            // Karawang
            ['plant_id' => $karawang->id, 'code' => 'KZ1', 'name' => 'Injection Line 1'],
            ['plant_id' => $karawang->id, 'code' => 'KZ2', 'name' => 'Injection Line 2'],
            ['plant_id' => $karawang->id, 'code' => 'KZ3', 'name' => 'Finishing Line'],
        ];

        $zoneMap = [];
        foreach ($zones as $z) {
            $zone = Zone::create(array_merge($z, ['id' => Str::uuid()]));
            $zoneMap[$zone->code] = $zone;
        }

        /* ============================
           MACHINES
        ============================ */
        $machines = [
            // Jakarta
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZ1']->id, 'code' => 'MC-JKT-180T-01', 'name' => 'Injection 180T #1', 'tonnage_t' => 180, 'plc_connected' => true],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZ1']->id, 'code' => 'MC-JKT-180T-02', 'name' => 'Injection 180T #2', 'tonnage_t' => 180, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZ2']->id, 'code' => 'MC-JKT-250T-01', 'name' => 'Injection 250T', 'tonnage_t' => 250, 'plc_connected' => true],

            // Karawang
            ['plant_id' => $karawang->id, 'zone_id' => $zoneMap['KZ1']->id, 'code' => 'MC-KRW-120T-01', 'name' => 'Injection 120T', 'tonnage_t' => 120, 'plc_connected' => false],
            ['plant_id' => $karawang->id, 'zone_id' => $zoneMap['KZ2']->id, 'code' => 'MC-KRW-180T-01', 'name' => 'Injection 180T', 'tonnage_t' => 180, 'plc_connected' => true],
            ['plant_id' => $karawang->id, 'zone_id' => $zoneMap['KZ2']->id, 'code' => 'MC-KRW-300T-01', 'name' => 'Injection 300T', 'tonnage_t' => 300, 'plc_connected' => false],
        ];

        foreach ($machines as $m) {
            Machine::create(array_merge($m, ['id' => Str::uuid()]));
        }

        /* ============================
           MOULDS (sample)
        ============================ */
        $moulds = [
            ['code' => 'M-0001', 'name' => 'Cup 250ml', 'cavities' => 2, 'customer' => 'ABC', 'resin' => 'PP', 'min_tonnage_t' => 120, 'max_tonnage_t' => 180, 'pm_interval_shot' => 50000, 'pm_interval_days' => 30, 'commissioned_at' => '2024-01-15', 'status' => 'AVAILABLE'],
            ['code' => 'M-0002', 'name' => 'Bottle Cap', 'cavities' => 4, 'customer' => 'XYZ', 'resin' => 'HDPE', 'min_tonnage_t' => 180, 'max_tonnage_t' => 250, 'pm_interval_shot' => 80000, 'pm_interval_days' => 45, 'commissioned_at' => '2023-11-10', 'status' => 'AVAILABLE'],
            ['code' => 'M-0003', 'name' => 'Food Tray', 'cavities' => 1, 'customer' => 'FOODCO', 'resin' => 'PET', 'min_tonnage_t' => 250, 'max_tonnage_t' => 300, 'pm_interval_shot' => 30000, 'pm_interval_days' => 20, 'commissioned_at' => '2024-03-01', 'status' => 'AVAILABLE'],
            ['code' => 'M-0004', 'name' => 'Plastic Spoon', 'cavities' => 8, 'customer' => 'ABC', 'resin' => 'PP', 'min_tonnage_t' => 120, 'max_tonnage_t' => 180, 'pm_interval_shot' => 100000, 'pm_interval_days' => 60, 'commissioned_at' => '2022-07-01', 'status' => 'AVAILABLE'],
            ['code' => 'M-0005', 'name' => 'Container Lid', 'cavities' => 2, 'customer' => 'PACKCO', 'resin' => 'PP', 'min_tonnage_t' => 180, 'max_tonnage_t' => 250, 'pm_interval_shot' => 60000, 'pm_interval_days' => 40, 'commissioned_at' => '2023-05-10', 'status' => 'AVAILABLE'],
        ];

        foreach ($moulds as $m) {
            Mould::create(array_merge($m, ['id' => Str::uuid()]));
        }
    }
}
