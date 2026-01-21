<?php

namespace Tests\Feature;

use App\Livewire\Maintenance\Index;
use App\Models\MaintenanceEvent;
use App\Models\Mould;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TimezoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_converts_user_input_to_utc()
    {
        // 1. Setup User in Jakarta (GMT+7)
        $user = User::factory()->create(['timezone' => 'Asia/Jakarta']);
        \Spatie\Permission\Models\Permission::create(['name' => 'create_maintenance_events']);
        $user->givePermissionTo('create_maintenance_events');
        $mould = Mould::factory()->create();

        // 2. Input: "2023-01-01 10:00" (Jakarta Time)
        // Expected DB (UTC): "2023-01-01 03:00"
        
        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('mould_id', $mould->id)
            ->set('type', 'PM')
            ->set('start_ts', '2023-01-01T10:00')
            ->set('end_ts', '2023-01-01T10:01') // 1 minute duration
             ->set('is_completed', true)
            ->set('downtime_min', 0)
            ->call('save')
            ->assertHasNoErrors();

        // 3. Verify DB is in UTC
        $event = MaintenanceEvent::first();
        // 10:00 Jakarta is 03:00 UTC
        $this->assertEquals('2023-01-01 03:00:00', $event->start_ts->format('Y-m-d H:i:s'));
    }

    public function test_displays_utc_as_user_time()
    {
        // 1. Setup Data in UTC: "2023-01-01 03:00"
        $mould = Mould::factory()->create();
        $event = MaintenanceEvent::create([
            'mould_id' => $mould->id,
            'type' => 'PM',
            'start_ts' => '2023-01-01 03:00:00', 
            'end_ts' => '2023-01-01 04:00:00',
            'status' => 'COMPLETED',
            'downtime_min' => 60,
        ]);

        // 2. Setup User in Jakarta
        $user = User::factory()->create(['timezone' => 'Asia/Jakarta']);
        $user->givePermissionTo(\Spatie\Permission\Models\Permission::create(['name' => 'create_maintenance_events']));

        // 3. Mount Component
        // Expected Display: "2023-01-01 10:00" (Jakarta)
        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('edit', $event->id)
            ->assertSet('start_ts', '2023-01-01T10:00')
            ->assertSet('end_ts', '2023-01-01T11:00');
    }
}
