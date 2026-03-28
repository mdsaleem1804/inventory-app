<x-app-layout>
    <x-slot name="header">Products</x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Product List</h2>
                <p class="text-sm text-slate-500">Track product details and toggle product status.</p>
            </div>
            <a href="{{ route('products.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Add Product</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="px-3 py-2 font-medium">Name</th>
                        <th class="px-3 py-2 font-medium">SKU</th>
                        <th class="px-3 py-2 font-medium">Category</th>
                        <th class="px-3 py-2 font-medium">Current Stock</th>
                        <th class="px-3 py-2 font-medium">Price</th>
                        <th class="px-3 py-2 font-medium">Status</th>
                        <th class="px-3 py-2 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="border-b border-slate-100">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $product->name }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $product->sku }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $product->category?->name }}</td>
                            <td class="px-3 py-3 font-semibold text-slate-900">{{ $product->current_stock }}</td>
                            <td class="px-3 py-3 text-slate-700">${{ number_format((float) $product->price, 2) }}</td>
                            <td class="px-3 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('products.show', $product) }}" class="rounded-md border border-cyan-300 px-3 py-1.5 text-xs font-medium text-cyan-700 hover:bg-cyan-50">View</a>
                                    <a href="{{ route('products.stock-in.create', ['product' => $product, 'type' => 'IN']) }}" class="rounded-md border border-green-300 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-50">Add Stock</a>
                                    <a href="{{ route('products.stock-out.create', ['product' => $product, 'type' => 'OUT']) }}" class="rounded-md border border-rose-300 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Remove Stock</a>
                                    <a href="{{ route('products.stock-movements.index', $product) }}" class="rounded-md border border-indigo-300 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50">History</a>
                                    <a href="{{ route('products.edit', $product) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100">Edit</a>

                                    <form method="POST" action="{{ route('products.toggle-status', $product) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-md border border-amber-300 px-3 py-1.5 text-xs font-medium text-amber-700 hover:bg-amber-50">
                                            {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-sm text-slate-500">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $products->links() }}</div>
    </div>
</x-app-layout>
