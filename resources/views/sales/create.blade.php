<x-app-layout>
    <x-slot name="header">Create Sale Invoice</x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <form method="POST" action="{{ route('sales.store') }}" id="sale-form" class="space-y-4">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Customer</label>
                    <select name="customer_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                        <option value="">Select customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Invoice Number</label>
                    <input name="invoice_number" value="{{ old('invoice_number', 'SAL-' . now()->format('Ymd-His')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                    @error('invoice_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="min-w-full text-left text-sm" id="sale-items-table">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2">Product</th>
                            <th class="px-3 py-2">Available</th>
                            <th class="px-3 py-2">Batch (Optional)</th>
                            <th class="px-3 py-2">Qty</th>
                            <th class="px-3 py-2">Price</th>
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
                <a href="{{ route('sales.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Cancel</a>
            </div>
        </form>
    </div>

    <script id="sales-products-data" type="application/json">@json($productsData)</script>
    <script>
        const products = JSON.parse(document.getElementById('sales-products-data').textContent);
        const tbody = document.querySelector('#sale-items-table tbody');
        const addRowBtn = document.getElementById('add-row');
        const grandTotalEl = document.getElementById('grand-total');

        function productOptions(selected = '') {
            return products.map(p => `<option value="${p.id}" ${String(selected) === String(p.id) ? 'selected' : ''}>${p.name}</option>`).join('');
        }

        function batchOptions(product, selected = '') {
            if (!product?.is_batch_enabled) {
                return '<option value="">Auto FIFO (Non-Batch Product)</option>';
            }

            const validBatches = (product.batches || []).filter(b => Number(b.remaining_quantity) > 0);
            if (!validBatches.length) {
                return '<option value="">No available batch</option>';
            }

            const options = validBatches.map((b) => {
                const expiryPart = b.expiry_date ? ` | Exp: ${b.expiry_date}` : '';
                return `<option value="${b.id}" ${String(selected) === String(b.id) ? 'selected' : ''}>${b.batch_number} (${b.remaining_quantity})${expiryPart}</option>`;
            }).join('');

            return `<option value="">Auto FIFO</option>${options}`;
        }

        function recalc() {
            let grand = 0;
            tbody.querySelectorAll('tr').forEach((row) => {
                const qty = parseFloat(row.querySelector('.qty').value || 0);
                const price = parseFloat(row.querySelector('.price').value || 0);
                const line = qty * price;
                row.querySelector('.line-total').textContent = line.toFixed(2);
                grand += line;

                const productId = row.querySelector('.product').value;
                const product = products.find(p => String(p.id) === String(productId));
                const stock = product?.stock ?? 0;
                row.querySelector('.stock').textContent = stock;

                const batchSelect = row.querySelector('.batch');
                const selectedBatch = batchSelect.value || batchSelect.dataset.selected || '';
                batchSelect.innerHTML = batchOptions(product, selectedBatch);
                batchSelect.disabled = !(product?.is_batch_enabled);
                batchSelect.dataset.selected = batchSelect.value || '';
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
                <td class="px-3 py-2 text-slate-700"><span class="stock">0</span></td>
                <td class="px-3 py-2">
                    <select name="items[${index}][batch_id]" class="batch w-44 rounded-lg border border-slate-300 px-2 py-1" data-selected="${item.batch_id || ''}" disabled>
                        <option value="">Auto FIFO</option>
                    </select>
                </td>
                <td class="px-3 py-2"><input name="items[${index}][quantity]" type="number" min="1" value="${item.quantity || 1}" class="qty w-24 rounded-lg border border-slate-300 px-2 py-1" required></td>
                <td class="px-3 py-2"><input name="items[${index}][price]" type="number" min="0" step="0.01" value="${item.price || 0}" class="price w-28 rounded-lg border border-slate-300 px-2 py-1" required></td>
                <td class="px-3 py-2 text-slate-900">$<span class="line-total">0.00</span></td>
                <td class="px-3 py-2"><button type="button" class="remove rounded-md border border-red-300 px-2 py-1 text-xs text-red-700">Remove</button></td>
            `;
            tbody.appendChild(tr);

            tr.querySelectorAll('input,select').forEach(el => el.addEventListener('input', recalc));
            tr.querySelector('.product').addEventListener('change', () => {
                tr.querySelector('.batch').dataset.selected = '';
                recalc();
            });
            tr.querySelector('.batch').addEventListener('change', (e) => {
                e.target.dataset.selected = e.target.value;
            });
            tr.querySelector('.remove').addEventListener('click', () => {
                tr.remove();
                recalc();
            });

            recalc();
        }

        addRowBtn.addEventListener('click', () => addRow());
        addRow();
    </script>
</x-app-layout>
