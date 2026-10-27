<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemTypeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

Route::middleware('platform')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('items', ItemController::class);
    Route::resource('item-types', ItemTypeController::class)->except(['create']);
    Route::get('locations/{location}/inventory', [LocationController::class, 'inventory'])->name('locations.inventory');
    Route::resource('locations', LocationController::class);
    Route::resource('assets', AssetController::class)->except(['index']);
    Route::get('stock/receive', [StockMovementController::class, 'receive'])->name('stock.receive.create');
    Route::post('stock/quick-item', [StockMovementController::class, 'quickStoreItem'])->name('stock.quick-item.store');
    Route::get('movements', [StockMovementController::class, 'index'])->name('movements.index');
    Route::get('movements/create', [StockMovementController::class, 'create'])->name('movements.create');
    Route::post('movements', [StockMovementController::class, 'store'])->name('movements.store');
    Route::get('movements/{movement}/edit', [StockMovementController::class, 'edit'])->name('movements.edit');
    Route::put('movements/{movement}', [StockMovementController::class, 'update'])->name('movements.update');
    Route::delete('movements/{movement}', [StockMovementController::class, 'destroy'])->name('movements.destroy');
    Route::get('movements/{movement}', [StockMovementController::class, 'show'])->name('movements.show');
    Route::get('audit', AuditLogController::class)->name('audit.index');
});
