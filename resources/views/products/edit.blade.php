<x-app-layout>
    <x-slot name="header">Edit Product</x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <form method="POST" action="{{ route('products.update', $product) }}" class="grid gap-4 md:grid-cols-2">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="sku" class="mb-1 block text-sm font-medium text-slate-700">SKU</label>
                <input id="sku" name="sku" type="text" value="{{ old('sku', $product->sku) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                @error('sku')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="barcode" class="mb-1 block text-sm font-medium text-slate-700">Barcode</label>
                <input id="barcode" name="barcode" type="text" value="{{ old('barcode', $product->barcode) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                @error('barcode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="category_id" class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                <select id="category_id" name="category_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                    <option value="">Select category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="price" class="mb-1 block text-sm font-medium text-slate-700">Selling Price</label>
                <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                @error('price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="cost_price" class="mb-1 block text-sm font-medium text-slate-700">Cost Price</label>
                <input id="cost_price" name="cost_price" type="number" step="0.01" min="0" value="{{ old('cost_price', $product->cost_price) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                @error('cost_price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="unit" class="mb-1 block text-sm font-medium text-slate-700">Unit</label>
                <select id="unit" name="unit" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                    @foreach (['pcs', 'kg', 'box'] as $unit)
                        <option value="{{ $unit }}" @selected(old('unit', $product->unit) === $unit)>{{ strtoupper($unit) }}</option>
                    @endforeach
                </select>
                @error('unit')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="minimum_stock" class="mb-1 block text-sm font-medium text-slate-700">Minimum Stock</label>
                <input id="minimum_stock" name="minimum_stock" type="number" min="0" value="{{ old('minimum_stock', $product->minimum_stock) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" required>
                @error('minimum_stock')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                <textarea id="description" name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">{{ old('description', $product->description) }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked(old('is_active', $product->is_active))>
                    Active product
                </label>
            </div>

            <div class="md:col-span-2 rounded-lg border border-slate-200 p-4">
                <p class="mb-2 text-sm font-medium text-slate-800">Advanced Inventory Options</p>
                <div class="grid gap-3 sm:grid-cols-3">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input id="is_batch_enabled" type="checkbox" name="is_batch_enabled" value="1" class="rounded border-slate-300" @checked(old('is_batch_enabled', $product->is_batch_enabled))>
                        Enable Batch
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input id="has_expiry" type="checkbox" name="has_expiry" value="1" class="rounded border-slate-300" @checked(old('has_expiry', $product->has_expiry))>
                        Enable Expiry
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input id="has_mrp" type="checkbox" name="has_mrp" value="1" class="rounded border-slate-300" @checked(old('has_mrp', $product->has_mrp))>
                        Enable MRP
                    </label>
                </div>
                @error('is_batch_enabled')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('has_expiry')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('has_mrp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Update Product</button>
                <a href="{{ route('products.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        const batchToggle = document.getElementById('is_batch_enabled');
        const expiryToggle = document.getElementById('has_expiry');
        const mrpToggle = document.getElementById('has_mrp');

        function syncBatchOptionDependencies() {
            const enabled = batchToggle.checked;
            expiryToggle.disabled = !enabled;
            mrpToggle.disabled = !enabled;

            if (!enabled) {
                expiryToggle.checked = false;
                mrpToggle.checked = false;
            }
        }

        batchToggle.addEventListener('change', syncBatchOptionDependencies);
        syncBatchOptionDependencies();
    </script>
</x-app-layout>
