<x-app-layout>
    <x-slot name="header">Product Details</x-slot>

    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
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
                    <p class="font-semibold text-slate-900">{{ $product->current_stock }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-slate-500">Status</p>
                    <p class="font-semibold {{ $product->is_active ? 'text-emerald-700' : 'text-rose-700' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</p>
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

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <h2 class="mb-3 text-lg font-semibold text-slate-900">Stock Movement History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2">Date</th>
                            <th class="px-3 py-2">Type</th>
                            <th class="px-3 py-2">Quantity</th>
                            <th class="px-3 py-2">Batch</th>
                            <th class="px-3 py-2">Reference</th>
                            <th class="px-3 py-2">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->stockMovements as $movement)
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-3 text-slate-700">{{ $movement->created_at?->format('d M Y H:i') }}</td>
                                <td class="px-3 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium {{ $movement->type === 'IN' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $movement->type }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 font-medium text-slate-900">{{ $movement->quantity }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $movement->batch?->batch_number ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $movement->reference ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $movement->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-slate-500">No stock movement history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <h2 class="mb-3 text-lg font-semibold text-slate-900">Sales History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2">Date</th>
                            <th class="px-3 py-2">Invoice</th>
                            <th class="px-3 py-2">Quantity</th>
                            <th class="px-3 py-2">Price</th>
                            <th class="px-3 py-2">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->saleItems as $saleItem)
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-3 text-slate-700">{{ $saleItem->created_at?->format('d M Y') }}</td>
                                <td class="px-3 py-3 font-medium text-slate-900">{{ $saleItem->sale?->invoice_number ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $saleItem->quantity }}</td>
                                <td class="px-3 py-3 text-slate-700">${{ number_format((float) $saleItem->price, 2) }}</td>
                                <td class="px-3 py-3 text-slate-700">${{ number_format((float) $saleItem->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-slate-500">No sales history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <h2 class="mb-3 text-lg font-semibold text-slate-900">Purchase History</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2">Date</th>
                            <th class="px-3 py-2">Invoice</th>
                            <th class="px-3 py-2">Quantity</th>
                            <th class="px-3 py-2">Cost Price</th>
                            <th class="px-3 py-2">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->purchaseItems as $purchaseItem)
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-3 text-slate-700">{{ $purchaseItem->created_at?->format('d M Y') }}</td>
                                <td class="px-3 py-3 font-medium text-slate-900">{{ $purchaseItem->purchase?->invoice_number ?? '-' }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $purchaseItem->quantity }}</td>
                                <td class="px-3 py-3 text-slate-700">${{ number_format((float) $purchaseItem->cost_price, 2) }}</td>
                                <td class="px-3 py-3 text-slate-700">${{ number_format((float) $purchaseItem->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-slate-500">No purchase history found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ route('products.index') }}" class="inline-block rounded-lg border border-slate-300 px-4 py-2 text-sm">Back to Products</a>
    </div>
</x-app-layout>
