<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Products</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalProducts }}</p>
        </article>

        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Active Products</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ $activeProducts }}</p>
        </article>

        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-1">
            <p class="text-sm text-slate-500">Categories</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalCategories }}</p>
        </article>
    </section>

    <section class="mt-6 grid gap-4 md:grid-cols-2">
        <a href="{{ route('products.index') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow">
            <h2 class="text-lg font-semibold text-slate-900">Manage Products</h2>
            <p class="mt-2 text-sm text-slate-600">Create, edit, activate, and organize inventory items.</p>
        </a>

        <a href="{{ route('categories.index') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow">
            <h2 class="text-lg font-semibold text-slate-900">Manage Categories</h2>
            <p class="mt-2 text-sm text-slate-600">Keep product classification clean and scalable.</p>
        </a>
    </section>

    <section class="mt-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900">Foundation Ready</h2>
        <p class="mt-2 text-sm text-slate-600">
            Your inventory foundation now supports secure access, structured category/product data, and extensible modules for stock and reporting.
        </p>
    </section>
</x-app-layout>
