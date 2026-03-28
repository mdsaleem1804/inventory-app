<aside
    class="fixed inset-y-0 left-0 z-40 w-64 transform border-r border-slate-200 bg-white transition-all duration-300 ease-out lg:static lg:translate-x-0"
    :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        sidebarCollapsed ? 'lg:w-20' : 'lg:w-64'
    ]"
>
    <div class="flex h-16 items-center border-b border-slate-200 px-3" :class="sidebarCollapsed ? 'justify-center lg:justify-between' : 'justify-between'">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-semibold text-slate-900">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-xs font-bold text-white">IA</span>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="text-base">Inventory App</span>
        </a>

        <div class="flex items-center gap-1">
            <button type="button" class="hidden rounded-md p-2 text-slate-500 hover:bg-slate-100 lg:inline-flex" @click="toggleSidebar" :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'" aria-label="Toggle sidebar width">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!sidebarCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 19l-7-7 7-7" />
                    <path x-show="sidebarCollapsed" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <button type="button" class="rounded-md p-2 text-slate-500 hover:bg-slate-100 lg:hidden" @click="sidebarOpen = false" aria-label="Close sidebar">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <nav class="space-y-1 p-3 text-sm" x-data="{ reportsOpen: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
        @php($user = auth()->user())

        <a href="{{ route('dashboard') }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Dashboard">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 10.5L12 3l9 7.5V21a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-10.5z"/></svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity>Dashboard</span>
            <span x-show="sidebarCollapsed" x-cloak class="pointer-events-none absolute left-full ml-3 rounded bg-slate-900 px-2 py-1 text-xs text-white opacity-0 shadow transition group-hover:opacity-100">Dashboard</span>
        </a>

        @if($user->isAdmin() || $user->isManager())
            <a href="{{ route('products.index') }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('products.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Products">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity>Products</span>
                <span x-show="sidebarCollapsed" x-cloak class="pointer-events-none absolute left-full ml-3 rounded bg-slate-900 px-2 py-1 text-xs text-white opacity-0 shadow transition group-hover:opacity-100">Products</span>
            </a>

            <a href="{{ route('categories.index') }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('categories.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Categories">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 5h6v6H4V5zm10 0h6v6h-6V5zM4 13h6v6H4v-6zm10 3h6m-3-3v6"/></svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity>Categories</span>
                <span x-show="sidebarCollapsed" x-cloak class="pointer-events-none absolute left-full ml-3 rounded bg-slate-900 px-2 py-1 text-xs text-white opacity-0 shadow transition group-hover:opacity-100">Categories</span>
            </a>

            <a href="{{ route('customers.index') }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('customers.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Customers">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M16 14a4 4 0 10-8 0m8 0a6 6 0 013 5v1H5v-1a6 6 0 013-5m8 0a4 4 0 11-8 0"/></svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity>Customers</span>
                <span x-show="sidebarCollapsed" x-cloak class="pointer-events-none absolute left-full ml-3 rounded bg-slate-900 px-2 py-1 text-xs text-white opacity-0 shadow transition group-hover:opacity-100">Customers</span>
            </a>

            <a href="{{ route('suppliers.index') }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('suppliers.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Suppliers">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 21h18M5 21V7l7-4 7 4v14M9 10h6M9 14h6"/></svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity>Suppliers</span>
                <span x-show="sidebarCollapsed" x-cloak class="pointer-events-none absolute left-full ml-3 rounded bg-slate-900 px-2 py-1 text-xs text-white opacity-0 shadow transition group-hover:opacity-100">Suppliers</span>
            </a>
        @endif

        @if($user->isAdmin())
            <a href="{{ route('admin.users.index') }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('admin.users.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Users">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1m14-12a3 3 0 110 6 3 3 0 010-6zm-8 0a3 3 0 110 6 3 3 0 010-6z"/></svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity>Users</span>
                <span x-show="sidebarCollapsed" x-cloak class="pointer-events-none absolute left-full ml-3 rounded bg-slate-900 px-2 py-1 text-xs text-white opacity-0 shadow transition group-hover:opacity-100">Users</span>
            </a>
        @endif

        <a href="{{ route('sales.index') }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('sales.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Sales">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 6h18M7 12h10M10 18h4"/></svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity>Sales</span>
            <span x-show="sidebarCollapsed" x-cloak class="pointer-events-none absolute left-full ml-3 rounded bg-slate-900 px-2 py-1 text-xs text-white opacity-0 shadow transition group-hover:opacity-100">Sales</span>
        </a>

        @if($user->isAdmin() || $user->isManager())
            <a href="{{ route('purchases.index') }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('purchases.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Purchases">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 7h16M4 12h16M4 17h10"/></svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity>Purchases</span>
                <span x-show="sidebarCollapsed" x-cloak class="pointer-events-none absolute left-full ml-3 rounded bg-slate-900 px-2 py-1 text-xs text-white opacity-0 shadow transition group-hover:opacity-100">Purchases</span>
            </a>

            <a href="{{ route('stock.index') }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('stock.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Stock">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M20 13V7a2 2 0 00-2-2h-4V3H10v2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/></svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity>Stock</span>
                <span x-show="sidebarCollapsed" x-cloak class="pointer-events-none absolute left-full ml-3 rounded bg-slate-900 px-2 py-1 text-xs text-white opacity-0 shadow transition group-hover:opacity-100">Stock</span>
            </a>
        @endif

        @if($user->isAdmin() || $user->isManager())
            <template x-if="sidebarCollapsed">
                <a href="{{ route('reports.index') }}" class="group relative flex items-center justify-center rounded-lg px-3 py-2 transition {{ request()->routeIs('reports.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}" title="Reports">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 17v-2m4 2V7m4 10v-6M4 21h16M4 3h16"/></svg>
                    <span class="pointer-events-none absolute left-full ml-3 rounded bg-slate-900 px-2 py-1 text-xs text-white opacity-0 shadow transition group-hover:opacity-100">Reports</span>
                </a>
            </template>

            <div x-show="!sidebarCollapsed" x-cloak class="rounded-lg {{ request()->routeIs('reports.*') ? 'bg-slate-50' : '' }}">
                <button
                    type="button"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left transition {{ request()->routeIs('reports.*') ? 'font-medium text-slate-900' : 'text-slate-700 hover:bg-slate-100' }}"
                    @click="reportsOpen = !reportsOpen"
                    :aria-expanded="reportsOpen.toString()"
                    aria-controls="reports-submenu"
                >
                    <span class="flex items-center gap-3">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 17v-2m4 2V7m4 10v-6M4 21h16M4 3h16"/></svg>
                        <span>Reports</span>
                    </span>
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

        <a href="{{ route('profile.edit') }}" class="group relative flex items-center gap-3 rounded-lg px-3 py-2 transition {{ request()->routeIs('profile.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}" :class="sidebarCollapsed ? 'justify-center' : ''" title="Settings">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M10.325 4.317a1 1 0 011.35-.936l1.2.48a1 1 0 00.75 0l1.2-.48a1 1 0 011.35.936l.09 1.29a1 1 0 00.513.82l1.11.63a1 1 0 01.37 1.37l-.66 1.11a1 1 0 000 .96l.66 1.11a1 1 0 01-.37 1.37l-1.11.63a1 1 0 00-.512.82l-.09 1.29a1 1 0 01-1.35.936l-1.2-.48a1 1 0 00-.75 0l-1.2.48a1 1 0 01-1.35-.936l-.09-1.29a1 1 0 00-.512-.82l-1.11-.63a1 1 0 01-.37-1.37l.66-1.11a1 1 0 000-.96l-.66-1.11a1 1 0 01.37-1.37l1.11-.63a1 1 0 00.512-.82l.09-1.29z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 15a3 3 0 100-6 3 3 0 000 6z"/></svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity>Settings</span>
            <span x-show="sidebarCollapsed" x-cloak class="pointer-events-none absolute left-full ml-3 rounded bg-slate-900 px-2 py-1 text-xs text-white opacity-0 shadow transition group-hover:opacity-100">Settings</span>
        </a>

        <form method="POST" action="{{ route('logout') }}" class="pt-2">
            @csrf
            <button type="submit" class="group relative flex w-full items-center gap-3 rounded-lg px-3 py-2 text-slate-700 transition hover:bg-slate-100" :class="sidebarCollapsed ? 'justify-center' : ''" title="Logout">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 12H3m0 0l4-4m-4 4l4 4m6-9h5a2 2 0 012 2v6a2 2 0 01-2 2h-5"/></svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity>Logout</span>
                <span x-show="sidebarCollapsed" x-cloak class="pointer-events-none absolute left-full ml-3 rounded bg-slate-900 px-2 py-1 text-xs text-white opacity-0 shadow transition group-hover:opacity-100">Logout</span>
            </button>
        </form>
    </nav>
</aside>
