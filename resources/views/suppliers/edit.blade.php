<x-app-layout>
    <x-slot name="header">Edit Supplier</x-slot>

    <div class="max-w-2xl rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="grid gap-4 md:grid-cols-2">
            @csrf
            @method('PUT')
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                <input name="name" value="{{ old('name', $supplier->name) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Phone</label>
                <input name="phone" value="{{ old('phone', $supplier->phone) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium text-slate-700">Address</label>
                <textarea name="address" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('address', $supplier->address) }}</textarea>
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white">Update Supplier</button>
                <a href="{{ route('suppliers.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
