<?php

namespace Tests\Feature;

use App\Models\Mould;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MouldManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);

        // Create and authenticate an admin user
        $this->user = User::factory()->create();
        $this->user->assignRole('Admin');
        $this->actingAs($this->user);
    }

    /** @test */
    public function test_can_view_moulds_index(): void
    {
        $response = $this->get(route('moulds.index'));

        $response->assertOk();
        $response->assertSeeLivewire('moulds.index');
    }

    /** @test */
    public function test_can_create_mould_with_valid_data(): void
    {
        Livewire::test(\App\Livewire\Moulds\Index::class)
            ->set('code', 'MLD-TEST-001')
            ->set('name', 'Test Mould')
            ->set('cavities', 4)
            ->set('customer', 'ACME Corp')
            ->set('resin', 'PP')
            ->set('min_tonnage_t', 150)
            ->set('max_tonnage_t', 300)
            ->set('pm_interval_shot', 100000)
            ->set('pm_interval_days', 90)
            ->set('status', 'AVAILABLE')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('moulds', [
            'code' => 'MLD-TEST-001',
            'name' => 'Test Mould',
            'cavities' => 4,
            'customer' => 'ACME Corp',
        ]);
    }

    /** @test */
    public function test_cannot_create_mould_with_duplicate_code(): void
    {
        Mould::factory()->create(['code' => 'MLD-DUP-001']);

        Livewire::test(\App\Livewire\Moulds\Index::class)
            ->set('code', 'MLD-DUP-001')
            ->set('name', 'Duplicate Mould')
            ->set('cavities', 2)
            ->set('status', 'AVAILABLE')
            ->call('save')
            ->assertHasErrors('code');
    }

    /** @test */
    public function test_min_tonnage_cannot_exceed_max_tonnage(): void
    {
        Livewire::test(\App\Livewire\Moulds\Index::class)
            ->set('code', 'MLD-TON-001')
            ->set('name', 'Tonnage Test Mould')
            ->set('cavities', 2)
            ->set('min_tonnage_t', 500)
            ->set('max_tonnage_t', 300) // Min > Max!
            ->set('status', 'AVAILABLE')
            ->call('save')
            ->assertHasErrors('min_tonnage_t');
    }

    /** @test */
    public function test_can_edit_mould(): void
    {
        $mould = Mould::factory()->create([
            'code' => 'MLD-EDIT-001',
            'name' => 'Original Name',
        ]);

        Livewire::test(\App\Livewire\Moulds\Index::class)
            ->call('edit', $mould->id)
            ->assertSet('mouldId', $mould->id)
            ->assertSet('code', 'MLD-EDIT-001')
            ->assertSet('name', 'Original Name')
            ->set('name', 'Updated Name')
            ->set('customer', 'New Customer')
            ->call('save')
            ->assertHasNoErrors();

        $mould->refresh();
        $this->assertEquals('Updated Name', $mould->name);
        $this->assertEquals('New Customer', $mould->customer);
    }

    /** @test */
    public function test_can_delete_mould(): void
    {
        $mould = Mould::factory()->create();

        Livewire::test(\App\Livewire\Moulds\Index::class)
            ->call('delete', $mould->id);

        $this->assertDatabaseMissing('moulds', [
            'id' => $mould->id,
        ]);
    }

    /** @test */
    public function test_can_search_moulds(): void
    {
        Mould::factory()->create(['code' => 'MLD-001', 'name' => 'Alpha Mould']);
        Mould::factory()->create(['code' => 'MLD-002', 'name' => 'Beta Mould']);

        Livewire::test(\App\Livewire\Moulds\Index::class)
            ->set('search', 'Alpha')
            ->assertSee('MLD-001')
            ->assertDontSee('MLD-002');
    }

    /** @test */
    public function test_cavities_must_be_at_least_one(): void
    {
        Livewire::test(\App\Livewire\Moulds\Index::class)
            ->set('code', 'MLD-CAV-001')
            ->set('name', 'Cavity Test')
            ->set('cavities', 0) // Invalid
            ->set('status', 'AVAILABLE')
            ->call('save')
            ->assertHasErrors('cavities');
    }
}
