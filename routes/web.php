<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Imports\MouldImport;
use App\Livewire\Moulds\Index as MouldIndex;
use App\Livewire\Moulds\Show as MouldShow;
use App\Livewire\Audit\Index as AuditIndex;
use App\Livewire\Qr\MouldQrBatch;
use Illuminate\Support\Facades\Route;

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
    });

    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/import/moulds', MouldImport::class)->name('import.moulds');
    });

    Route::middleware(['role:Admin|Production|Maintenance|QA|Viewer'])->group(function () {
        Route::get('/moulds/{mould}', MouldShow::class)->name('moulds.show');
    });

    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/qr/moulds', MouldQrBatch::class)->name('qr.moulds');
    });

    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/audit', AuditIndex::class)->name('audit.index');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
