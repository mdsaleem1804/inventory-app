<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Services\ExportService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReportsController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
        private readonly ExportService $exportService
    )
    {
    }

    public function index()
    {
        return redirect()->route('reports.stock');
    }

    public function stock(Request $request)
    {
        $filters = $request->only(['category_id', 'product_id']);

        if ($response = $this->dispatchExport($request, $this->stockExportData($filters), 'stock_report')) {
            return $response;
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

        if ($response = $this->dispatchExport($request, $this->lowStockExportData($filters), 'low_stock_report')) {
            return $response;
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

        if ($response = $this->dispatchExport($request, $this->profitExportData($filters), 'profit_report')) {
            return $response;
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

        if ($response = $this->dispatchExport($request, $this->batchExportData($filters), 'batch_stock_report')) {
            return $response;
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

        if ($response = $this->dispatchExport($request, $this->expiryExportData($filters), 'expiry_report')) {
            return $response;
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

        if ($response = $this->dispatchExport($request, $this->mrpExportData($filters), 'mrp_vs_selling_report')) {
            return $response;
        }

        $rows = $this->reportService->mrpVsSellingReport($filters);

        return view('reports.mrp', [
            'rows' => $rows,
            'productOptions' => Product::orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    private function dispatchExport(Request $request, array $payload, string $baseFilename)
    {
        $export = (string) $request->query('export', '');

        if (! in_array($export, ['pdf', 'excel', 'print'], true)) {
            return null;
        }

        return match ($export) {
            'pdf' => $this->exportService->exportToPdf([
                ...$payload,
                'filename' => $baseFilename . '.pdf',
            ], 'reports.exports.table'),
            'excel' => $this->exportService->exportToExcel($payload, $baseFilename . '.xlsx'),
            'print' => $this->exportService->exportToPrint('reports.exports.table', $payload),
        };
    }

    private function stockExportData(array $filters): array
    {
        $rows = $this->reportService->stockReportCollection($filters)->map(function ($product) {
            return [
                $product->name,
                $product->sku,
                $product->category?->name,
                $product->current_stock,
                $product->minimum_stock,
                $product->current_stock <= $product->minimum_stock ? 'YES' : 'NO',
            ];
        })->all();

        return $this->reportService->structuredReportData(
            'Stock Report',
            ['Product', 'SKU', 'Category', 'Current Stock', 'Minimum Stock', 'Low Stock'],
            $rows,
            $filters
        );
    }

    private function lowStockExportData(array $filters): array
    {
        $rows = $this->reportService->lowStockReportCollection($filters)->map(function ($product) {
            return [
                $product->name,
                $product->sku,
                $product->current_stock,
                $product->minimum_stock,
            ];
        })->all();

        return $this->reportService->structuredReportData(
            'Low Stock Report',
            ['Product', 'SKU', 'Current Stock', 'Minimum Stock'],
            $rows,
            $filters
        );
    }

    private function profitExportData(array $filters): array
    {
        $rows = $this->reportService->profitReportCollection($filters)->map(function ($row) {
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
        })->all();

        return $this->reportService->structuredReportData(
            'Profit Report',
            ['Invoice', 'Date', 'Customer', 'Product', 'Quantity', 'Sale Price', 'Cost Price', 'Sales', 'Cost', 'Profit'],
            $rows,
            $filters
        );
    }

    private function batchExportData(array $filters): array
    {
        $rows = $this->reportService->batchStockReportCollection($filters)->map(function ($row) {
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
        })->all();

        return $this->reportService->structuredReportData(
            'Batch Stock Report',
            ['Batch Number', 'Product', 'SKU', 'Category', 'Quantity', 'Remaining', 'Cost Price', 'MRP', 'Expiry Date', 'Expiry Status'],
            $rows,
            $filters
        );
    }

    private function expiryExportData(array $filters): array
    {
        $rows = $this->reportService->expiryReportCollection($filters)->map(function ($row) {
            return [
                $row->product_name,
                $row->product_sku,
                $row->category_name,
                $row->batch_number,
                $row->remaining_quantity,
                $row->expiry_date,
            ];
        })->all();

        return $this->reportService->structuredReportData(
            'Expiry Report',
            ['Product', 'SKU', 'Category', 'Batch', 'Remaining', 'Expiry Date'],
            $rows,
            $filters
        );
    }

    private function mrpExportData(array $filters): array
    {
        $rows = $this->reportService->mrpVsSellingReportCollection($filters)->map(function ($row) {
            return [
                $row->invoice_number,
                $row->invoice_date,
                $row->product_name,
                $row->quantity,
                $row->sale_price,
                $row->mrp,
                $row->price_vs_mrp,
            ];
        })->all();

        return $this->reportService->structuredReportData(
            'MRP vs Selling Price Report',
            ['Invoice', 'Date', 'Product', 'Qty', 'Selling Price', 'MRP', 'Diff (Selling - MRP)'],
            $rows,
            $filters
        );
    }
}
