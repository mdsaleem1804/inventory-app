<x-app-layout>
    <x-slot name="header">Stock History</x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">{{ $product->name }} <span class="text-slate-500">({{ $product->sku }})</span></h2>
                <p class="text-sm text-slate-600">Current Stock: <span class="font-semibold">{{ $product->current_stock }}</span></p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('products.stock-in.create', ['product' => $product, 'type' => 'IN']) }}" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">Add Stock</a>
                <a href="{{ route('products.stock-out.create', ['product' => $product, 'type' => 'OUT']) }}" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Remove Stock</a>
                <a href="{{ route('products.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Back</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="px-3 py-2 font-medium">Date</th>
                        <th class="px-3 py-2 font-medium">Type</th>
                        <th class="px-3 py-2 font-medium">Quantity</th>
                        <th class="px-3 py-2 font-medium">Batch</th>
                        <th class="px-3 py-2 font-medium">Reference</th>
                        <th class="px-3 py-2 font-medium">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        <tr class="border-b border-slate-100">
                            <td class="px-3 py-3 text-slate-700">{{ $movement->created_at?->format('M d, Y h:i A') }}</td>
                            <td class="px-3 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-medium {{ $movement->type === 'IN' ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700' }}">{{ $movement->type }}</span>
                            </td>
                            <td class="px-3 py-3 text-slate-900 font-medium">{{ $movement->quantity }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $movement->batch?->batch_number ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $movement->reference ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $movement->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-sm text-slate-500">No stock movements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $movements->links() }}</div>
    </div>
</x-app-layout>
