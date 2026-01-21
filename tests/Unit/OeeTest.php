<?php

namespace Tests\Unit;

use App\Models\Mould;
use App\Models\ProductionRun;
use App\Stats\OeeCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_performance_correctly()
    {
        // 1. Setup Mould with Ideal Cycle Time = 10 sec
        $mould = Mould::factory()->create(['ideal_cycle_time' => 10.0]);

        // 2. Run for 100 seconds
        $start = now();
        $end = now()->addSeconds(100);

        // 3. Perfect Performance: 10 shots in 100s
        $run = ProductionRun::factory()->create([
            'mould_id' => $mould->id,
            'start_ts' => $start,
            'end_ts' => $end,
            'shot_total' => 10,
            'ok_part' => 10, // 1 cavity assumed
            'part_total' => 10,
        ]);

        $oee = OeeCalculator::calculate($run);
        $this->assertEquals(1.0, $oee['performance'], 'Performance should be 100%');
        $this->assertEquals(1.0, $oee['oee']);

        // 4. Half Speed: 5 shots in 100s
        $run2 = ProductionRun::factory()->create([
            'mould_id' => $mould->id,
            'start_ts' => $start,
            'end_ts' => $end,
            'shot_total' => 5,
            'ok_part' => 5,
            'part_total' => 5,
        ]);
        $oee2 = OeeCalculator::calculate($run2);
        $this->assertEquals(0.5, $oee2['performance'], 'Performance should be 50%');
    }

    public function test_calculates_quality_correctly()
    {
        $mould = Mould::factory()->create(['ideal_cycle_time' => 10.0]);
        $start = now();
        $end = now()->addSeconds(100);

        // 10 shots, but 5 rejects
        $run = ProductionRun::factory()->create([
            'mould_id' => $mould->id,
            'start_ts' => $start,
            'end_ts' => $end,
            'shot_total' => 10,  // performance 100%
            'part_total' => 10,
            'ok_part' => 5,
            'ng_part' => 5,
        ]);

        $oee = OeeCalculator::calculate($run);

        $this->assertEquals(1.0, $oee['performance']);
        $this->assertEquals(0.5, $oee['quality'], 'Quality should be 50%'); // 5/10
        $this->assertEquals(0.5, $oee['oee']); // 1.0 * 1.0 * 0.5
    }
}
