<?php

namespace App\Services;

use App\Models\Product;
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
            ->selectRaw('sale_items.id, sales.invoice_number, sales.created_at as invoice_date, customers.name as customer_name, products.name as product_name, sale_items.quantity, sale_items.price as sale_price, products.cost_price, (sale_items.quantity * sale_items.price) as line_sales, (sale_items.quantity * products.cost_price) as line_cost, (sale_items.quantity * (sale_items.price - products.cost_price)) as line_profit')
            ->orderByDesc('sales.created_at')
            ->paginate($perPage)
            ->withQueryString();

        $summary = (clone $query)
            ->selectRaw('COALESCE(SUM(sale_items.quantity * sale_items.price), 0) as total_sales_amount')
            ->selectRaw('COALESCE(SUM(sale_items.quantity * products.cost_price), 0) as total_cost')
            ->selectRaw('COALESCE(SUM(sale_items.quantity * (sale_items.price - products.cost_price)), 0) as total_profit')
            ->first();

        return [
            'items' => $items,
            'summary' => $summary,
        ];
    }

    public function profitReportCollection(array $filters): Collection
    {
        return $this->profitBaseQuery($filters)
            ->selectRaw('sales.invoice_number, sales.created_at as invoice_date, customers.name as customer_name, products.name as product_name, sale_items.quantity, sale_items.price as sale_price, products.cost_price, (sale_items.quantity * sale_items.price) as line_sales, (sale_items.quantity * products.cost_price) as line_cost, (sale_items.quantity * (sale_items.price - products.cost_price)) as line_profit')
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
}
