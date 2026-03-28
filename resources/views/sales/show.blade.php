<x-app-layout>
    <x-slot name="header">Sale Invoice Details</x-slot>

    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <div class="grid gap-3 sm:grid-cols-3">
                <div><p class="text-xs uppercase text-slate-500">Invoice</p><p class="font-semibold text-slate-900">{{ $sale->invoice_number }}</p></div>
                <div><p class="text-xs uppercase text-slate-500">Customer</p><p class="font-semibold text-slate-900">{{ $sale->customer?->name }}</p></div>
                <div><p class="text-xs uppercase text-slate-500">Total</p><p class="font-semibold text-slate-900">${{ number_format((float)$sale->total_amount, 2) }}</p></div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
            <h2 class="mb-3 text-lg font-semibold text-slate-900">Invoice Items</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2">Product</th>
                            <th class="px-3 py-2">Quantity</th>
                            <th class="px-3 py-2">Price</th>
                            <th class="px-3 py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-3 text-slate-900">{{ $item->product?->name }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $item->quantity }}</td>
                                <td class="px-3 py-3 text-slate-700">${{ number_format((float)$item->price, 2) }}</td>
                                <td class="px-3 py-3 text-slate-900 font-medium">${{ number_format((float)$item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ route('sales.index') }}" class="inline-block rounded-lg border border-slate-300 px-4 py-2 text-sm">Back to Sales</a>
    </div>
</x-app-layout>
