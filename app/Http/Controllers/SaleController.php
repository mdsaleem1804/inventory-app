<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleItemBatch;
use App\Models\StockMovement;
use App\Services\BatchStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function __construct(private readonly BatchStockService $batchStockService)
    {
    }

    public function index()
    {
        $sales = Sale::with('customer')->latest()->paginate(12);

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = $this->productsWithStock();
        $productsData = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'is_batch_enabled' => (bool) $product->is_batch_enabled,
                'has_expiry' => (bool) $product->has_expiry,
                'has_mrp' => (bool) $product->has_mrp,
                'stock' => $product->is_batch_enabled
                    ? (int) ($product->batch_stock_total ?? 0)
                    : ((int) ($product->stock_in_total ?? 0) - (int) ($product->stock_out_total ?? 0)),
                'batches' => $product->batches->map(function ($batch) {
                    return [
                        'id' => $batch->id,
                        'batch_number' => $batch->batch_number,
                        'remaining_quantity' => (int) $batch->remaining_quantity,
                        'expiry_date' => $batch->expiry_date?->format('Y-m-d'),
                        'mrp' => $batch->mrp,
                    ];
                })->values(),
            ];
        })->values();

        return view('sales.create', compact('customers', 'productsData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', Rule::exists('customers', 'id')->whereNull('deleted_at')],
            'invoice_number' => ['required', 'string', 'max:100', 'unique:sales,invoice_number'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.batch_id' => ['nullable', 'exists:product_batches,id'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $sale = Sale::create([
                'customer_id' => $validated['customer_id'],
                'invoice_number' => $validated['invoice_number'],
                'total_amount' => 0,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);

            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::query()->whereKey($item['product_id'])->lockForUpdate()->firstOrFail();
                $allocations = collect();

                $lineTotal = (float) $item['quantity'] * (float) $item['price'];
                $totalAmount += $lineTotal;

                if ($product->is_batch_enabled) {
                    $allocations = $this->batchStockService->consumeFifo(
                        product: $product,
                        requiredQuantity: (int) $item['quantity'],
                        userId: $request->user()->id,
                        reference: 'sale:' . $sale->invoice_number,
                        notes: 'Sale invoice movement',
                        enforceExpiry: (bool) $product->has_expiry,
                        preferredBatchId: isset($item['batch_id']) ? (int) $item['batch_id'] : null,
                    );
                } else {
                    $currentStock = (int) $product->current_stock;

                    if ((int) $item['quantity'] > $currentStock) {
                        throw ValidationException::withMessages([
                            'items' => ["Insufficient stock for {$product->name}. Current stock is {$currentStock}."],
                        ]);
                    }

                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'OUT',
                        'quantity' => (int) $item['quantity'],
                        'reference' => 'sale:' . $sale->invoice_number,
                        'notes' => 'Sale invoice movement',
                        'created_by' => $request->user()->id,
                        'updated_by' => $request->user()->id,
                    ]);
                }

                $lineCostTotal = $product->is_batch_enabled
                    ? (float) $allocations->sum('line_cost')
                    : ((int) $item['quantity'] * (float) $product->cost_price);
                $lineCostPrice = $item['quantity'] > 0 ? $lineCostTotal / (int) $item['quantity'] : 0;

                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'cost_price' => $lineCostPrice,
                    'total' => $lineTotal,
                    'cost_total' => $lineCostTotal,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);

                if ($product->is_batch_enabled) {
                    foreach ($allocations as $allocation) {
                        SaleItemBatch::create([
                            'sale_item_id' => $saleItem->id,
                            'product_batch_id' => $allocation['batch']->id,
                            'quantity' => $allocation['quantity'],
                            'cost_price' => $allocation['cost_price'],
                            'mrp' => $allocation['mrp'],
                            'total_cost' => $allocation['line_cost'],
                            'created_by' => $request->user()->id,
                            'updated_by' => $request->user()->id,
                        ]);
                    }
                }
            }

            $sale->update([
                'total_amount' => $totalAmount,
                'updated_by' => $request->user()->id,
            ]);
        });

        return redirect()->route('sales.index')
            ->with('success', 'Sale invoice created successfully.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'items.product', 'items.batchAllocations.batch']);

        return view('sales.show', compact('sale'));
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Sale invoice deleted successfully.');
    }

    private function productsWithStock()
    {
        return Product::query()
            ->with(['batches' => function ($query) {
                $query->where('remaining_quantity', '>', 0)
                    ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END ASC')
                    ->orderBy('expiry_date')
                    ->orderBy('created_at');
            }])
            ->withSum('batches as batch_stock_total', 'remaining_quantity')
            ->withSum(['stockMovements as stock_in_total' => function ($query) {
                $query->where('type', 'IN');
            }], 'quantity')
            ->withSum(['stockMovements as stock_out_total' => function ($query) {
                $query->where('type', 'OUT');
            }], 'quantity')
            ->orderBy('name')
            ->get();
    }
}
