<?php

namespace Tests\Feature;

use App\Livewire\Maintenance\Index;
use App\Models\MaintenanceEvent;
use App\Models\Mould;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MaintenanceFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_completed_maintenance_log()
    {
        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::create(['name' => 'create_maintenance_events']);
        $user->givePermissionTo('create_maintenance_events');
        $mould = Mould::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('mould_id', $mould->id)
            ->set('start_ts', now()->subHour()->format('Y-m-d\TH:i'))
            ->set('end_ts', now()->format('Y-m-d\TH:i'))
            ->set('type', 'PM')
            ->set('downtime_min', 60)
            ->set('cost', 100)
            ->set('description', 'Routine Check')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('maintenance_events', [
            'mould_id' => $mould->id,
            'status' => 'COMPLETED',
            'type' => 'PM',
            'downtime_min' => 60,
            'cost' => 100,
        ]);
    }

    public function test_validation_always_requires_completion_fields()
    {
        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::create(['name' => 'create_maintenance_events']);
        $user->givePermissionTo('create_maintenance_events');
        $mould = Mould::factory()->create();
        
        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('mould_id', $mould->id)
            ->set('start_ts', '2023-01-01 10:00')
            ->set('end_ts', '') // Empty
            ->set('downtime_min', '') // Empty
            ->call('save')
            ->assertHasErrors(['end_ts', 'downtime_min']);
    }
}
