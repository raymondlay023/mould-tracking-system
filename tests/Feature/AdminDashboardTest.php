<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        Role::create(['name' => 'Admin']);
    }

    public function test_admin_can_access_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $user->refresh();

        $response = $this->actingAs($user)->get(route('admin.index'));
        $response->assertStatus(200);
    }

    public function test_dashboard_component_renders()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $user->refresh();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Admin\Dashboard::class)
            ->assertStatus(200);
    }
}
