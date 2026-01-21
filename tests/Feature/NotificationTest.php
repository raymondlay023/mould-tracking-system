<?php

namespace Tests\Feature;

use App\Livewire\Maintenance\WorkOrders;
use App\Livewire\Partials\NotificationBell;
use App\Models\Mould;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_work_order_request_sends_notification()
    {
        Notification::fake();

        // 1. Create Recipient (Maintenance Team)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $recipient = User::factory()->create();
        \Spatie\Permission\Models\Permission::create(['name' => 'view_maintenance_section']);
        $recipient->givePermissionTo('view_maintenance_section');

        // DEBUG: Ensure scope works
        if (!User::permission('view_maintenance_section')->exists()) {
            $this->fail('Permission scope failed to find user.');
        }

        // 2. Create Sender (Operator)
        $sender = User::factory()->create();
        \Spatie\Permission\Models\Permission::create(['name' => 'create_maintenance_events']);
        $sender->givePermissionTo('create_maintenance_events');
        
        $mould = Mould::factory()->create();

        // 3. Trigger Creation
        Livewire::actingAs($sender)
            ->test(WorkOrders::class)
            ->call('create')
            ->set('newMouldId', $mould->id)
            ->set('newType', 'CM')
            ->set('newDescription', 'Test Notification')
            ->set('newStartTs', '2023-01-01T10:00')
            ->call('saveNew');

        // 4. Assert Notification Sent
        Notification::assertSentTo(
            $recipient,
            \App\Notifications\Maintenance\WorkOrderRequested::class
        );
    }

    public function test_notification_bell_shows_unread_count()
    {
        $user = User::factory()->create();
        $mould = Mould::factory()->create(['code' => 'M001']);
        
        // Manual Notification Insert
        $noti = new \App\Notifications\Maintenance\WorkOrderRequested(
            \App\Models\MaintenanceEvent::factory()->create(['mould_id' => $mould->id])
        );
        $user->notify($noti);

        // Assert Count
        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->assertSet('unreadCount', 1)
            ->assertSee('New Work Order')
            ->assertSee('M001');
    }
}
