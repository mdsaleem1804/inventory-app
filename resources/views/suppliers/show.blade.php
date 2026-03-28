<x-app-layout>
    <x-slot name="header">Supplier Details</x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <dl class="grid gap-4 sm:grid-cols-2">
            <div><dt class="text-xs uppercase text-slate-500">Name</dt><dd class="mt-1 font-medium text-slate-900">{{ $supplier->name }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Phone</dt><dd class="mt-1 text-slate-700">{{ $supplier->phone ?? '-' }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Email</dt><dd class="mt-1 text-slate-700">{{ $supplier->email ?? '-' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-xs uppercase text-slate-500">Address</dt><dd class="mt-1 text-slate-700">{{ $supplier->address ?? '-' }}</dd></div>
        </dl>
        <div class="mt-6"><a href="{{ route('suppliers.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Back</a></div>
    </div>
</x-app-layout>
