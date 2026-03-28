<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\BatchStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function __construct(private readonly BatchStockService $batchStockService)
    {
    }

    public function index()
    {
        $purchases = Purchase::with('supplier')->latest()->paginate(12);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $productsData = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'cost_price' => $product->cost_price,
                'is_batch_enabled' => (bool) $product->is_batch_enabled,
                'has_expiry' => (bool) $product->has_expiry,
                'has_mrp' => (bool) $product->has_mrp,
            ];
        })->values();

        return view('purchases.create', compact('suppliers', 'productsData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', Rule::exists('suppliers', 'id')->whereNull('deleted_at')],
            'invoice_number' => ['required', 'string', 'max:100', 'unique:purchases,invoice_number'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.cost_price' => ['required', 'numeric', 'min:0'],
            'items.*.expiry_date' => ['nullable', 'date'],
            'items.*.mrp' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $request): void {
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'invoice_number' => $validated['invoice_number'],
                'total_amount' => 0,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);

            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::query()->whereKey($item['product_id'])->lockForUpdate()->firstOrFail();
                $lineTotal = (float) $item['quantity'] * (float) $item['cost_price'];
                $totalAmount += $lineTotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'],
                    'total' => $lineTotal,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);

                if ($product->is_batch_enabled) {
                    if ($product->has_expiry && empty($item['expiry_date'])) {
                        throw ValidationException::withMessages([
                            'items' => ["Expiry date is required for {$product->name}."],
                        ]);
                    }

                    if ($product->has_mrp && ! isset($item['mrp'])) {
                        throw ValidationException::withMessages([
                            'items' => ["MRP is required for {$product->name}."],
                        ]);
                    }

                    $this->batchStockService->createInboundBatch(
                        product: $product,
                        quantity: (int) $item['quantity'],
                        costPrice: (float) $item['cost_price'],
                        userId: $request->user()->id,
                        reference: 'purchase:' . $purchase->invoice_number,
                        notes: 'Purchase invoice movement',
                        expiryDate: $product->has_expiry ? ($item['expiry_date'] ?? null) : null,
                        mrp: $product->has_mrp ? (float) ($item['mrp'] ?? 0) : null,
                    );

                    continue;
                }

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'IN',
                    'quantity' => (int) $item['quantity'],
                    'reference' => 'purchase:' . $purchase->invoice_number,
                    'notes' => 'Purchase invoice movement',
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);
            }

            $purchase->update([
                'total_amount' => $totalAmount,
                'updated_by' => $request->user()->id,
            ]);
        });

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase invoice created successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product']);

        return view('purchases.show', compact('purchase'));
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase invoice deleted successfully.');
    }
}
