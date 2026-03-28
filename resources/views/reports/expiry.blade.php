<x-app-layout>
    <x-slot name="header">Expiry Report</x-slot>

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
                <a href="{{ route('reports.expiry', array_merge($filters, ['export' => 'csv'])) }}" class="w-full rounded-lg border border-emerald-300 px-4 py-2.5 text-center text-sm font-medium text-emerald-700 hover:bg-emerald-50 sm:w-auto">Export CSV</a>
            </div>

            <form method="GET" action="{{ route('reports.expiry') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
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
                    <label class="mb-1 block text-sm font-medium text-slate-700">From Date</label>
                    <input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-base sm:text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">To Date</label>
                    <input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-base sm:text-sm">
                </div>
                <div class="flex items-end gap-2 xl:justify-end">
                    <button class="h-11 w-full rounded-lg bg-slate-900 px-4 text-sm font-medium text-white sm:w-auto">Apply</button>
                    <a href="{{ route('reports.expiry') }}" class="h-11 w-full rounded-lg border border-slate-300 px-4 text-center text-sm leading-[2.75rem] sm:w-auto">Reset</a>
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
                            <th class="px-3 py-2">Batch</th>
                            <th class="px-3 py-2">Remaining</th>
                            <th class="px-3 py-2">Expiry Date</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            @php
                                $expiryDate = \Illuminate\Support\Carbon::parse($row->expiry_date);
                                $daysToExpiry = now()->diffInDays($expiryDate, false);
                            @endphp
                            <tr class="block border-b border-slate-100 py-2 md:table-row md:py-0">
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-900 md:table-cell md:py-3"><span class="text-xs uppercase text-slate-400 md:hidden">Product</span><span>{{ $row->product_name }}</span></td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3"><span class="text-xs uppercase text-slate-400 md:hidden">SKU</span><span>{{ $row->product_sku }}</span></td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3"><span class="text-xs uppercase text-slate-400 md:hidden">Category</span><span>{{ $row->category_name }}</span></td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3"><span class="text-xs uppercase text-slate-400 md:hidden">Batch</span><span>{{ $row->batch_number }}</span></td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3"><span class="text-xs uppercase text-slate-400 md:hidden">Remaining</span><span>{{ $row->remaining_quantity }}</span></td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3"><span class="text-xs uppercase text-slate-400 md:hidden">Expiry</span><span>{{ $expiryDate->format('d M Y') }}</span></td>
                                <td class="flex items-center justify-between px-3 py-1.5 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Status</span>
                                    @if($daysToExpiry < 0)
                                        <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">Expired</span>
                                    @elseif($daysToExpiry <= 30)
                                        <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-700">Expiring Soon</span>
                                    @else
                                        <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700">Valid</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-3 py-8 text-center text-slate-500">No expiry records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $rows->links() }}</div>
        </div>
    </div>
</x-app-layout>
