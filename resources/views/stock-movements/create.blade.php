<x-app-layout>
    <x-slot name="header">{{ $type === 'OUT' ? 'Remove Stock' : 'Add Stock' }}</x-slot>

    <div class="max-w-3xl rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-slate-900">{{ $product->name }} <span class="text-slate-500">({{ $product->sku }})</span></h2>
            <p class="mt-1 text-sm text-slate-600">Current Stock: <span class="font-semibold">{{ $product->current_stock }}</span></p>
        </div>

        <form method="POST" action="{{ route('products.stock-movements.store', $product) }}" class="grid gap-4 md:grid-cols-2">
            @csrf

            <div>
                <label for="type" class="mb-1 block text-sm font-medium text-slate-700">Movement Type</label>
                <select id="type" name="type" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                    <option value="IN" @selected(old('type', $type) === 'IN')>IN</option>
                    <option value="OUT" @selected(old('type', $type) === 'OUT')>OUT</option>
                </select>
                @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="quantity" class="mb-1 block text-sm font-medium text-slate-700">Quantity</label>
                <input id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                @error('quantity')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="reference" class="mb-1 block text-sm font-medium text-slate-700">Reference</label>
                <input id="reference" name="reference" type="text" value="{{ old('reference') }}" placeholder="purchase, sale, adjustment" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                @error('reference')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="notes" class="mb-1 block text-sm font-medium text-slate-700">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">{{ old('notes') }}</textarea>
                @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2 flex flex-wrap items-center gap-2">
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Save Movement</button>
                <a href="{{ route('products.stock-movements.index', $product) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">View History</a>
                <a href="{{ route('products.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Back to Products</a>
            </div>
        </form>
    </div>
</x-app-layout>
