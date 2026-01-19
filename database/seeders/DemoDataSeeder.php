<?php

declare(strict_types=1);

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
            // ===== 0) Optional: Clear tables (HATI-HATI kalau sudah ada data nyata)
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

            // ===== 1) Users
            if (User::count() === 0) {
                $this->makeUser('Admin Demo', 'admin@demo.local', 'Admin');
                $this->makeUser('Prod Demo', 'prod@demo.local', 'Production');
                $this->makeUser('Maint Demo', 'maint@demo.local', 'Maintenance');
                $this->makeUser('QA Demo', 'qa@demo.local', 'QA');
                $this->makeUser('Viewer Demo', 'viewer@demo.local', 'Viewer');
            }

            // ===== Call other seeders
            $this->call([
                PlantSeeder::class,
                MouldSeeder::class,
                ProductionSeeder::class,
                MaintenanceSeeder::class,
            ]);
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
