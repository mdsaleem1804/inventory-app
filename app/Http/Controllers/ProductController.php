<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->integer('category_id');
        $status = $request->query('status', 'all');
        $lowStock = $request->boolean('low_stock');
        $sort = (string) $request->query('sort', 'created_at');
        $direction = strtolower((string) $request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = [
            'name' => 'name',
            'sku' => 'sku',
            'price' => 'price',
            'minimum_stock' => 'minimum_stock',
            'is_active' => 'is_active',
            'created_at' => 'created_at',
        ];

        $sortColumn = $allowedSorts[$sort] ?? 'created_at';

        $products = Product::query()
            ->with('category')
            ->withSum(['stockMovements as stock_in_total' => function ($query) {
                $query->where('type', 'IN');
            }], 'quantity')
            ->withSum(['stockMovements as stock_out_total' => function ($query) {
                $query->where('type', 'OUT');
            }], 'quantity')
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($categoryId > 0, function (Builder $query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when(in_array($status, ['active', 'inactive'], true), function (Builder $query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->when($lowStock, function (Builder $query) {
                $query->havingRaw('(COALESCE(stock_in_total, 0) - COALESCE(stock_out_total, 0)) <= minimum_stock');
            })
            ->orderBy($sortColumn, $direction)
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact(
            'products',
            'categories',
            'search',
            'categoryId',
            'status',
            'lowStock',
            'sort',
            'direction'
        ));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_batch_enabled'] = $request->boolean('is_batch_enabled');
        $validated['has_expiry'] = $validated['is_batch_enabled'] && $request->boolean('has_expiry');
        $validated['has_mrp'] = $validated['is_batch_enabled'] && $request->boolean('has_mrp');
        $validated['created_by'] = $request->user()->id;
        $validated['updated_by'] = $request->user()->id;

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load([
            'category',
            'batches' => function ($query) {
                $query->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END ASC')
                    ->orderBy('expiry_date')
                    ->orderBy('created_at');
            },
            'stockMovements' => function ($query) {
                $query->with('batch')->latest()->limit(20);
            },
            'saleItems' => function ($query) {
                $query->with('sale')->latest()->limit(20);
            },
            'purchaseItems' => function ($query) {
                $query->with('purchase')->latest()->limit(20);
            },
        ]);

        return view('products.show', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate($this->rules($product));
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_batch_enabled'] = $request->boolean('is_batch_enabled');
        $validated['has_expiry'] = $validated['is_batch_enabled'] && $request->boolean('has_expiry');
        $validated['has_mrp'] = $validated['is_batch_enabled'] && $request->boolean('has_mrp');
        $validated['updated_by'] = $request->user()->id;

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => ! $product->is_active,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product status updated successfully.');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'action' => ['required', Rule::in(['activate', 'deactivate', 'delete'])],
        ]);

        $products = Product::query()->whereIn('id', $validated['product_ids']);

        if ($validated['action'] === 'delete') {
            if (! $request->user()->isAdmin()) {
                return redirect()->route('products.index')
                    ->with('error', 'Only Admin can delete products.');
            }

            $deletedCount = (clone $products)->count();
            $products->delete();

            return redirect()->route('products.index')
                ->with('success', "{$deletedCount} products deleted successfully.");
        }

        $isActive = $validated['action'] === 'activate';
        $updatedCount = $products->update([
            'is_active' => $isActive,
            'updated_by' => Auth::id(),
        ]);

        $message = $isActive ? 'activated' : 'deactivated';

        return redirect()->route('products.index')
            ->with('success', "{$updatedCount} products {$message} successfully.");
    }

    private function rules(?Product $product = null): array
    {
        $productId = $product?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)],
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'barcode')->ignore($productId),
            ],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', Rule::in(['pcs', 'kg', 'box'])],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'is_batch_enabled' => ['nullable', 'boolean'],
            'has_expiry' => ['nullable', 'boolean'],
            'has_mrp' => ['nullable', 'boolean'],
        ];
    }
}
