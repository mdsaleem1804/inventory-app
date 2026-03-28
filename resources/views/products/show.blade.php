<x-app-layout>
    <x-slot name="header">Product Details</x-slot>

    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p class="text-xs uppercase text-slate-500">Product</p>
                    <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-slate-500">SKU</p>
                    <p class="font-semibold text-slate-900">{{ $product->sku }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-slate-500">Category</p>
                    <p class="font-semibold text-slate-900">{{ $product->category?->name }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-slate-500">Current Stock</p>
                    <p class="font-semibold text-slate-900">{{ $product->batches->sum('remaining_quantity') }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-semibold text-slate-900">Batch Inventory</h2>
                <a href="{{ route('reports.batches', ['product_id' => $product->id]) }}" class="rounded-lg border border-cyan-300 px-3 py-2 text-sm font-medium text-cyan-700 hover:bg-cyan-50">Open Batch Report</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2">Batch #</th>
                            <th class="px-3 py-2">Qty</th>
                            <th class="px-3 py-2">Remaining</th>
                            <th class="px-3 py-2">Cost Price</th>
                            <th class="px-3 py-2">Expiry</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->batches as $batch)
                            @php
                                $isExpired = $batch->expiry_date && $batch->expiry_date->isPast();
                                $isExpiringSoon = $batch->expiry_date && $batch->expiry_date->isFuture() && now()->diffInDays($batch->expiry_date, false) <= 30;
                            @endphp
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-3 font-medium text-slate-900">{{ $batch->batch_number }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $batch->quantity }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $batch->remaining_quantity }}</td>
                                <td class="px-3 py-3 text-slate-700">${{ number_format((float) $batch->cost_price, 2) }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $batch->expiry_date?->format('d M Y') ?? 'N/A' }}</td>
                                <td class="px-3 py-3">
                                    @if($isExpired)
                                        <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">Expired</span>
                                    @elseif($isExpiringSoon)
                                        <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-700">Expiring Soon</span>
                                    @else
                                        <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700">Healthy</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-slate-500">No batch records found for this product.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ route('products.index') }}" class="inline-block rounded-lg border border-slate-300 px-4 py-2 text-sm">Back to Products</a>
    </div>
</x-app-layout>
