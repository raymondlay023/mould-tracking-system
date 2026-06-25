<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Mould;
use App\Models\ProductionRun;
use App\Models\MaintenanceEvent;
use App\Livewire\Mobile\History;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MobileHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_page_requires_auth()
    {
        $this->get(route('mobile.history'))
            ->assertRedirect(route('login'));
    }

    public function test_history_displays_activities_in_user_timezone()
    {
        // 1. Setup Spatie Permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        \Spatie\Permission\Models\Permission::findOrCreate('operations.access');
        \Spatie\Permission\Models\Permission::findOrCreate('production.view');
        \Spatie\Permission\Models\Permission::findOrCreate('maintenance.view');

        // 2. Create a user with a specific timezone (Asia/Jakarta is UTC+7)
        $user = User::factory()->create([
            'timezone' => 'Asia/Jakarta',
        ]);
        
        $user->givePermissionTo('operations.access');
        $user->givePermissionTo('production.view');
        $user->givePermissionTo('maintenance.view');

        $mould = Mould::factory()->create();

        // 3. Create a Production Run in UTC
        // 12:00 UTC = 19:00 Jakarta (UTC+7)
        $run = ProductionRun::factory()->create([
            'mould_id' => $mould->id,
            'end_ts' => '2026-06-25 12:00:00',
        ]);

        // 4. Create a Maintenance Event in UTC
        // 03:00 UTC = 10:00 Jakarta (UTC+7)
        $maintenance = MaintenanceEvent::factory()->create([
            'mould_id' => $mould->id,
            'type' => 'PM',
            'end_ts' => '2026-06-25 03:00:00',
            'status' => 'COMPLETED',
        ]);

        // 5. Test the Livewire component
        Livewire::actingAs($user)
            ->test(History::class)
            ->assertSee('19:00') // Jakarta time for production run end_ts
            ->assertSee('10:00') // Jakarta time for maintenance end_ts
            ->assertSee('Jun 25');
    }
}
