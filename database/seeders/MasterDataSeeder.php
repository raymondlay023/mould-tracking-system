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

        \App\Models\Part::truncate();
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
           ZONES
        ============================ */
        $zones = [
            // Jakarta
            ['plant_id' => $jakarta->id, 'code' => 'JZA', 'name' => 'Injection Zone A'],
            ['plant_id' => $jakarta->id, 'code' => 'JZB', 'name' => 'Injection Zone B'],
            ['plant_id' => $jakarta->id, 'code' => 'JZC', 'name' => 'Injection Zone C'],

            // Karawang
            ['plant_id' => $karawang->id, 'code' => 'KZA', 'name' => 'Injection Zone A']
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
            // Jakarta, zone A
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZA']->id, 'code' => 'MC-JKT-350T-F', 'name' => 'Injection 350F', 'tonnage_t' => 350, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZA']->id, 'code' => 'MC-JKT-450T-H', 'name' => 'Injection 450H', 'tonnage_t' => 450, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZA']->id, 'code' => 'MC-JKT-450T-I', 'name' => 'Injection 450I', 'tonnage_t' => 450, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZA']->id, 'code' => 'MC-JKT-450T-J', 'name' => 'Injection 450J', 'tonnage_t' => 450, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZA']->id, 'code' => 'MC-JKT-450T-G', 'name' => 'Injection 450G', 'tonnage_t' => 450, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZA']->id, 'code' => 'MC-JKT-550T-B', 'name' => 'Injection 550B', 'tonnage_t' => 550, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZA']->id, 'code' => 'MC-JKT-650T-D', 'name' => 'Injection 650D', 'tonnage_t' => 650, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZA']->id, 'code' => 'MC-JKT-650T-E', 'name' => 'Injection 650E', 'tonnage_t' => 650, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZA']->id, 'code' => 'MC-JKT-850T-D', 'name' => 'Injection 850D', 'tonnage_t' => 850, 'plc_connected' => false],
            // Jakarta, zone B
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-1300T-C', 'name' => 'Injection 1300C', 'tonnage_t' => 1300, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-1050T-C', 'name' => 'Injection 1050C', 'tonnage_t' => 1050, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-850T-B', 'name' => 'Injection 850B', 'tonnage_t' => 850, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-1300T-A', 'name' => 'Injection 1300A', 'tonnage_t' => 1300, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-1050T-A', 'name' => 'Injection 1050A', 'tonnage_t' => 1050, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-1050T-D', 'name' => 'Injection 1050D', 'tonnage_t' => 1050, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-650T-A', 'name' => 'Injection 650A', 'tonnage_t' => 650, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-850T-C', 'name' => 'Injection 850C', 'tonnage_t' => 850, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-650T-C', 'name' => 'Injection 650C', 'tonnage_t' => 650, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-1050T-B', 'name' => 'Injection 1050B', 'tonnage_t' => 1050, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-700T-B', 'name' => 'Injection 700B', 'tonnage_t' => 700, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-700T-A', 'name' => 'Injection 700A', 'tonnage_t' => 700, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZB']->id, 'code' => 'MC-JKT-1300T-B', 'name' => 'Injection 1300B', 'tonnage_t' => 1300, 'plc_connected' => false],
            // Jakarta, zone C
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-450T-C', 'name' => 'Injection 450C', 'tonnage_t' => 450, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-150T-E', 'name' => 'Injection 150E', 'tonnage_t' => 150, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-150T-F', 'name' => 'Injection 150F', 'tonnage_t' => 150, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-360T-A', 'name' => 'Injection 360A', 'tonnage_t' => 360, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-360T-B', 'name' => 'Injection 360B', 'tonnage_t' => 360, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-360T-C', 'name' => 'Injection 360C', 'tonnage_t' => 360, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-360T-D', 'name' => 'Injection 360D', 'tonnage_t' => 360, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-450T-B', 'name' => 'Injection 450B', 'tonnage_t' => 450, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-450T-A', 'name' => 'Injection 450A', 'tonnage_t' => 450, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-350T-E', 'name' => 'Injection 350E', 'tonnage_t' => 350, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-450T-E', 'name' => 'Injection 450E', 'tonnage_t' => 450, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-150T-B', 'name' => 'Injection 150B', 'tonnage_t' => 150, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-150T-C', 'name' => 'Injection 150C', 'tonnage_t' => 150, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-150T-D', 'name' => 'Injection 150D', 'tonnage_t' => 150, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-50T-A', 'name' => 'Injection 50A', 'tonnage_t' => 50, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-110T-A', 'name' => 'Injection 110A', 'tonnage_t' => 110, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-50T-B', 'name' => 'Injection 50B', 'tonnage_t' => 50, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-240T-A', 'name' => 'Injection 240A', 'tonnage_t' => 240, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-300T-A', 'name' => 'Injection 300A', 'tonnage_t' => 300, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-300T-B', 'name' => 'Injection 300B', 'tonnage_t' => 300, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-170T-A', 'name' => 'Injection 170A', 'tonnage_t' => 170, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-170T-B', 'name' => 'Injection 170B', 'tonnage_t' => 170, 'plc_connected' => false],
            ['plant_id' => $jakarta->id, 'zone_id' => $zoneMap['JZC']->id, 'code' => 'MC-JKT-150T-A', 'name' => 'Injection 150A', 'tonnage_t' => 150, 'plc_connected' => false],
        ];

        foreach ($machines as $m) {
            Machine::create(array_merge($m, ['id' => Str::uuid()]));
        }
    }
}
