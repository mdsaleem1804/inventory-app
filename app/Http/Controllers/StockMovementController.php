<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StockMovementController extends Controller
{
    public function stockIndex()
    {
        $movements = StockMovement::with('product')
            ->latest()
            ->paginate(20);

        return view('stock.index', compact('movements'));
    }

    public function index(Product $product)
    {
        $movements = $product->stockMovements()
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
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($product, $request, $validated) {
            $lockedProduct = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($validated['type'] === 'OUT' && $validated['quantity'] > $lockedProduct->current_stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Insufficient stock. Current stock is ' . $lockedProduct->current_stock . '.',
                ]);
            }

            StockMovement::create([
                'product_id' => $lockedProduct->id,
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);
        });

        return redirect()->route('products.stock-movements.index', $product)
            ->with('success', 'Stock movement recorded successfully.');
    }
}
