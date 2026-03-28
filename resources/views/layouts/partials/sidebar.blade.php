<aside
    class="fixed inset-y-0 left-0 z-40 w-64 transform border-r border-slate-200 bg-white transition-transform duration-200 ease-in-out lg:static lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>
    <div class="flex h-16 items-center justify-between border-b border-slate-200 px-4">
        <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-slate-900">Inventory App</a>
        <button type="button" class="rounded-md p-2 text-slate-500 lg:hidden" @click="sidebarOpen = false" aria-label="Close sidebar">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="space-y-1 p-3 text-sm" x-data="{ reportsOpen: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
        @php($user = auth()->user())
        <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Dashboard</a>

        @if($user->isAdmin() || $user->isManager())
            <a href="{{ route('products.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('products.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Products</a>
            <a href="{{ route('categories.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('categories.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Categories</a>
            <a href="{{ route('customers.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('customers.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Customers</a>
            <a href="{{ route('suppliers.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('suppliers.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Suppliers</a>
        @endif

        @if($user->isAdmin())
            <a href="{{ route('admin.users.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('admin.users.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Users</a>
        @endif

        <a href="{{ route('sales.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('sales.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Sales</a>

        @if($user->isAdmin() || $user->isManager())
            <a href="{{ route('purchases.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('purchases.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Purchases</a>
            <a href="{{ route('stock.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('stock.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Stock</a>
        @endif

        @if($user->isAdmin() || $user->isManager())
            <div class="rounded-lg {{ request()->routeIs('reports.*') ? 'bg-slate-50' : '' }}">
                <button
                    type="button"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left {{ request()->routeIs('reports.*') ? 'font-medium text-slate-900' : 'text-slate-700 hover:bg-slate-100' }}"
                    @click="reportsOpen = !reportsOpen"
                    :aria-expanded="reportsOpen.toString()"
                    aria-controls="reports-submenu"
                >
                    <span>Reports</span>
                    <svg class="h-4 w-4 transition-transform" :class="reportsOpen ? 'rotate-180' : 'rotate-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="reports-submenu" x-show="reportsOpen" x-transition class="mt-1 space-y-1 pb-2 pl-2 pr-1" x-cloak>
                    <a href="{{ route('reports.stock') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.stock') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Stock</a>
                    <a href="{{ route('reports.low-stock') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.low-stock') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Low Stock</a>
                    <a href="{{ route('reports.profit') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.profit') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Profit</a>
                    <a href="{{ route('reports.batches') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.batches') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Batches</a>
                    <a href="{{ route('reports.expiry') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.expiry') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Expiry</a>
                    <a href="{{ route('reports.mrp') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.mrp') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">MRP</a>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="pt-2">
            @csrf
            <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-slate-700 hover:bg-slate-100">Logout</button>
        </form>
    </nav>
</aside>
