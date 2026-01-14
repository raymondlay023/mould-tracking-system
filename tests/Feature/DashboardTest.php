<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\MaintenanceEvent;
use App\Models\Mould;
use App\Models\ProductionRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Viewer']);

        // Create and authenticate a viewer user
        $this->user = User::factory()->create();
        $this->user->assignRole('Viewer');
        $this->actingAs($this->user);
    }

    /** @test */
    public function test_dashboard_displays_active_runs(): void
    {
        $activeRun1 = ProductionRun::factory()->active()->create();
        $activeRun2 = ProductionRun::factory()->active()->create();
        $closedRun = ProductionRun::factory()->closed()->create();

        Livewire::test(\App\Livewire\Dashboard\Summary::class)
            ->assertSee($activeRun1->mould->code)
            ->assertSee($activeRun2->mould->code)
            ->assertDontSee($closedRun->mould->code);
    }

    /** @test */
    public function test_dashboard_shows_active_run_count(): void
    {
        ProductionRun::factory()->active()->count(5)->create();
        ProductionRun::factory()->closed()->count(10)->create();

        Livewire::test(\App\Livewire\Dashboard\Summary::class)
            ->assertViewHas('activeCount', 5);
    }

    /** @test */
    public function test_dashboard_shows_pm_due_moulds(): void
    {
        $mould = Mould::factory()->create();
        
        // Create a maintenance event with past due date
        MaintenanceEvent::factory()->pm()->create([
            'mould_id' => $mould->id,
            'next_due_date' => now()->subDays(5), // Overdue
        ]);

        Livewire::test(\App\Livewire\Dashboard\Summary::class)
            ->assertSee($mould->code)
            ->assertSee('OVERDUE');
    }

    /** @test */
    public function test_dashboard_shows_top_ng_metrics(): void
    {
        $mouldHighNg = Mould::factory()->create(['code' => 'HIGH-NG']);
        $mouldLowNg = Mould::factory()->create(['code' => 'LOW-NG']);

        // Create runs with different NG rates
        ProductionRun::factory()->closed()->create([
            'mould_id' => $mouldHighNg->id,
            'part_total' => 1000,
            'ok_part' => 800,
            'ng_part' => 200, // 20% NG
        ]);

        ProductionRun::factory()->closed()->create([
            'mould_id' => $mouldLowNg->id,
            'part_total' => 1000,
            'ok_part' => 990,
            'ng_part' => 10, // 1% NG
        ]);

        Livewire::test(\App\Livewire\Dashboard\Summary::class)
            ->assertSee('HIGH-NG')
            ->assertSee('LOW-NG'); // Both should appear, but HIGH-NG first
    }

    /** @test */
    public function test_dashboard_shows_top_cm_metrics(): void
    {
        $mouldFrequentCm = Mould::factory()->create(['code' => 'FREQ-CM']);
        $mouldRareCm = Mould::factory()->create(['code' => 'RARE-CM']);

        // Create multiple CM events
        MaintenanceEvent::factory()->cm()->count(5)->create([
            'mould_id' => $mouldFrequentCm->id,
        ]);

        MaintenanceEvent::factory()->cm()->create([
            'mould_id' => $mouldRareCm->id,
        ]);

        Livewire::test(\App\Livewire\Dashboard\Summary::class)
            ->assertSee('FREQ-CM')
            ->assertSee('RARE-CM');
    }

    /** @test */
    public function test_can_export_top_ng_to_excel(): void
    {
        $mould = Mould::factory()->create();
        ProductionRun::factory()->closed()->create([
            'mould_id' => $mould->id,
            'ng_part' => 50,
        ]);

        $response = Livewire::test(\App\Livewire\Dashboard\Summary::class)
            ->call('exportTopNg');

        $this->assertTrue($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse);
    }

    /** @test */
    public function test_can_export_top_cm_to_excel(): void
    {
        $mould = Mould::factory()->create();
        MaintenanceEvent::factory()->cm()->create([
            'mould_id' => $mould->id,
        ]);

        $response = Livewire::test(\App\Livewire\Dashboard\Summary::class)
            ->call('exportTopCm');

        $this->assertTrue($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse);
    }
}
