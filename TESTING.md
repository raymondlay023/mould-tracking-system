# Testing Guide

## Overview

This document provides comprehensive testing documentation for the Mould Tracking System.

## Running Tests

### All Tests
```bash
php artisan test
```

### Specific Test Suite
```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Specific Test File
```bash
php artisan test tests/Feature/ProductionRunTest.php
```

### Specific Test Method
```bash
php artisan test --filter=test_can_close_production_run_with_valid_data
```

## Test Database

All tests use an in-memory SQLite database configured in `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

This ensures:
- Tests run fast
- No interference with development database
- Complete isolation between test runs

## Test Structure

### Feature Tests (`tests/Feature/`)

**ProductionRunTest.php** - Production run workflows
- Active run display
- Run closure validation
- Part/defect calculations
- Status transitions

**MaintenanceEventTest.php** - Maintenance management
- PM/CM event creation
- Timestamp validation
- Edit/delete operations
- Search functionality

**MouldManagementTest.php** - Mould CRUD
- Creation and validation
- Tonnage logic
- Duplicate code prevention
- Search functionality

**DashboardTest.php** - Dashboard metrics
- Active run display
- PM alerting
- Top NG/CM calculations
- Excel exports

### Unit Tests (`tests/Unit/`)

Currently minimal - expand as needed for isolated business logic testing.

## Database Factories

Factories create realistic test data:

```php
// Create a mould
$mould = Mould::factory()->create();

// Create with specific attributes
$mould = Mould::factory()->create(['cavities' => 8]);

// Create active production run
$run = ProductionRun::factory()->active()->create();

// Create closed production run
$run = ProductionRun::factory()->closed()->create();

// Create PM event
$event = MaintenanceEvent::factory()->pm()->create();
```

## Writing New Tests

### Test Class Template

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class YourTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);

        // Authenticate user
        $this->user = User::factory()->create();
        $this->user->assignRole('Admin');
        $this->actingAs($this->user);
    }

    /** @test */
    public function test_your_feature(): void
    {
        // Arrange
        // ... setup test data

        // Act
        // ... perform action

        // Assert
        $this->assertTrue(true);
    }
}
```

### Livewire Component Testing

```php
use Livewire\Livewire;

Livewire::test(\App\Livewire\Moulds\Index::class)
    ->set('code', 'MLD-001')
    ->set('name', 'Test Mould')
    ->call('save')
    ->assertHasNoErrors();
```

## Best Practices

1. **Use RefreshDatabase** - Always use this trait in Feature tests
2. **Create Required Roles** - Tests need roles before assigning to users
3. **Authenticate Users** - Most features require authentication
4. **Use Factories** - Never manually create test data
5. **Test Business Rules** - Focus on validation and business logic
6. **Descriptive Names** - Test method names should describe what they test
7. **Arrange-Act-Assert** - Follow this pattern for clarity

## Continuous Integration

Tests should be run in CI/CD pipeline:

```yaml
# Example GitHub Actions
- name: Run tests
  run: php artisan test --coverage --min=80
```

## Code Coverage

Ensure Xdebug is installed:

```bash
# Run with coverage
php artisan test --coverage

# With minimum coverage threshold
php artisan test --coverage --min=80
```

## Common Assertions

```php
// Database
$this->assertDatabaseHas('moulds', ['code' => 'MLD-001']);
$this->assertDatabaseMissing('moulds', ['id' => $id]);

// HTTP
$response->assertOk(); // 200
$response->assertStatus(201);
$response->assertRedirect('/dashboard');

// Livewire
->assertHasErrors('field_name');
->assertHasNoErrors();
->assertSee('text');
->assertSet('property', 'value');
```

## Troubleshooting

**Tests failing after migration**
```bash
php artisan migrate:fresh
php artisan test
```

**Factory errors**
```bash
composer dump-autoload
```

**Slow tests**
- Ensure using `:memory:` database
- Minimize database interactions
- Use object mocking where possible

---

**Last Updated**: January 2026
