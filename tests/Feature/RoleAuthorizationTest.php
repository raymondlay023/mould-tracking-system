<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Mould;
use App\Models\ProductionRun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_viewer_cannot_close_run()
    {
        $user = User::factory()->create();
        $user->assignRole('Viewer');
        $user->refresh();
        
        $run = ProductionRun::factory()->create(['end_ts' => null]);

        // Debug assertions
        if (!$user->hasRole('Viewer')) {
             throw new \Exception('User failed to get Viewer role');
        }

        Livewire::actingAs($user)
            ->test(\App\Livewire\Runs\Close::class, ['run' => $run])
            ->call('closeRun')
            ->assertStatus(403);
    }
}
