<x-app-layout>
    <x-slot name="header">Products</x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Product List</h2>
                <p class="text-sm text-slate-500">Search, filter, sort, and manage product inventory quickly.</p>
            </div>
            <a href="{{ route('products.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Add Product</a>
        </div>

        <form method="GET" action="{{ route('products.index') }}" class="mb-4 grid gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3 md:grid-cols-5">
            <div class="md:col-span-2">
                <label for="search" class="mb-1 block text-xs font-medium uppercase tracking-wide text-slate-500">Search</label>
                <input id="search" name="search" value="{{ $search }}" placeholder="Name, SKU, or barcode" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label for="category_id" class="mb-1 block text-xs font-medium uppercase tracking-wide text-slate-500">Category</label>
                <select id="category_id" name="category_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) $categoryId === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="mb-1 block text-xs font-medium uppercase tracking-wide text-slate-500">Status</label>
                <select id="status" name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="all" @selected($status === 'all')>All</option>
                    <option value="active" @selected($status === 'active')>Active</option>
                    <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="low_stock" value="1" class="rounded border-slate-300" @checked($lowStock)>
                    Low stock only
                </label>
            </div>
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="direction" value="{{ $direction }}">
            <div class="md:col-span-5 flex flex-wrap gap-2">
                <button type="submit" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Apply Filters</button>
                <a href="{{ route('products.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Reset</a>
            </div>
        </form>

        <form method="POST" action="{{ route('products.bulk-action') }}" id="bulk-action-form" class="mb-4">
            @csrf
            <div class="flex flex-wrap items-center gap-2 rounded-lg border border-slate-200 p-3">
                <select name="action" id="bulk-action" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                    <option value="">Bulk action</option>
                    <option value="activate">Activate</option>
                    <option value="deactivate">Deactivate</option>
                    @if(auth()->user()->isAdmin())
                        <option value="delete">Delete</option>
                    @endif
                </select>
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Apply</button>
                <p class="text-xs text-slate-500">Select products from the table below, then choose a bulk action.</p>
            </div>
            <div id="bulk-selected-products"></div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="px-3 py-2 font-medium">
                            <input type="checkbox" id="select-all-products" class="rounded border-slate-300" aria-label="Select all products">
                        </th>
                        @php
                            $queryForSort = request()->except(['sort', 'direction', 'page']);
                            $sortLabel = static function (string $column, string $label) use ($sort, $direction, $queryForSort) {
                                $nextDirection = $sort === $column && $direction === 'asc' ? 'desc' : 'asc';
                                $arrow = $sort === $column ? ($direction === 'asc' ? '↑' : '↓') : '';
                                $url = route('products.index', array_merge($queryForSort, ['sort' => $column, 'direction' => $nextDirection]));

                                return '<a href="' . $url . '" class="inline-flex items-center gap-1 hover:text-slate-800">' . e($label) . ' <span class="text-xs">' . $arrow . '</span></a>';
                            };
                        @endphp
                        <th class="px-3 py-2 font-medium">{!! $sortLabel('name', 'Name') !!}</th>
                        <th class="px-3 py-2 font-medium">{!! $sortLabel('sku', 'SKU') !!}</th>
                        <th class="px-3 py-2 font-medium">Category</th>
                        <th class="px-3 py-2 font-medium">Current Stock</th>
                        <th class="px-3 py-2 font-medium">{!! $sortLabel('minimum_stock', 'Min Stock') !!}</th>
                        <th class="px-3 py-2 font-medium">{!! $sortLabel('price', 'Price') !!}</th>
                        <th class="px-3 py-2 font-medium">{!! $sortLabel('is_active', 'Status') !!}</th>
                        <th class="px-3 py-2 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="border-b border-slate-100">
                            <td class="px-3 py-3">
                                <input type="checkbox" value="{{ $product->id }}" class="product-select rounded border-slate-300" aria-label="Select product {{ $product->name }}">
                            </td>
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $product->name }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $product->sku }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $product->category?->name }}</td>
                            <td class="px-3 py-3 font-semibold text-slate-900">{{ $product->current_stock }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $product->minimum_stock }}</td>
                            <td class="px-3 py-3 text-slate-700">${{ number_format((float) $product->price, 2) }}</td>
                            <td class="px-3 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('products.show', $product) }}" class="rounded-md border border-cyan-300 px-3 py-1.5 text-xs font-medium text-cyan-700 hover:bg-cyan-50">View</a>
                                    <a href="{{ route('products.stock-in.create', ['product' => $product, 'type' => 'IN']) }}" class="rounded-md border border-green-300 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-50">Add Stock</a>
                                    <a href="{{ route('products.stock-out.create', ['product' => $product, 'type' => 'OUT']) }}" class="rounded-md border border-rose-300 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Remove Stock</a>
                                    <a href="{{ route('products.stock-movements.index', $product) }}" class="rounded-md border border-indigo-300 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50">History</a>
                                    <a href="{{ route('products.edit', $product) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100">Edit</a>

                                    <form method="POST" action="{{ route('products.toggle-status', $product) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-md border border-amber-300 px-3 py-1.5 text-xs font-medium text-amber-700 hover:bg-amber-50">
                                            {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    @if(auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-md border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-3 py-8 text-center text-sm text-slate-500">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $products->links() }}</div>
    </div>

    <script>
        const selectAllCheckbox = document.getElementById('select-all-products');
        const productCheckboxes = () => Array.from(document.querySelectorAll('.product-select'));
        const bulkForm = document.getElementById('bulk-action-form');
        const bulkAction = document.getElementById('bulk-action');
        const bulkSelectedContainer = document.getElementById('bulk-selected-products');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', () => {
                productCheckboxes().forEach((checkbox) => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        }

        bulkForm?.addEventListener('submit', (event) => {
            const selectedCount = productCheckboxes().filter((checkbox) => checkbox.checked).length;
            const selectedIds = productCheckboxes()
                .filter((checkbox) => checkbox.checked)
                .map((checkbox) => checkbox.value);

            if (selectedCount === 0) {
                event.preventDefault();
                alert('Please select at least one product.');
                return;
            }

            if (!bulkAction.value) {
                event.preventDefault();
                alert('Please choose a bulk action.');
                return;
            }

            bulkSelectedContainer.innerHTML = '';
            selectedIds.forEach((id) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'product_ids[]';
                input.value = id;
                bulkSelectedContainer.appendChild(input);
            });

            if (bulkAction.value === 'delete' && !confirm('Delete selected products? This action cannot be undone.')) {
                event.preventDefault();
                return;
            }

            if ((bulkAction.value === 'activate' || bulkAction.value === 'deactivate') && !confirm('Apply this bulk action to selected products?')) {
                event.preventDefault();
            }
        });
    </script>
</x-app-layout>
