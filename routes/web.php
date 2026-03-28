<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('sales', SaleController::class)->except(['edit', 'update']);
    Route::resource('purchases', PurchaseController::class)->except(['edit', 'update']);
    Route::resource('products', ProductController::class)->except(['show']);
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
        ->name('products.toggle-status');
    Route::get('products/{product}/stock-movements', [StockMovementController::class, 'index'])
        ->name('products.stock-movements.index');
    Route::get('products/{product}/stock-movements/create', [StockMovementController::class, 'create'])
        ->name('products.stock-movements.create');
    Route::post('products/{product}/stock-movements', [StockMovementController::class, 'store'])
        ->name('products.stock-movements.store');

    Route::get('products/{product}/stock-in', [StockMovementController::class, 'create'])
        ->defaults('type', 'IN')
        ->name('products.stock-in.create');
    Route::get('products/{product}/stock-out', [StockMovementController::class, 'create'])
        ->defaults('type', 'OUT')
        ->name('products.stock-out.create');

    Route::get('/stock', [StockMovementController::class, 'stockIndex'])->name('stock.index');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/stock', [ReportsController::class, 'stock'])->name('reports.stock');
    Route::get('/reports/low-stock', [ReportsController::class, 'lowStock'])->name('reports.low-stock');
    Route::get('/reports/profit', [ReportsController::class, 'profit'])->name('reports.profit');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
