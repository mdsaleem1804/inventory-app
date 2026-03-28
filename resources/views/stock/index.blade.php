<x-app-layout>
    <x-slot name="header">Stock Ledger</x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-slate-900">Recent Stock Movements</h2>
            <p class="text-sm text-slate-600">Ledger view across all products. Stock is calculated from these transactions.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="px-3 py-2 font-medium">Date</th>
                        <th class="px-3 py-2 font-medium">Product</th>
                        <th class="px-3 py-2 font-medium">Type</th>
                        <th class="px-3 py-2 font-medium">Quantity</th>
                        <th class="px-3 py-2 font-medium">Batch</th>
                        <th class="px-3 py-2 font-medium">Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
                        <tr class="border-b border-slate-100">
                            <td class="px-3 py-3 text-slate-700">{{ $movement->created_at?->format('M d, Y h:i A') }}</td>
                            <td class="px-3 py-3">
                                <a href="{{ route('products.stock-movements.index', $movement->product) }}" class="font-medium text-slate-900 hover:underline">{{ $movement->product?->name }}</a>
                            </td>
                            <td class="px-3 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-medium {{ $movement->type === 'IN' ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700' }}">{{ $movement->type }}</span>
                            </td>
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $movement->quantity }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $movement->batch?->batch_number ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $movement->reference ?? '-' }}</td>
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
