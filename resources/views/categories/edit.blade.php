<x-app-layout>
    <x-slot name="header">Edit Category</x-slot>

    <div class="max-w-xl rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Category Name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $category->name) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                    required
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Update Category</button>
                <a href="{{ route('categories.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
