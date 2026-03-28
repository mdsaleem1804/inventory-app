<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
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
                'stock' => $product->current_stock,
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
                $currentStock = (int) $product->current_stock;

                if ($item['quantity'] > $currentStock) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for {$product->name}. Current stock is {$currentStock}."],
                    ]);
                }

                $lineTotal = (float) $item['quantity'] * (float) $item['price'];
                $totalAmount += $lineTotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $lineTotal,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'OUT',
                    'quantity' => $item['quantity'],
                    'reference' => 'sale:' . $sale->invoice_number,
                    'notes' => 'Sale invoice movement',
                    'created_by' => $request->user()->id,
                ]);
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
        $sale->load(['customer', 'items.product']);

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
