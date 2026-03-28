<x-app-layout>
    <x-slot name="header">Create Purchase Invoice</x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <form method="POST" action="{{ route('purchases.store') }}" id="purchase-form" class="space-y-4">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Supplier</label>
                    <select name="supplier_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                        <option value="">Select supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Invoice Number</label>
                    <input name="invoice_number" value="{{ old('invoice_number', 'PUR-' . now()->format('Ymd-His')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                    @error('invoice_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="min-w-full text-left text-sm" id="purchase-items-table">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2">Product</th>
                            <th class="px-3 py-2">Qty</th>
                            <th class="px-3 py-2">Cost Price</th>
                            <th class="px-3 py-2">MRP</th>
                            <th class="px-3 py-2">Expiry Date</th>
                            <th class="px-3 py-2">Total</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            @error('items')<p class="text-sm text-red-600">{{ $message }}</p>@enderror

            <div class="flex flex-wrap items-center justify-between gap-2">
                <button type="button" id="add-row" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Add Item</button>
                <p class="text-base font-semibold text-slate-900">Grand Total: $<span id="grand-total">0.00</span></p>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white">Save Invoice</button>
                <a href="{{ route('purchases.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Cancel</a>
            </div>
        </form>
    </div>

    <script id="purchase-products-data" type="application/json">@json($productsData)</script>
    <script>
        const products = JSON.parse(document.getElementById('purchase-products-data').textContent);
        const tbody = document.querySelector('#purchase-items-table tbody');
        const addRowBtn = document.getElementById('add-row');
        const grandTotalEl = document.getElementById('grand-total');

        function productOptions(selected = '') {
            return products.map(p => `<option value="${p.id}" ${String(selected) === String(p.id) ? 'selected' : ''}>${p.name}</option>`).join('');
        }

        function recalc() {
            let grand = 0;
            tbody.querySelectorAll('tr').forEach((row) => {
                const qty = parseFloat(row.querySelector('.qty').value || 0);
                const price = parseFloat(row.querySelector('.price').value || 0);
                const line = qty * price;
                row.querySelector('.line-total').textContent = line.toFixed(2);
                grand += line;
            });
            grandTotalEl.textContent = grand.toFixed(2);
        }

        function addRow(item = {}) {
            const index = tbody.children.length;
            const tr = document.createElement('tr');
            tr.className = 'border-t border-slate-100';
            tr.innerHTML = `
                <td class="px-3 py-2">
                    <select name="items[${index}][product_id]" class="product w-full rounded-lg border border-slate-300 px-2 py-1" required>
                        <option value="">Select</option>
                        ${productOptions(item.product_id || '')}
                    </select>
                </td>
                <td class="px-3 py-2"><input name="items[${index}][quantity]" type="number" min="1" value="${item.quantity || 1}" class="qty w-24 rounded-lg border border-slate-300 px-2 py-1" required></td>
                <td class="px-3 py-2"><input name="items[${index}][cost_price]" type="number" min="0" step="0.01" value="${item.cost_price || 0}" class="price w-28 rounded-lg border border-slate-300 px-2 py-1" required></td>
                <td class="px-3 py-2"><input name="items[${index}][mrp]" type="number" min="0" step="0.01" value="${item.mrp || ''}" class="mrp w-28 rounded-lg border border-slate-300 px-2 py-1" disabled></td>
                <td class="px-3 py-2"><input name="items[${index}][expiry_date]" type="date" value="${item.expiry_date || ''}" class="expiry rounded-lg border border-slate-300 px-2 py-1" disabled></td>
                <td class="px-3 py-2 text-slate-900">$<span class="line-total">0.00</span></td>
                <td class="px-3 py-2"><button type="button" class="remove rounded-md border border-red-300 px-2 py-1 text-xs text-red-700">Remove</button></td>
            `;
            tbody.appendChild(tr);

            tr.querySelectorAll('input,select').forEach(el => el.addEventListener('input', recalc));
            tr.querySelector('.product').addEventListener('change', (e) => {
                const product = products.find(p => String(p.id) === String(e.target.value));
                if (product) {
                    tr.querySelector('.price').value = parseFloat(product.cost_price || 0).toFixed(2);

                    const mrpInput = tr.querySelector('.mrp');
                    mrpInput.disabled = !(product.is_batch_enabled && product.has_mrp);
                    mrpInput.required = product.is_batch_enabled && product.has_mrp;
                    if (mrpInput.disabled) {
                        mrpInput.value = '';
                    }

                    const expiryInput = tr.querySelector('.expiry');
                    expiryInput.disabled = !(product.is_batch_enabled && product.has_expiry);
                    expiryInput.required = product.is_batch_enabled && product.has_expiry;
                    if (expiryInput.disabled) {
                        expiryInput.value = '';
                    }
                }
                recalc();
            });
            tr.querySelector('.remove').addEventListener('click', () => {
                tr.remove();
                recalc();
            });

            recalc();

            tr.querySelector('.product').dispatchEvent(new Event('change'));
        }

        addRowBtn.addEventListener('click', () => addRow());
        addRow();
    </script>
</x-app-layout>
