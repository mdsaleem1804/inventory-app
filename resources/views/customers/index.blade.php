<x-app-layout>
    <x-slot name="header">Customers</x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Customer List</h2>
                <p class="text-sm text-slate-600">Manage customer master records.</p>
            </div>
            <a href="{{ route('customers.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Add Customer</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Phone</th>
                        <th class="px-3 py-2">Email</th>
                        <th class="px-3 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr class="border-b border-slate-100">
                            <td class="px-3 py-3 font-medium text-slate-900">{{ $customer->name }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $customer->phone ?? '-' }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ $customer->email ?? '-' }}</td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('customers.show', $customer) }}" class="rounded-md border border-indigo-300 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50">View</a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100">Edit</a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Delete this customer?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-red-300 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-3 py-8 text-center text-slate-500">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $customers->links() }}</div>
    </div>
</x-app-layout>
