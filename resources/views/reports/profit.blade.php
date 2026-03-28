<x-app-layout>
    <x-slot name="header">Profit Report</x-slot>

    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                <div class="grid w-full grid-cols-1 gap-2 sm:flex sm:w-auto sm:flex-wrap">
                    <a href="{{ route('reports.stock') }}" class="rounded-lg px-3 py-2.5 text-center text-sm font-medium {{ request()->routeIs('reports.stock') ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700' }}">Stock</a>
                    <a href="{{ route('reports.low-stock') }}" class="rounded-lg px-3 py-2.5 text-center text-sm font-medium {{ request()->routeIs('reports.low-stock') ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700' }}">Low Stock</a>
                    <a href="{{ route('reports.profit') }}" class="rounded-lg px-3 py-2.5 text-center text-sm font-medium {{ request()->routeIs('reports.profit') ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700' }}">Profit</a>
                    <a href="{{ route('reports.batches') }}" class="rounded-lg px-3 py-2.5 text-center text-sm font-medium {{ request()->routeIs('reports.batches') ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700' }}">Batches</a>
                    <a href="{{ route('reports.expiry') }}" class="rounded-lg px-3 py-2.5 text-center text-sm font-medium {{ request()->routeIs('reports.expiry') ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700' }}">Expiry</a>
                    <a href="{{ route('reports.mrp') }}" class="rounded-lg px-3 py-2.5 text-center text-sm font-medium {{ request()->routeIs('reports.mrp') ? 'bg-slate-900 text-white' : 'border border-slate-300 text-slate-700' }}">MRP</a>
                </div>
                <a href="{{ route('reports.profit', array_merge($filters, ['export' => 'csv'])) }}" class="w-full rounded-lg border border-emerald-300 px-4 py-2.5 text-center text-sm font-medium text-emerald-700 hover:bg-emerald-50 sm:w-auto">Export CSV</a>
            </div>

            <form method="GET" action="{{ route('reports.profit') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">From Date</label>
                    <input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-base sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">To Date</label>
                    <input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-base sm:text-sm">
                </div>
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
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Customer</label>
                    <select name="customer_id" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-base sm:text-sm">
                        <option value="">All customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(($filters['customer_id'] ?? '') == $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2 md:col-span-2 xl:col-span-1">
                    <button class="h-11 w-full rounded-lg bg-slate-900 px-4 text-sm font-medium text-white sm:w-auto">Apply</button>
                    <a href="{{ route('reports.profit') }}" class="h-11 w-full rounded-lg border border-slate-300 px-4 text-center text-sm leading-[2.75rem] sm:w-auto">Reset</a>
                </div>
            </form>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Total Sales Amount</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">${{ number_format((float)($summary->total_sales_amount ?? 0), 2) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Total Cost</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">${{ number_format((float)($summary->total_cost ?? 0), 2) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Total Profit</p>
                <p class="mt-1 text-2xl font-bold {{ (($summary->total_profit ?? 0) >= 0) ? 'text-green-600' : 'text-red-600' }}">${{ number_format((float)($summary->total_profit ?? 0), 2) }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="hidden md:table-header-group">
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2">Invoice</th>
                            <th class="px-3 py-2">Date</th>
                            <th class="px-3 py-2">Customer</th>
                            <th class="px-3 py-2">Product</th>
                            <th class="px-3 py-2">Qty</th>
                            <th class="px-3 py-2">Sale Price</th>
                            <th class="px-3 py-2">Cost Price</th>
                            <th class="px-3 py-2">Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $row)
                            <tr class="block border-b border-slate-100 py-2 md:table-row md:py-0">
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-900 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Invoice</span>
                                    <span>{{ $row->invoice_number }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Date</span>
                                    <span>{{ \Illuminate\Support\Carbon::parse($row->invoice_date)->format('M d, Y') }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Customer</span>
                                    <span>{{ $row->customer_name }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Product</span>
                                    <span>{{ $row->product_name }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Qty</span>
                                    <span>{{ $row->quantity }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Sale Price</span>
                                    <span>${{ number_format((float)$row->sale_price, 2) }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Cost Price</span>
                                    <span>${{ number_format((float)$row->cost_price, 2) }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 font-semibold {{ ((float)$row->line_profit >= 0) ? 'text-green-600' : 'text-red-600' }} md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Profit</span>
                                    <span>${{ number_format((float)$row->line_profit, 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-3 py-8 text-center text-slate-500">No profit data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $items->links() }}</div>
        </div>
    </div>
</x-app-layout>
