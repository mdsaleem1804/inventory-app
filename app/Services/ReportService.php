<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\SaleItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReportService
{
    public function stockReport(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->stockBaseQuery($filters)
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function stockReportCollection(array $filters): Collection
    {
        return $this->stockBaseQuery($filters)
            ->orderBy('name')
            ->get();
    }

    public function lowStockReport(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $balanceExpression = $this->stockBalanceExpression();

        return $this->stockBaseQuery($filters)
            ->whereRaw($balanceExpression . ' <= products.minimum_stock')
            ->orderByRaw($balanceExpression . ' asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function lowStockReportCollection(array $filters): Collection
    {
        $balanceExpression = $this->stockBalanceExpression();

        return $this->stockBaseQuery($filters)
            ->whereRaw($balanceExpression . ' <= products.minimum_stock')
            ->orderByRaw($balanceExpression . ' asc')
            ->get();
    }

    public function profitReport(array $filters, int $perPage = 20): array
    {
        $query = $this->profitBaseQuery($filters);

        $items = (clone $query)
            ->selectRaw('sale_items.id, sales.invoice_number, sales.created_at as invoice_date, customers.name as customer_name, products.name as product_name, sale_items.quantity, sale_items.price as sale_price, sale_items.cost_price, sale_items.total as line_sales, sale_items.cost_total as line_cost, (sale_items.total - sale_items.cost_total) as line_profit')
            ->orderByDesc('sales.created_at')
            ->paginate($perPage)
            ->withQueryString();

        $summary = (clone $query)
            ->selectRaw('COALESCE(SUM(sale_items.total), 0) as total_sales_amount')
            ->selectRaw('COALESCE(SUM(sale_items.cost_total), 0) as total_cost')
            ->selectRaw('COALESCE(SUM(sale_items.total - sale_items.cost_total), 0) as total_profit')
            ->first();

        return [
            'items' => $items,
            'summary' => $summary,
        ];
    }

    public function profitReportCollection(array $filters): Collection
    {
        return $this->profitBaseQuery($filters)
            ->selectRaw('sales.invoice_number, sales.created_at as invoice_date, customers.name as customer_name, products.name as product_name, sale_items.quantity, sale_items.price as sale_price, sale_items.cost_price, sale_items.total as line_sales, sale_items.cost_total as line_cost, (sale_items.total - sale_items.cost_total) as line_profit')
            ->orderByDesc('sales.created_at')
            ->get();
    }

    public function batchStockReport(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->batchBaseQuery($filters)
            ->orderByRaw('CASE WHEN product_batches.expiry_date IS NULL THEN 1 ELSE 0 END ASC')
            ->orderBy('product_batches.expiry_date')
            ->orderBy('product_batches.created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function batchStockReportCollection(array $filters): Collection
    {
        return $this->batchBaseQuery($filters)
            ->orderByRaw('CASE WHEN product_batches.expiry_date IS NULL THEN 1 ELSE 0 END ASC')
            ->orderBy('product_batches.expiry_date')
            ->orderBy('product_batches.created_at')
            ->get();
    }

    public function expiryReport(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->expiryBaseQuery($filters)
            ->orderBy('product_batches.expiry_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function expiryReportCollection(array $filters): Collection
    {
        return $this->expiryBaseQuery($filters)
            ->orderBy('product_batches.expiry_date')
            ->get();
    }

    public function mrpVsSellingReport(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->mrpBaseQuery($filters)
            ->orderByDesc('sales.created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function mrpVsSellingReportCollection(array $filters): Collection
    {
        return $this->mrpBaseQuery($filters)
            ->orderByDesc('sales.created_at')
            ->get();
    }

    private function stockBaseQuery(array $filters): Builder
    {
        return Product::query()
            ->with('category:id,name')
            ->withSum(['stockMovements as stock_in_total' => function ($query) {
                $query->where('type', 'IN');
            }], 'quantity')
            ->withSum(['stockMovements as stock_out_total' => function ($query) {
                $query->where('type', 'OUT');
            }], 'quantity')
            ->when(! empty($filters['category_id']), function (Builder $query) use ($filters) {
                $query->where('category_id', $filters['category_id']);
            })
            ->when(! empty($filters['product_id']), function (Builder $query) use ($filters) {
                $query->where('id', $filters['product_id']);
            });
    }

    private function profitBaseQuery(array $filters): Builder
    {
        return SaleItem::query()
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->whereNull('sales.deleted_at')
            ->whereNull('sale_items.deleted_at')
            ->when(! empty($filters['from_date']), function ($query) use ($filters) {
                $query->whereDate('sales.created_at', '>=', $filters['from_date']);
            })
            ->when(! empty($filters['to_date']), function ($query) use ($filters) {
                $query->whereDate('sales.created_at', '<=', $filters['to_date']);
            })
            ->when(! empty($filters['product_id']), function ($query) use ($filters) {
                $query->where('sale_items.product_id', $filters['product_id']);
            })
            ->when(! empty($filters['customer_id']), function ($query) use ($filters) {
                $query->where('sales.customer_id', $filters['customer_id']);
            })
            ->when(! empty($filters['category_id']), function ($query) use ($filters) {
                $query->where('products.category_id', $filters['category_id']);
            });
    }

    private function stockBalanceExpression(): string
    {
        return "(
            (SELECT COALESCE(SUM(sm_in.quantity), 0)
             FROM stock_movements sm_in
             WHERE sm_in.product_id = products.id
               AND sm_in.type = 'IN')
            -
            (SELECT COALESCE(SUM(sm_out.quantity), 0)
             FROM stock_movements sm_out
             WHERE sm_out.product_id = products.id
               AND sm_out.type = 'OUT')
        )";
    }

    private function batchBaseQuery(array $filters): Builder
    {
        return ProductBatch::query()
            ->join('products', 'products.id', '=', 'product_batches.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->whereNull('product_batches.deleted_at')
            ->where('products.is_batch_enabled', true)
            ->where('product_batches.remaining_quantity', '>', 0)
            ->selectRaw('product_batches.id, product_batches.batch_number, product_batches.quantity, product_batches.remaining_quantity, product_batches.cost_price, product_batches.mrp, product_batches.expiry_date, products.id as product_id, products.name as product_name, products.sku as product_sku, categories.name as category_name')
            ->when(! empty($filters['category_id']), function ($query) use ($filters) {
                $query->where('products.category_id', $filters['category_id']);
            })
            ->when(! empty($filters['product_id']), function ($query) use ($filters) {
                $query->where('product_batches.product_id', $filters['product_id']);
            })
            ->when(! empty($filters['expiry_to']), function ($query) use ($filters) {
                $query->whereDate('product_batches.expiry_date', '<=', $filters['expiry_to']);
            })
            ->when(! empty($filters['only_expired']), function ($query) {
                $query->whereNotNull('product_batches.expiry_date')
                    ->whereDate('product_batches.expiry_date', '<', now()->toDateString());
            });
    }

    private function expiryBaseQuery(array $filters): Builder
    {
        return ProductBatch::query()
            ->join('products', 'products.id', '=', 'product_batches.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->whereNull('product_batches.deleted_at')
            ->where('products.is_batch_enabled', true)
            ->where('product_batches.remaining_quantity', '>', 0)
            ->whereNotNull('product_batches.expiry_date')
            ->selectRaw('product_batches.batch_number, product_batches.expiry_date, product_batches.remaining_quantity, products.name as product_name, products.sku as product_sku, categories.name as category_name')
            ->when(! empty($filters['category_id']), function ($query) use ($filters) {
                $query->where('products.category_id', $filters['category_id']);
            })
            ->when(! empty($filters['product_id']), function ($query) use ($filters) {
                $query->where('product_batches.product_id', $filters['product_id']);
            })
            ->when(! empty($filters['from_date']), function ($query) use ($filters) {
                $query->whereDate('product_batches.expiry_date', '>=', $filters['from_date']);
            })
            ->when(! empty($filters['to_date']), function ($query) use ($filters) {
                $query->whereDate('product_batches.expiry_date', '<=', $filters['to_date']);
            });
    }

    private function mrpBaseQuery(array $filters): Builder
    {
        return SaleItem::query()
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->leftJoin('sale_item_batches', function ($join) {
                $join->on('sale_item_batches.sale_item_id', '=', 'sale_items.id')
                    ->whereNull('sale_item_batches.deleted_at');
            })
            ->whereNull('sales.deleted_at')
            ->whereNull('sale_items.deleted_at')
            ->where('products.has_mrp', true)
            ->selectRaw('sale_items.id, sales.invoice_number, sales.created_at as invoice_date, products.name as product_name, sale_items.quantity, sale_items.price as sale_price, COALESCE(SUM(sale_item_batches.quantity * COALESCE(sale_item_batches.mrp, 0)) / NULLIF(SUM(sale_item_batches.quantity), 0), NULL) as mrp, (sale_items.price - COALESCE(SUM(sale_item_batches.quantity * COALESCE(sale_item_batches.mrp, 0)) / NULLIF(SUM(sale_item_batches.quantity), 0), 0)) as price_vs_mrp')
            ->when(! empty($filters['from_date']), function ($query) use ($filters) {
                $query->whereDate('sales.created_at', '>=', $filters['from_date']);
            })
            ->when(! empty($filters['to_date']), function ($query) use ($filters) {
                $query->whereDate('sales.created_at', '<=', $filters['to_date']);
            })
            ->when(! empty($filters['product_id']), function ($query) use ($filters) {
                $query->where('sale_items.product_id', $filters['product_id']);
            })
            ->groupBy('sale_items.id', 'sales.invoice_number', 'sales.created_at', 'products.name', 'sale_items.quantity', 'sale_items.price');
    }
}
