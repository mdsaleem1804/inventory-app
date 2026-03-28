<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function index()
    {
        return redirect()->route('reports.stock');
    }

    public function stock(Request $request)
    {
        $filters = $request->only(['category_id', 'product_id']);

        if ($request->get('export') === 'csv') {
            $rows = $this->reportService->stockReportCollection($filters);

            return $this->csvDownload('stock_report.csv', ['Product', 'SKU', 'Category', 'Current Stock', 'Minimum Stock', 'Low Stock'], $rows->map(function ($product) {
                return [
                    $product->name,
                    $product->sku,
                    $product->category?->name,
                    $product->current_stock,
                    $product->minimum_stock,
                    $product->current_stock <= $product->minimum_stock ? 'YES' : 'NO',
                ];
            }));
        }

        $cacheKey = 'report.stock.' . md5(json_encode([
            'filters' => $filters,
            'page' => (int) $request->query('page', 1),
        ]));

        $products = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($filters) {
            return $this->reportService->stockReport($filters);
        });

        return view('reports.stock', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'productOptions' => Product::orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    public function lowStock(Request $request)
    {
        $filters = $request->only(['category_id', 'product_id']);

        if ($request->get('export') === 'csv') {
            $rows = $this->reportService->lowStockReportCollection($filters);

            return $this->csvDownload('low_stock_report.csv', ['Product', 'SKU', 'Current Stock', 'Minimum Stock'], $rows->map(function ($product) {
                return [
                    $product->name,
                    $product->sku,
                    $product->current_stock,
                    $product->minimum_stock,
                ];
            }));
        }

        $cacheKey = 'report.low_stock.' . md5(json_encode([
            'filters' => $filters,
            'page' => (int) $request->query('page', 1),
        ]));

        $products = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($filters) {
            return $this->reportService->lowStockReport($filters);
        });

        return view('reports.low_stock', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'productOptions' => Product::orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    public function profit(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'category_id', 'product_id', 'customer_id']);

        if ($request->get('export') === 'csv') {
            $rows = $this->reportService->profitReportCollection($filters);

            return $this->csvDownload('profit_report.csv', ['Invoice', 'Date', 'Customer', 'Product', 'Quantity', 'Sale Price', 'Cost Price', 'Sales', 'Cost', 'Profit'], $rows->map(function ($row) {
                return [
                    $row->invoice_number,
                    $row->invoice_date,
                    $row->customer_name,
                    $row->product_name,
                    $row->quantity,
                    $row->sale_price,
                    $row->cost_price,
                    $row->line_sales,
                    $row->line_cost,
                    $row->line_profit,
                ];
            }));
        }

        $report = $this->reportService->profitReport($filters);

        return view('reports.profit', [
            'items' => $report['items'],
            'summary' => $report['summary'],
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'productOptions' => Product::orderBy('name')->get(['id', 'name']),
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    private function csvDownload(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $stream = fopen('php://output', 'w');

            fputcsv($stream, $headers);

            foreach ($rows as $row) {
                fputcsv($stream, (array) $row);
            }

            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
