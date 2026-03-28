<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->withSum(['stockMovements as stock_in_total' => function ($query) {
                $query->where('type', 'IN');
            }], 'quantity')
            ->withSum(['stockMovements as stock_out_total' => function ($query) {
                $query->where('type', 'OUT');
            }], 'quantity')
            ->latest()
            ->paginate(12);

        return view('products.index', compact('products'));
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

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate($this->rules($product));
        $validated['is_active'] = $request->boolean('is_active');
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
        ];
    }
}
