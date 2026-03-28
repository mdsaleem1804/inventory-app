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
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin,manager,staff')->group(function () {
        Route::resource('sales', SaleController::class)->except(['edit', 'update', 'destroy']);
    });

    Route::delete('sales/{sale}', [SaleController::class, 'destroy'])
        ->middleware('role:admin,manager')
        ->name('sales.destroy');

    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('customers', CustomerController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('purchases', PurchaseController::class)->except(['edit', 'update']);
        Route::resource('products', ProductController::class)->except(['destroy']);

        Route::post('products/bulk-action', [ProductController::class, 'bulkAction'])
            ->name('products.bulk-action');
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
        Route::get('/reports/batches', [ReportsController::class, 'batches'])->name('reports.batches');
        Route::get('/reports/expiry', [ReportsController::class, 'expiry'])->name('reports.expiry');
        Route::get('/reports/mrp', [ReportsController::class, 'mrp'])->name('reports.mrp');
    });

    Route::delete('products/{product}', [ProductController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('products.destroy');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/account', [UserManagementController::class, 'updateAccount'])->name('users.update-account');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.update-role');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
