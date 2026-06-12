<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role as SpatieRole;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        SpatieRole::create(['name' => Role::Admin->value]);
    }

    public function test_admin_can_access_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Admin->value);
        $user->refresh();

        $response = $this->actingAs($user)->get(route('admin.index'));
        $response->assertStatus(200);
    }

    public function test_dashboard_component_renders()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::Admin->value);
        $user->refresh();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Admin\Dashboard::class)
            ->assertStatus(200);
    }
}
