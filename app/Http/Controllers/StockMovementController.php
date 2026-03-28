<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\BatchStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StockMovementController extends Controller
{
    public function __construct(private readonly BatchStockService $batchStockService)
    {
    }

    public function stockIndex()
    {
        $movements = StockMovement::with(['product', 'batch'])
            ->latest()
            ->paginate(20);

        return view('stock.index', compact('movements'));
    }

    public function index(Product $product)
    {
        $movements = $product->stockMovements()
            ->with('batch')
            ->latest()
            ->paginate(15);

        return view('stock-movements.index', compact('product', 'movements'));
    }

    public function create(Product $product, Request $request)
    {
        $type = strtoupper((string) $request->query('type', $request->route('type', 'IN')));

        if (! in_array($type, ['IN', 'OUT'], true)) {
            $type = 'IN';
        }

        return view('stock-movements.create', compact('product', 'type'));
    }

    public function store(Product $product, Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['IN', 'OUT'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'mrp' => ['nullable', 'numeric', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($product, $request, $validated) {
            $lockedProduct = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedProduct->is_batch_enabled) {
                if ($validated['type'] === 'OUT' && (int) $validated['quantity'] > (int) $lockedProduct->current_stock) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Insufficient stock. Current stock is ' . $lockedProduct->current_stock . '.',
                    ]);
                }

                StockMovement::create([
                    'product_id' => $lockedProduct->id,
                    'type' => $validated['type'],
                    'quantity' => (int) $validated['quantity'],
                    'reference' => $validated['reference'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);

                return;
            }

            if ($validated['type'] === 'IN') {
                if ($lockedProduct->has_expiry && empty($validated['expiry_date'])) {
                    throw ValidationException::withMessages([
                        'expiry_date' => 'Expiry date is required for this product.',
                    ]);
                }

                if ($lockedProduct->has_mrp && ! isset($validated['mrp'])) {
                    throw ValidationException::withMessages([
                        'mrp' => 'MRP is required for this product.',
                    ]);
                }

                $this->batchStockService->createInboundBatch(
                    product: $lockedProduct,
                    quantity: (int) $validated['quantity'],
                    costPrice: isset($validated['cost_price']) ? (float) $validated['cost_price'] : (float) $lockedProduct->cost_price,
                    userId: $request->user()->id,
                    reference: $validated['reference'] ?? 'manual:in',
                    notes: $validated['notes'] ?? 'Manual stock in',
                    expiryDate: $lockedProduct->has_expiry ? ($validated['expiry_date'] ?? null) : null,
                    mrp: $lockedProduct->has_mrp ? (float) ($validated['mrp'] ?? 0) : null,
                );

                return;
            }

            $this->batchStockService->consumeFifo(
                product: $lockedProduct,
                requiredQuantity: (int) $validated['quantity'],
                userId: $request->user()->id,
                reference: $validated['reference'] ?? 'manual:out',
                notes: $validated['notes'] ?? 'Manual stock out',
                enforceExpiry: (bool) $lockedProduct->has_expiry,
            );
        });

        return redirect()->route('products.stock-movements.index', $product)
            ->with('success', 'Stock movement recorded successfully.');
    }
}
