<x-app-layout>
    <x-slot name="header">Low Stock Report</x-slot>

    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                <div class="grid w-full grid-cols-1 gap-2 sm:flex sm:w-auto sm:flex-wrap">
                    <a href="{{ route('reports.stock') }}" class="rounded-lg px-3 py-2.5 text-center text-sm font-medium {{ request()->routeIs('reports.stock') ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700' }}">Stock</a>
                    <a href="{{ route('reports.low-stock') }}" class="rounded-lg px-3 py-2.5 text-center text-sm font-medium {{ request()->routeIs('reports.low-stock') ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700' }}">Low Stock</a>
                    <a href="{{ route('reports.profit') }}" class="rounded-lg px-3 py-2.5 text-center text-sm font-medium {{ request()->routeIs('reports.profit') ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700' }}">Profit</a>
                </div>
                <a href="{{ route('reports.low-stock', array_merge($filters, ['export' => 'csv'])) }}" class="w-full rounded-lg border border-emerald-300 px-4 py-2.5 text-center text-sm font-medium text-emerald-700 hover:bg-emerald-50 sm:w-auto">Export CSV</a>
            </div>

            <form method="GET" action="{{ route('reports.low-stock') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                    <select name="category_id" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-base sm:text-sm">
                        <option value="">All categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? '') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Product</label>
                    <select name="product_id" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-base sm:text-sm">
                        <option value="">All products</option>
                        @foreach($productOptions as $product)
                            <option value="{{ $product->id }}" @selected(($filters['product_id'] ?? '') == $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2 md:col-span-2 xl:col-span-2">
                    <button class="h-11 w-full rounded-lg bg-slate-900 px-4 text-sm font-medium text-white sm:w-auto">Apply</button>
                    <a href="{{ route('reports.low-stock') }}" class="h-11 w-full rounded-lg border border-slate-300 px-4 text-center text-sm leading-[2.75rem] sm:w-auto">Reset</a>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="hidden md:table-header-group">
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2">Product</th>
                            <th class="px-3 py-2">SKU</th>
                            <th class="px-3 py-2">Category</th>
                            <th class="px-3 py-2">Current Stock</th>
                            <th class="px-3 py-2">Minimum Stock</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr class="block border-b border-slate-100 py-2 md:table-row md:py-0">
                                <td class="flex items-center justify-between px-3 py-1.5 font-medium text-slate-900 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Product</span>
                                    <span>{{ $product->name }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">SKU</span>
                                    <span>{{ $product->sku }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Category</span>
                                    <span>{{ $product->category?->name }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-red-600 font-semibold md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Current</span>
                                    <span>{{ $product->current_stock }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Min</span>
                                    <span>{{ $product->minimum_stock }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Status</span>
                                    <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">Low Stock</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-8 text-center text-slate-500">No low-stock products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>
