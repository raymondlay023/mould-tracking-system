<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Audit\Index as AuditIndex;
use App\Livewire\Imports\MouldImport;
use App\Livewire\Machines\Index as MachineIndex;
use App\Livewire\Moulds\Index as MouldIndex;
use App\Livewire\Moulds\Show as MouldShow;
use App\Livewire\Plants\Index as PlantIndex;
use App\Livewire\Qr\MouldQrBatch;
use App\Livewire\Zones\Index as ZoneIndex;
use Illuminate\Support\Facades\Route;
use App\Livewire\Runs\Active as ActiveRuns;
use App\Livewire\Runs\Close as CloseRun;

Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::middleware(['role:Admin'])->group(function () {
        Route::view('/admin', 'admin.index')->name('admin.index');
    });

    Route::middleware(['role:Production|Admin'])->group(function () {
        Route::view('/production', 'production.index')->name('production.index');
    });

    Route::middleware(['role:Maintenance|Admin'])->group(function () {
        Route::view('/maintenance', 'maintenance.index')->name('maintenance.index');
    });

    Route::middleware(['role:QA|Admin'])->group(function () {
        Route::view('/qa', 'qa.index')->name('qa.index');
    });

    Route::middleware(['role:Admin|Production|Maintenance|QA|Viewer'])->group(function () {
        Route::get('/moulds', MouldIndex::class)->name('moulds.index');
        Route::get('/moulds/{mould}', MouldShow::class)->name('moulds.show');
    });

    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/import/moulds', MouldImport::class)->name('import.moulds');
    });

    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/qr/moulds', MouldQrBatch::class)->name('qr.moulds');
        Route::get('/audit', AuditIndex::class)->name('audit.index');
        Route::get('/plants', PlantIndex::class)->name('plants.index');
        Route::get('/zones', ZoneIndex::class)->name('zones.index');
        Route::get('/machines', MachineIndex::class)->name('machines.index');
    });

    Route::middleware(['auth', 'role:Admin|Production|Maintenance|QA|Viewer'])->group(function () {
        Route::get('/runs/active', ActiveRuns::class)->name('runs.active');
        Route::get('/runs/{run}/close', CloseRun::class)->name('runs.close');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
