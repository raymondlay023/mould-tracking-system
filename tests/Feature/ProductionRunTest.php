<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\Mould;
use App\Models\ProductionRun;
use App\Models\RunDefect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductionRunTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Production']);

        // Create and authenticate a production user
        $this->user = User::factory()->create();
        $this->user->assignRole('Production');
        $this->actingAs($this->user);
    }

    /** @test */
    public function test_can_view_active_runs_page(): void
    {
        $response = $this->get(route('runs.active'));

        $response->assertOk();
        $response->assertSeeLivewire('runs.active');
    }

    /** @test */
    public function test_active_runs_display_correctly(): void
    {
        $activeRun = ProductionRun::factory()->active()->create();
        $closedRun = ProductionRun::factory()->closed()->create();

        Livewire::test(\App\Livewire\Runs\Active::class)
            ->assertSee($activeRun->mould->code)
            ->assertDontSee($closedRun->mould->code);
    }

    /** @test */
    public function test_can_close_production_run_with_valid_data(): void
    {
        $mould = Mould::factory()->create(['cavities' => 4]);
        $machine = Machine::factory()->create();
        $run = ProductionRun::factory()->create([
            'mould_id' => $mould->id,
            'machine_id' => $machine->id,
            'cavities_snapshot' => 4,
            'end_ts' => null, // Active run
        ]);

        Livewire::test(\App\Livewire\Runs\Close::class, ['run' => $run])
            ->set('shot_total', 100)
            ->set('ok_part', 380)
            ->set('ng_part', 20)
            ->set('cycle_time_avg_sec', 45)
            ->set('operator_name', 'John Doe')
            ->set('defects', [
                ['defect_code' => 'FLASH', 'qty' => 12],
                ['defect_code' => 'SHORT', 'qty' => 8],
            ])
            ->call('closeRun')
            ->assertHasNoErrors();

        $run->refresh();

        $this->assertNotNull($run->end_ts);
        $this->assertEquals(100, $run->shot_total);
        $this->assertEquals(400, $run->part_total); // 100 shots * 4 cavities
        $this->assertEquals(380, $run->ok_part);
        $this->assertEquals(20, $run->ng_part);
        $this->assertEquals(45, $run->cycle_time_avg_sec);

        // Verify defects were recorded
        $this->assertEquals(2, $run->defects()->count());
        $this->assertEquals(12, $run->defects()->where('defect_code', 'FLASH')->first()->qty);
        $this->assertEquals(8, $run->defects()->where('defect_code', 'SHORT')->first()->qty);
    }

    /** @test */
    public function test_cannot_close_run_with_invalid_ok_ng_totals(): void
    {
        $mould = Mould::factory()->create(['cavities' => 2]);
        $machine = Machine::factory()->create();
        $run = ProductionRun::factory()->create([
            'mould_id' => $mould->id,
            'machine_id' => $machine->id,
            'cavities_snapshot' => 2,
            'end_ts' => null,
        ]);

        Livewire::test(\App\Livewire\Runs\Close::class, ['run' => $run])
            ->set('shot_total', 100)
            ->set('ok_part', 150) // Should be 200 total (100 * 2)
            ->set('ng_part', 40)  // 150 + 40 = 190 ≠ 200
            ->set('defects', [['defect_code' => 'FLASH', 'qty' => 40]])
            ->call('closeRun')
            ->assertHasErrors('ok_part');

        $run->refresh();
        $this->assertNull($run->end_ts); // Run should still be active
    }

    /** @test */
    public function test_cannot_close_run_with_mismatched_defect_qty(): void
    {
        $mould = Mould::factory()->create(['cavities' => 2]);
        $machine = Machine::factory()->create();
        $run = ProductionRun::factory()->create([
            'mould_id' => $mould->id,
            'machine_id' => $machine->id,
            'cavities_snapshot' => 2,
            'end_ts' => null,
        ]);

        Livewire::test(\App\Livewire\Runs\Close::class, ['run' => $run])
            ->set('shot_total', 100)
            ->set('ok_part', 180)
            ->set('ng_part', 20) // ng_part is 20
            ->set('defects', [
                ['defect_code' => 'FLASH', 'qty' => 10],
                ['defect_code' => 'SHORT', 'qty' => 5], // Total is 15, not 20
            ])
            ->call('closeRun')
            ->assertHasErrors('ng_part');

        $run->refresh();
        $this->assertNull($run->end_ts);
    }

    /** @test */
    public function test_cavities_snapshot_auto_populated(): void
    {
        $mould = Mould::factory()->create(['cavities' => 8]);
        $machine = Machine::factory()->create();

        $run = ProductionRun::create([
            'mould_id' => $mould->id,
            'machine_id' => $machine->id,
            'start_ts' => now(),
        ]);

        $this->assertEquals(8, $run->cavities_snapshot);
    }

    /** @test */
    public function test_mould_status_updated_after_run_close(): void
    {
        $mould = Mould::factory()->create(['cavities' => 4, 'status' => 'IN_RUN']);
        $machine = Machine::factory()->create();
        $run = ProductionRun::factory()->create([
            'mould_id' => $mould->id,
            'machine_id' => $machine->id,
            'cavities_snapshot' => 4,
            'end_ts' => null,
        ]);

        Livewire::test(\App\Livewire\Runs\Close::class, ['run' => $run])
            ->set('shot_total', 100)
            ->set('ok_part', 380)
            ->set('ng_part', 20)
            ->set('defects', [['defect_code' => 'FLASH', 'qty' => 20]])
            ->call('closeRun')
            ->assertHasNoErrors();

        $mould->refresh();
        $this->assertEquals('AVAILABLE', $mould->status);
    }

    /** @test */
    public function test_cannot_close_already_closed_run(): void
    {
        $run = ProductionRun::factory()->closed()->create();

        Livewire::test(\App\Livewire\Runs\Close::class, ['run' => $run])
            ->set('shot_total', 100)
            ->set('ok_part', 95)
            ->set('ng_part', 5)
            ->call('closeRun');

        // Run should remain closed, session should have error
        $run->refresh();
        $this->assertNotNull($run->end_ts); // Still closed
    }
}
