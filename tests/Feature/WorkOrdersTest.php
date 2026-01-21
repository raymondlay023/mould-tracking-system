<?php

namespace Tests\Feature;

use App\Livewire\Maintenance\WorkOrders;
use App\Models\MaintenanceEvent;
use App\Models\Mould;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WorkOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_work_order_request()
    {
        $user = User::factory()->create(['timezone' => 'Asia/Jakarta']);
        \Spatie\Permission\Models\Permission::create(['name' => 'create_maintenance_events']);
        $user->givePermissionTo('create_maintenance_events');
        $mould = Mould::factory()->create();

        // Simulate User Input: "2023-01-01 10:00" (Jakarta)
        Livewire::actingAs($user)
            ->test(WorkOrders::class)
            ->call('create') // Open Modal
            ->set('newMouldId', $mould->id)
            ->set('newType', 'CM')
            ->set('newDescription', 'Broken Pin')
            ->set('newStartTs', '2023-01-01T10:00')
            ->call('saveNew')
            ->assertHasNoErrors();

        // Verify creation in DB (UTC: 03:00)
        $this->assertDatabaseHas('maintenance_events', [
            'mould_id' => $mould->id,
            'status' => 'REQUESTED',
            'type' => 'CM',
            'description' => 'Broken Pin',
            'start_ts' => '2023-01-01 03:00:00', // UTC check
            'end_ts' => null,
        ]);
    }
}
