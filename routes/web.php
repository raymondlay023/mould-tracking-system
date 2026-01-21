<?php

use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Alerts\PmDue;
use App\Livewire\Audit\Index as AuditIndex;
use App\Livewire\Dashboard\Summary;
use App\Livewire\Imports\MouldImport;
use App\Livewire\Locations\Move;
use App\Livewire\Machines\Index as MachineIndex;
use App\Livewire\Maintenance\Index as MaintenanceIndex;
use App\Livewire\Moulds\Index as MouldIndex;
use App\Livewire\Moulds\Show as MouldShow;
use App\Livewire\Plants\Index as PlantIndex;
use App\Livewire\Qr\MouldQrBatch;
use App\Livewire\Reports\MaintenanceDrilldown;
use App\Livewire\Reports\MaintenanceReport;
use App\Livewire\Reports\ProductionDrilldown;
use App\Livewire\Reports\ProductionReport;
use App\Livewire\Runs\Active as ActiveRuns;
use App\Livewire\Runs\Close as CloseRun;
use App\Livewire\Setups\Index as SetupIndex;
use App\Livewire\Trials\Index as TrialIndex;
use App\Livewire\Zones\Index as ZoneIndex;
use Illuminate\Support\Facades\Route;

// Health check endpoint (no authentication required)
Route::get('/health', HealthCheckController::class)->name('health');

Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {

    /**
     * Dashboard (batasi sesuai role yang kamu mau)
     * Kalau sudah pakai Gate: ganti middleware ini jadi ->middleware(['can:view-dashboard'])
     */
    Route::get('/dashboard', Summary::class)
        ->middleware(['can:view_main_dashboard'])
        ->name('dashboard');

    /**
     * Area landing per role
     */
    Route::get('/admin', \App\Livewire\Admin\Dashboard::class)
        ->middleware(['can:view_admin_panel'])
        ->name('admin.index');

    Route::get('/production', \App\Livewire\Production\Dashboard::class)
        ->middleware(['can:view_production_section'])
        ->name('production.index');

    Route::get('/maintenance', \App\Livewire\Maintenance\Dashboard::class)
        ->middleware(['can:view_maintenance_section'])
        ->name('maintenance.home');

    Route::get('/qa', \App\Livewire\Qa\Dashboard::class)
        ->middleware(['can:view_qa_section'])
        ->name('qa.index');

    /**
     * Mobile App (Shop Floor)
     */
    Route::prefix('app')->name('mobile.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Mobile\Dashboard::class)->name('dashboard');
        Route::get('/scan', \App\Livewire\Mobile\Scanner::class)->name('scanner');
        Route::get('/moulds/{mould}', \App\Livewire\Mobile\MouldDetail::class)->name('mould-detail');
    });

    /**
     * Operasional umum (semua role yang boleh operasional)
     */
    Route::middleware(['can:access_operations'])->group(function () {
        // Mould
        Route::get('/moulds', MouldIndex::class)->name('moulds.index');
        Route::get('/moulds/{mould}', MouldShow::class)->name('moulds.show');

        // Setup & Trial
        Route::get('/setups', SetupIndex::class)->name('setups.index');
        Route::get('/trials', TrialIndex::class)->name('trials.index');

        // Runs
        Route::get('/runs/active', ActiveRuns::class)->name('runs.active');
        Route::get('/runs/{run}/close', CloseRun::class)->name('runs.close');

        // Maintenance feature (CRUD event)
        Route::get('/maintenance/work-orders', \App\Livewire\Maintenance\WorkOrders::class)->name('maintenance.work-orders');
        Route::get('/maintenance/events', MaintenanceIndex::class)->name('maintenance.index');

        // Location move
        Route::get('/locations/move', Move::class)->name('locations.move');

        // Alerts
        Route::get('/alerts/pm-due', PmDue::class)->name('alerts.pm_due');

        // Reports
        Route::get('/reports/production', ProductionReport::class)->name('reports.production');
        Route::get('/reports/production/{group}/{id}', ProductionDrilldown::class)->name('reports.production.drilldown');

        Route::get('/reports/maintenance', MaintenanceReport::class)->name('reports.maintenance');
        Route::get('/reports/maintenance/{group}/{id}', MaintenanceDrilldown::class)->name('reports.maintenance.drilldown');
    });

    /**
     * Admin only (master data + tools)
     */
    Route::middleware(['can:view_admin_panel'])->group(function () {
        // Import & QR
        Route::get('/import/moulds', MouldImport::class)->name('import.moulds');
        Route::get('/qr/moulds', MouldQrBatch::class)->name('qr.moulds');

        // Audit
        Route::get('/audit', AuditIndex::class)->name('audit.index');

        // Master Data
        Route::get('/plants', PlantIndex::class)->name('plants.index');
        Route::get('/zones', ZoneIndex::class)->name('zones.index');
        Route::get('/machines', MachineIndex::class)->name('machines.index');
        Route::view('/users', 'users.index')->name('users.index'); // Placeholder for now
    });

    /**
     * Profile
     */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
