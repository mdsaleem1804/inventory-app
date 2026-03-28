<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Observers\AuditableObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $observer = AuditableObserver::class;

        Category::observe($observer);
        Product::observe($observer);
        StockMovement::observe($observer);
        Customer::observe($observer);
        Supplier::observe($observer);
        Sale::observe($observer);
        SaleItem::observe($observer);
        Purchase::observe($observer);
        PurchaseItem::observe($observer);
    }
}
