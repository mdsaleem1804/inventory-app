<x-app-layout>
    <x-slot name="header">Reports</x-slot>

    <div class="grid gap-4 md:grid-cols-3">
        <a href="{{ route('reports.stock') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow">
            <h2 class="text-lg font-semibold text-slate-900">Stock Report</h2>
            <p class="mt-2 text-sm text-slate-600">Live stock position by product and category.</p>
        </a>

        <a href="{{ route('reports.low-stock') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow">
            <h2 class="text-lg font-semibold text-slate-900">Low Stock Report</h2>
            <p class="mt-2 text-sm text-slate-600">Reorder-sensitive items based on minimum stock thresholds.</p>
        </a>

        <a href="{{ route('reports.profit') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow">
            <h2 class="text-lg font-semibold text-slate-900">Profit Report</h2>
            <p class="mt-2 text-sm text-slate-600">Sales, cost, and margin analysis with filters and export.</p>
        </a>
    </div>
</x-app-layout>
