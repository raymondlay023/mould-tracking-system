<?php

namespace Tests\Feature;

use App\Enums\Permission as PermissionEnum;
use App\Models\Mould;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Mobile\Scanner;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class MobileAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_dashboard_loads()
    {
        Permission::create(['name' => PermissionEnum::AccessOperations->value]);
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionEnum::AccessOperations->value);

        $response = $this->actingAs($user)
            ->get(route('mobile.dashboard'));

        $response->assertOk();
        $response->assertSee('Hello, ' . $user->name);
    }

    public function test_scanner_loads()
    {
        Permission::firstOrCreate(['name' => PermissionEnum::AccessOperations->value]);
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionEnum::AccessOperations->value);

        $response = $this->actingAs($user)
            ->get(route('mobile.scanner'));

        $response->assertOk();
        $response->assertSee('Scan QR Code');
    }

    public function test_scanner_redirects_to_detail()
    {
        Permission::firstOrCreate(['name' => PermissionEnum::AccessOperations->value]);
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionEnum::AccessOperations->value);
        $mould = Mould::factory()->create();

        Livewire::actingAs($user)
            ->test(Scanner::class)
            // Simulate scanning "MOULD:{uuid}"
            ->call('handleScan', 'MOULD:' . $mould->id)
            ->assertRedirect(route('mobile.mould-detail', $mould));
    }

    public function test_mould_detail_loads()
    {
        Permission::firstOrCreate(['name' => PermissionEnum::AccessOperations->value]);
        $user = User::factory()->create();
        $user->givePermissionTo(PermissionEnum::AccessOperations->value);
        $mould = Mould::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('mobile.mould-detail', $mould));

        $response->assertOk();
        $response->assertSee($mould->code);
    }
}
