<?php

use App\Http\Controllers\CycleCountController;
use App\Http\Controllers\InboundTransactionController;
use App\Http\Controllers\InventoryAdjustmentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OutboundTransactionController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SystemAlertController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Authentication (Breeze/Jetstream)
require __DIR__.'/auth.php';

// Protected Routes - Requires Login
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/', function () {
            return redirect()->route('items.index');
        });

        Route::resource('items', ItemController::class);
        Route::get('/low-stock', [ItemController::class, 'lowStock'])->name('items.low-stock');
        Route::get('/near-expiry', [ItemController::class, 'nearExpiry'])->name('items.near-expiry');

        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::resource('locations', LocationController::class)->except(['show']);
        Route::resource('purchase-orders', PurchaseOrderController::class)->except(['show']);
        Route::patch('/purchase-orders/{purchase_order}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
        Route::patch('/purchase-orders/{purchase_order}/reject', [PurchaseOrderController::class, 'reject'])->name('purchase-orders.reject');

        Route::resource('inbound', InboundTransactionController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::patch('/inbound/{inbound}/complete', [InboundTransactionController::class, 'complete'])->name('inbound.complete');
        Route::patch('/inbound/{inbound}/quality-check', [InboundTransactionController::class, 'qualityCheck'])->name('inbound.quality-check');

        Route::resource('outbound', OutboundTransactionController::class)->only(['index', 'create', 'store']);
        Route::patch('/outbound/{outbound}/approve', [OutboundTransactionController::class, 'approve'])->name('outbound.approve');
        Route::patch('/outbound/{outbound}/ship', [OutboundTransactionController::class, 'ship'])->name('outbound.ship');
        Route::patch('/outbound/{outbound}/deliver', [OutboundTransactionController::class, 'deliver'])->name('outbound.deliver');

        Route::resource('cycle-counts', CycleCountController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::resource('inventory-adjustments', InventoryAdjustmentController::class)->only(['index', 'create', 'store']);
        Route::resource('system-alerts', SystemAlertController::class)->only(['index', 'update']);
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('/archives', [ArchiveController::class, 'index'])->name('archives.index');
        Route::post('/archives/{type}/{id}/restore', [ArchiveController::class, 'restore'])->name('archives.restore');
    });

    Route::prefix('pos')->middleware('role:staff')->group(function () {
        Route::get('/', function () {
            return redirect()->route('sales.create');
        });

        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store']);
        Route::resource('sale-returns', SaleReturnController::class)->only(['index', 'create', 'store']);
        Route::get('/reports/daily', [SaleController::class, 'dailyReport'])->name('reports.daily');
        Route::get('/reports/monthly', [SaleController::class, 'monthlyReport'])->name('reports.monthly');
    });

});
