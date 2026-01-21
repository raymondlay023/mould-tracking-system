<?php

namespace Tests\Feature;

use App\Models\Mould;
use App\Models\ProductionRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_oee_metrics()
    {
        // 1. Setup User & Permissions
        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::create(['name' => 'view_production_section']);
        $user->givePermissionTo('view_production_section');

        // 2. Setup Data
        $mould = Mould::factory()->create([
            'code' => 'M001', 
            'ideal_cycle_time' => 10.0
        ]);
        
        // Create a Closed Run with some stats
        ProductionRun::factory()->create([
            'mould_id' => $mould->id,
            'start_ts' => now()->subHour(),
            'end_ts' => now(),
            'shot_total' => 360, // 360 shots in 3600s = 10s cycle = 100% perf
            'part_total' => 360,
            'ok_part' => 360,
            'ng_part' => 0,
        ]);

        // 3. Visit Dashboard
        $response = $this->actingAs($user)
            ->get(route('production.index'));

        $response->assertOk();
        $response->assertSee('Avg OEE');
        $response->assertSee('100.0%'); // Expect 100% OEE
        $response->assertSee('M001');
    }
}
