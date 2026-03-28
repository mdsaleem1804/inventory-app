<x-app-layout>
    <x-slot name="header">Create Product</x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <form method="POST" action="{{ route('products.store') }}" class="grid gap-4 md:grid-cols-2">
            @csrf

            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="sku" class="mb-1 block text-sm font-medium text-slate-700">SKU</label>
                <input id="sku" name="sku" type="text" value="{{ old('sku') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                @error('sku')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="barcode" class="mb-1 block text-sm font-medium text-slate-700">Barcode</label>
                <input id="barcode" name="barcode" type="text" value="{{ old('barcode') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                @error('barcode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="category_id" class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                <select id="category_id" name="category_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                    <option value="">Select category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="price" class="mb-1 block text-sm font-medium text-slate-700">Selling Price</label>
                <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                @error('price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="cost_price" class="mb-1 block text-sm font-medium text-slate-700">Cost Price</label>
                <input id="cost_price" name="cost_price" type="number" step="0.01" min="0" value="{{ old('cost_price', 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                @error('cost_price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="unit" class="mb-1 block text-sm font-medium text-slate-700">Unit</label>
                <select id="unit" name="unit" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                    @foreach (['pcs', 'kg', 'box'] as $unit)
                        <option value="{{ $unit }}" @selected(old('unit', 'pcs') === $unit)>{{ strtoupper($unit) }}</option>
                    @endforeach
                </select>
                @error('unit')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="minimum_stock" class="mb-1 block text-sm font-medium text-slate-700">Minimum Stock</label>
                <input id="minimum_stock" name="minimum_stock" type="number" min="0" value="{{ old('minimum_stock', 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                @error('minimum_stock')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                <textarea id="description" name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked(old('is_active', true))>
                    Active product
                </label>
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Save Product</button>
                <a href="{{ route('products.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
