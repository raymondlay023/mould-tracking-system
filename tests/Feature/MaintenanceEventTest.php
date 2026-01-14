<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\MaintenanceEvent;
use App\Models\Mould;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MaintenanceEventTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Maintenance']);

        // Create and authenticate a maintenance user
        $this->user = User::factory()->create();
        $this->user->assignRole('Maintenance');
        $this->actingAs($this->user);
    }

    /** @test */
    public function test_can_view_maintenance_index(): void
    {
        $response = $this->get(route('maintenance.index'));

        $response->assertOk();
        $response->assertSeeLivewire('maintenance.index');
    }

    /** @test */
    public function test_can_create_pm_event(): void
    {
        $mould = Mould::factory()->create();
        $startTs = now()->subHours(2)->format('Y-m-d\TH:i');
        $endTs = now()->format('Y-m-d\TH:i');

        Livewire::test(\App\Livewire\Maintenance\Index::class)
            ->set('mould_id', $mould->id)
            ->set('start_ts', $startTs)
            ->set('end_ts', $endTs)
            ->set('type', 'PM')
            ->set('downtime_min', 120)
            ->set('next_due_shot', 100000)
            ->set('next_due_date', now()->addDays(90)->format('Y-m-d'))
            ->set('performed_by', 'John Mechanic')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('maintenance_events', [
            'mould_id' => $mould->id,
            'type' => 'PM',
            'downtime_min' => 120,
            'next_due_shot' => 100000,
        ]);
    }

    /** @test */
    public function test_can_create_cm_event(): void
    {
        $mould = Mould::factory()->create();
        $startTs = now()->subHours(4)->format('Y-m-d\TH:i');
        $endTs = now()->subHours(1)->format('Y-m-d\TH:i');

        Livewire::test(\App\Livewire\Maintenance\Index::class)
            ->set('mould_id', $mould->id)
            ->set('start_ts', $startTs)
            ->set('end_ts', $endTs)
            ->set('type', 'CM')
            ->set('downtime_min', 180)
            ->set('description', 'Emergency repair')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('maintenance_events', [
            'mould_id' => $mould->id,
            'type' => 'CM',
            'downtime_min' => 180,
            'description' => 'Emergency repair',
        ]);
    }

    /** @test */
    public function test_end_ts_must_be_after_start_ts(): void
    {
        $mould = Mould::factory()->create();
        $startTs = now()->format('Y-m-d\TH:i');
        $endTs = now()->subHour()->format('Y-m-d\TH:i'); // End before start

        Livewire::test(\App\Livewire\Maintenance\Index::class)
            ->set('mould_id', $mould->id)
            ->set('start_ts', $startTs)
            ->set('end_ts', $endTs)
            ->set('type', 'PM')
            ->set('downtime_min', 60)
            ->call('save')
            ->assertHasErrors('end_ts');
    }

    /** @test */
    public function test_can_edit_maintenance_event(): void
    {
        $event = MaintenanceEvent::factory()->pm()->create();

        Livewire::test(\App\Livewire\Maintenance\Index::class)
            ->call('edit', $event->id)
            ->assertSet('idEdit', $event->id)
            ->assertSet('mould_id', $event->mould_id)
            ->assertSet('type', 'PM')
            ->set('downtime_min', 150) // Update downtime
            ->call('save')
            ->assertHasNoErrors();

        $event->refresh();
        $this->assertEquals(150, $event->downtime_min);
    }

    /** @test */
    public function test_can_delete_maintenance_event(): void
    {
        $event = MaintenanceEvent::factory()->create();

        Livewire::test(\App\Livewire\Maintenance\Index::class)
            ->call('delete', $event->id);

        $this->assertDatabaseMissing('maintenance_events', [
            'id' => $event->id,
        ]);
    }

    /** @test */
    public function test_mould_id_must_exist(): void
    {
        $startTs = now()->subHours(2)->format('Y-m-d\TH:i');
        $endTs = now()->format('Y-m-d\TH:i');

        Livewire::test(\App\Livewire\Maintenance\Index::class)
            ->set('mould_id', 'non-existent-uuid')
            ->set('start_ts', $startTs)
            ->set('end_ts', $endTs)
            ->set('type', 'PM')
            ->set('downtime_min', 60)
            ->call('save')
            ->assertHasErrors('mould_id');
    }

    /** @test */
    public function test_search_filters_maintenance_events(): void
    {
        $mould1 = Mould::factory()->create(['code' => 'MLD-001']);
        $mould2 = Mould::factory()->create(['code' => 'MLD-002']);
        
        MaintenanceEvent::factory()->create(['mould_id' => $mould1->id]);
        MaintenanceEvent::factory()->create(['mould_id' => $mould2->id]);

        Livewire::test(\App\Livewire\Maintenance\Index::class)
            ->set('search', 'MLD-001')
            ->assertSee('MLD-001')
            ->assertDontSee('MLD-002');
    }
}
