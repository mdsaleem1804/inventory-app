<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'dashboardData' => $this->dashboardData(),
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json($this->dashboardData());
    }

    private function dashboardData(): array
    {
        return Cache::remember('dashboard:analytics', now()->addSeconds(15), function () {
            $stockByProduct = DB::table('stock_movements as sm')
                ->selectRaw("sm.product_id,
                    SUM(CASE WHEN sm.type = 'IN' THEN sm.quantity ELSE 0 END) as stock_in,
                    SUM(CASE WHEN sm.type = 'OUT' THEN sm.quantity ELSE 0 END) as stock_out")
                ->groupBy('sm.product_id');

            $totalStock = (int) (DB::table('stock_movements')
                ->selectRaw("SUM(CASE WHEN type = 'IN' THEN quantity ELSE -quantity END) as stock_total")
                ->value('stock_total') ?? 0);

            $totalProducts = Product::query()->count();
            $totalSales = (float) Sale::query()->sum('total_amount');
            $totalPurchases = (float) Purchase::query()->sum('total_amount');

            $lowStockCount = DB::table('products as p')
                ->leftJoinSub($stockByProduct, 'stock', function ($join) {
                    $join->on('stock.product_id', '=', 'p.id');
                })
                ->whereRaw('(COALESCE(stock.stock_in, 0) - COALESCE(stock.stock_out, 0)) <= p.minimum_stock')
                ->count();

            $startDate = now()->subDays(11)->startOfDay();

            $salesByDay = Sale::query()
                ->selectRaw('DATE(created_at) as day, SUM(total_amount) as total')
                ->where('created_at', '>=', $startDate)
                ->groupBy('day')
                ->pluck('total', 'day');

            $purchasesByDay = Purchase::query()
                ->selectRaw('DATE(created_at) as day, SUM(total_amount) as total')
                ->where('created_at', '>=', $startDate)
                ->groupBy('day')
                ->pluck('total', 'day');

            $lineLabels = [];
            $lineSales = [];
            $linePurchases = [];

            foreach (range(0, 11) as $offset) {
                $day = now()->subDays(11 - $offset);
                $key = $day->format('Y-m-d');
                $lineLabels[] = $day->format('M d');
                $lineSales[] = round((float) ($salesByDay[$key] ?? 0), 2);
                $linePurchases[] = round((float) ($purchasesByDay[$key] ?? 0), 2);
            }

            $topProducts = DB::table('sale_items as si')
                ->join('products as p', 'p.id', '=', 'si.product_id')
                ->selectRaw('p.name as product_name, SUM(si.quantity) as total_qty')
                ->groupBy('p.id', 'p.name')
                ->orderByDesc('total_qty')
                ->limit(7)
                ->get();

            $stockByCategory = DB::table('categories as c')
                ->leftJoin('products as p', 'p.category_id', '=', 'c.id')
                ->leftJoinSub($stockByProduct, 'stock', function ($join) {
                    $join->on('stock.product_id', '=', 'p.id');
                })
                ->selectRaw('c.name as category_name, COALESCE(SUM(COALESCE(stock.stock_in, 0) - COALESCE(stock.stock_out, 0)), 0) as stock_total')
                ->groupBy('c.id', 'c.name')
                ->orderBy('c.name')
                ->get();

            return [
                'kpis' => [
                    'total_products' => $totalProducts,
                    'total_stock' => $totalStock,
                    'total_sales' => round($totalSales, 2),
                    'total_purchases' => round($totalPurchases, 2),
                    'low_stock_count' => (int) $lowStockCount,
                ],
                'charts' => [
                    'sales_vs_purchases' => [
                        'labels' => $lineLabels,
                        'sales' => $lineSales,
                        'purchases' => $linePurchases,
                    ],
                    'top_selling_products' => [
                        'labels' => $topProducts->pluck('product_name')->all(),
                        'values' => $topProducts->pluck('total_qty')->map(fn ($value) => (int) $value)->all(),
                    ],
                    'stock_by_category' => [
                        'labels' => $stockByCategory->pluck('category_name')->all(),
                        'values' => $stockByCategory->pluck('stock_total')->map(fn ($value) => (int) $value)->all(),
                    ],
                ],
                'generated_at' => now()->toDateTimeString(),
            ];
        });
    }
}
