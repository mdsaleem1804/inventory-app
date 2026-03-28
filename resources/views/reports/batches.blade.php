<x-app-layout>
    <x-slot name="header">Batch Stock Report</x-slot>

    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
                <div class="grid w-full grid-cols-1 gap-2 sm:flex sm:w-auto sm:flex-wrap">
                    <a href="{{ route('reports.batches', array_merge($filters, ['export' => 'pdf'])) }}" class="w-full rounded-lg border border-rose-300 px-4 py-2.5 text-center text-sm font-medium text-rose-700 hover:bg-rose-50 sm:w-auto">Export PDF</a>
                    <a href="{{ route('reports.batches', array_merge($filters, ['export' => 'excel'])) }}" class="w-full rounded-lg border border-emerald-300 px-4 py-2.5 text-center text-sm font-medium text-emerald-700 hover:bg-emerald-50 sm:w-auto">Export Excel</a>
                    <a href="{{ route('reports.batches', array_merge($filters, ['export' => 'print'])) }}" target="_blank" class="w-full rounded-lg border border-sky-300 px-4 py-2.5 text-center text-sm font-medium text-sky-700 hover:bg-sky-50 sm:w-auto">Print</a>
                </div>
            </div>

            <form method="GET" action="{{ route('reports.batches') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
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
                    <label class="mb-1 block text-sm font-medium text-slate-700">Expiry On/Before</label>
                    <input type="date" name="expiry_to" value="{{ $filters['expiry_to'] ?? '' }}" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-base sm:text-sm">
                </div>
                <div class="flex items-center gap-2 pt-7">
                    <input id="only_expired" type="checkbox" name="only_expired" value="1" @checked(!empty($filters['only_expired'])) class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                    <label for="only_expired" class="text-sm text-slate-700">Only expired batches</label>
                </div>
                <div class="flex items-end gap-2 xl:justify-end">
                    <button class="h-11 w-full rounded-lg bg-slate-900 px-4 text-sm font-medium text-white sm:w-auto">Apply</button>
                    <a href="{{ route('reports.batches') }}" class="h-11 w-full rounded-lg border border-slate-300 px-4 text-center text-sm leading-[2.75rem] sm:w-auto">Reset</a>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="hidden md:table-header-group">
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2">Batch #</th>
                            <th class="px-3 py-2">Product</th>
                            <th class="px-3 py-2">SKU</th>
                            <th class="px-3 py-2">Category</th>
                            <th class="px-3 py-2">Qty</th>
                            <th class="px-3 py-2">Remaining</th>
                            <th class="px-3 py-2">Cost Price</th>
                            <th class="px-3 py-2">MRP</th>
                            <th class="px-3 py-2">Expiry</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            @php
                                $expiryDate = $batch->expiry_date ? \Illuminate\Support\Carbon::parse($batch->expiry_date) : null;
                                $daysToExpiry = $expiryDate ? now()->diffInDays($expiryDate, false) : null;
                            @endphp
                            <tr class="block border-b border-slate-100 py-2 md:table-row md:py-0">
                                <td class="flex items-center justify-between px-3 py-1.5 font-medium text-slate-900 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Batch</span>
                                    <span>{{ $batch->batch_number }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Product</span>
                                    <span>{{ $batch->product_name }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">SKU</span>
                                    <span>{{ $batch->product_sku }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Category</span>
                                    <span>{{ $batch->category_name }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Qty</span>
                                    <span>{{ $batch->quantity }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Remaining</span>
                                    <span>{{ $batch->remaining_quantity }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Cost</span>
                                    <span>${{ number_format((float) $batch->cost_price, 2) }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">MRP</span>
                                    <span>{{ $batch->mrp !== null ? '$' . number_format((float) $batch->mrp, 2) : 'N/A' }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 text-slate-700 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Expiry</span>
                                    <span>{{ $expiryDate?->format('d M Y') ?? 'N/A' }}</span>
                                </td>
                                <td class="flex items-center justify-between px-3 py-1.5 md:table-cell md:py-3">
                                    <span class="text-xs uppercase text-slate-400 md:hidden">Status</span>
                                    @if($expiryDate === null)
                                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">No Expiry</span>
                                    @elseif($expiryDate->isPast())
                                        <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">Expired</span>
                                    @elseif($daysToExpiry <= 30)
                                        <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-700">Expiring Soon</span>
                                    @else
                                        <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700">Valid</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="px-3 py-8 text-center text-slate-500">No batch stock records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $batches->links() }}</div>
        </div>
    </div>
</x-app-layout>
