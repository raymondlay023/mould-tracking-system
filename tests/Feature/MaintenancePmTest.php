<?php

namespace Tests\Feature;

use App\Actions\Maintenance\CheckPmStatusAction;
use App\Models\Mould;
use App\Models\MaintenanceEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MaintenancePmTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_action_identifies_overdue_moulds()
    {
        $mould = Mould::factory()->create([
            'pm_interval_shot' => 1000,
            'total_shots' => 1200,
            'last_pm_at_shot' => 0,
        ]);

        $action = new CheckPmStatusAction();
        $due = $action->execute();

        $this->assertCount(1, $due);
        $this->assertEquals($mould->id, $due->first()->id);
        $this->assertEquals(200, $due->first()->overdue_by);
    }

    /** @test */
    public function test_action_ignores_not_due_moulds()
    {
        Mould::factory()->create([
            'pm_interval_shot' => 1000,
            'total_shots' => 800,
            'last_pm_at_shot' => 0,
        ]);

        $action = new CheckPmStatusAction();
        $due = $action->execute();

        $this->assertEmpty($due);
    }

    /** @test */
    public function test_command_creates_maintenance_ticket()
    {
        $mould = Mould::factory()->create([
            'code' => 'M001',
            'pm_interval_shot' => 1000,
            'total_shots' => 1500,
            'last_pm_at_shot' => 0,
        ]);

        Artisan::call('maint:check-pm-due');

        $this->assertDatabaseHas('maintenance_events', [
            'mould_id' => $mould->id,
            'type' => 'PM',
            'status' => 'REQUESTED',
        ]);
    }

    /** @test */
    public function test_command_prevents_duplicate_events()
    {
        $mould = Mould::factory()->create([
            'pm_interval_shot' => 1000,
            'total_shots' => 1500,
            'last_pm_at_shot' => 0,
        ]);

        // First run
        Artisan::call('maint:check-pm-due');
        $this->assertDatabaseCount('maintenance_events', 1);

    }

    /** @test */
    public function test_complete_work_order_resets_mould_counters()
    {
        $mould = Mould::factory()->create([
            'pm_interval_shot' => 1000,
            'total_shots' => 1200,
            'last_pm_at_shot' => 0,
            'status' => 'IN_MAINTENANCE', 
            // We verify that completion frees the mould
        ]);

        // 1. Create Ticket
        Artisan::call('maint:check-pm-due');
        $event = MaintenanceEvent::where('mould_id', $mould->id)->active()->first(); 
        
        if(!$event) $event = MaintenanceEvent::where('mould_id', $mould->id)->whereNull('end_ts')->first();

        $this->assertNotNull($event);

        // 2. Complete Ticket
        $action = new \App\Actions\Maintenance\CompleteWorkOrderAction();
        $action->execute($event, [
            'downtime_min' => 60,
            'performed_by' => 'Tester',
            'notes' => 'Done',
        ]);

        // 3. Verify
        $mould->refresh();
        $this->assertEquals(1200, $mould->last_pm_at_shot); 
        $this->assertEquals(\App\Enums\MouldStatus::AVAILABLE, $mould->status);
        
        $event->refresh();
        $this->assertEquals('COMPLETED', $event->status);
        $this->assertNotNull($event->end_ts);
    }
}
