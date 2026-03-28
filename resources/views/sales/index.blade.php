<x-app-layout>
    <x-slot name="header">Sales Invoices</x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Sales</h2>
                <p class="text-sm text-slate-600">Track customer invoices and stock-out transactions.</p>
            </div>
            <a href="{{ route('sales.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Create Sale Invoice</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="px-3 py-2">Invoice</th>
                        <th class="px-3 py-2">Customer</th>
                        <th class="px-3 py-2">Total</th>
                        <th class="px-3 py-2">Date</th>
                        <th class="px-3 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr class="border-b border-slate-100">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $sale->invoice_number }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $sale->customer?->name }}</td>
                            <td class="px-3 py-3 text-slate-700">${{ number_format((float)$sale->total_amount, 2) }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $sale->created_at?->format('M d, Y') }}</td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('sales.show', $sale) }}" class="rounded-md border border-indigo-300 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50">View</a>
                                    <form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('Delete this sale invoice?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-8 text-center text-slate-500">No sales invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $sales->links() }}</div>
    </div>
</x-app-layout>
