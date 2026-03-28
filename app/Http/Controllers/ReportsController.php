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

    public function batches(Request $request)
    {
        $filters = $request->only(['category_id', 'product_id', 'expiry_to', 'only_expired']);

        if ($request->get('export') === 'csv') {
            $rows = $this->reportService->batchStockReportCollection($filters);

            return $this->csvDownload('batch_stock_report.csv', ['Batch Number', 'Product', 'SKU', 'Category', 'Quantity', 'Remaining', 'Cost Price', 'MRP', 'Expiry Date', 'Expiry Status'], $rows->map(function ($row) {
                $expiryStatus = 'No Expiry';

                if (! empty($row->expiry_date)) {
                    $expiryDate = \Illuminate\Support\Carbon::parse($row->expiry_date);
                    $daysToExpiry = now()->diffInDays($expiryDate, false);
                    $expiryStatus = $expiryDate->isPast() ? 'Expired' : ($daysToExpiry <= 30 ? 'Expiring Soon' : 'Valid');
                }

                return [
                    $row->batch_number,
                    $row->product_name,
                    $row->product_sku,
                    $row->category_name,
                    $row->quantity,
                    $row->remaining_quantity,
                    $row->cost_price,
                    $row->mrp,
                    $row->expiry_date,
                    $expiryStatus,
                ];
            }));
        }

        $batches = $this->reportService->batchStockReport($filters);

        return view('reports.batches', [
            'batches' => $batches,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'productOptions' => Product::orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    public function expiry(Request $request)
    {
        $filters = $request->only(['category_id', 'product_id', 'from_date', 'to_date']);

        if ($request->get('export') === 'csv') {
            $rows = $this->reportService->expiryReportCollection($filters);

            return $this->csvDownload('expiry_report.csv', ['Product', 'SKU', 'Category', 'Batch', 'Remaining', 'Expiry Date'], $rows->map(function ($row) {
                return [
                    $row->product_name,
                    $row->product_sku,
                    $row->category_name,
                    $row->batch_number,
                    $row->remaining_quantity,
                    $row->expiry_date,
                ];
            }));
        }

        $rows = $this->reportService->expiryReport($filters);

        return view('reports.expiry', [
            'rows' => $rows,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'productOptions' => Product::orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    public function mrp(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'product_id']);

        if ($request->get('export') === 'csv') {
            $rows = $this->reportService->mrpVsSellingReportCollection($filters);

            return $this->csvDownload('mrp_vs_selling_report.csv', ['Invoice', 'Date', 'Product', 'Qty', 'Selling Price', 'MRP', 'Diff (Selling - MRP)'], $rows->map(function ($row) {
                return [
                    $row->invoice_number,
                    $row->invoice_date,
                    $row->product_name,
                    $row->quantity,
                    $row->sale_price,
                    $row->mrp,
                    $row->price_vs_mrp,
                ];
            }));
        }

        $rows = $this->reportService->mrpVsSellingReport($filters);

        return view('reports.mrp', [
            'rows' => $rows,
            'productOptions' => Product::orderBy('name')->get(['id', 'name']),
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
