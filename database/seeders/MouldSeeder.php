<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MouldStatus;
use App\Models\Mould;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MouldSeeder extends Seeder
{
    public function run(): void
    {
        // ===== 5) Moulds (buat 60 mould)
        $resins = ['PP', 'ABS', 'HIPS', 'PE'];
        $customers = ['ABC', 'XYZ', 'Kirana', 'Sinar', 'GlobalPack'];
        $names = ['Cup', 'Lid', 'Tray', 'Housing', 'Cover', 'Cap', 'Bottle', 'Handle'];

        for ($i = 1; $i <= 60; $i++) {
            $cav = [1, 2, 4, 8][array_rand([1, 2, 4, 8])];
            $minT = [100, 120, 150][array_rand([100, 120, 150])];
            $maxT = $minT + [30, 60, 80][array_rand([30, 60, 80])];

            Mould::create([
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
                'status' => MouldStatus::AVAILABLE,
            ]);
        }
    }
}
