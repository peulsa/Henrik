<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::middleware('platform')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('items', ItemController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('assets', AssetController::class)->except(['index', 'create']);
    Route::get('movements', [StockMovementController::class, 'index'])->name('movements.index');
    Route::get('movements/create', [StockMovementController::class, 'create'])->name('movements.create');
    Route::post('movements', [StockMovementController::class, 'store'])->name('movements.store');
    Route::get('movements/{movement}', [StockMovementController::class, 'show'])->name('movements.show');
    Route::get('audit', AuditLogController::class)->name('audit.index');
});
