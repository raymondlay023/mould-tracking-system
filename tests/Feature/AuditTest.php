<?php

namespace Tests\Feature;

use App\Models\Mould;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Activity;
use Tests\TestCase;

class AuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_mould_changes_are_logged()
    {
        // 1. Setup User & Permission
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Create Mould
        $mould = Mould::factory()->create(['status' => 'AVAILABLE']);

        // 3. Update Mould
        $mould->update(['status' => 'IN_RUN']);

        // 4. Verification using Spatie ActivityLog
        // We expect TWO logs: 'created' and 'updated'. We want 'updated'.
        $log = \Spatie\Activitylog\Models\Activity::where('description', 'Updated mould')->latest()->firstOrFail();

        $this->assertEquals('Updated mould', $log->description);
        $this->assertEquals(get_class($mould), $log->subject_type);
        $this->assertEquals($mould->id, $log->subject_id);
        
        // properties['old'] and properties['attributes'] are populated by 'logOnlyDirty'
        $this->assertArrayHasKey('status', $log->properties['attributes']);
        $this->assertEquals('IN_RUN', $log->properties['attributes']['status']);
        
        $this->assertArrayHasKey('status', $log->properties['old']);
        $this->assertEquals('AVAILABLE', $log->properties['old']['status']);
    }

    public function test_audit_index_page_loads()
    {
        $admin = User::factory()->create();
        \Spatie\Permission\Models\Permission::create(['name' => 'view_admin_panel']);
        $admin->givePermissionTo('view_admin_panel');

        $this->actingAs($admin)
            ->get(route('audit.index'))
            ->assertOk()
            ->assertSee('Audit Log');
    }
}
