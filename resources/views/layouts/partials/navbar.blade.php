<header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="rounded-md p-2 text-slate-600 hover:bg-slate-100 lg:hidden"
                @click="sidebarOpen = true"
                aria-label="Open sidebar"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <div>
                <h1 class="text-lg font-semibold text-slate-900">{{ $header ?? 'Dashboard' }}</h1>
            </div>
        </div>

        <div class="text-right text-sm">
            <p class="font-medium text-slate-900">{{ auth()->user()->name }}</p>
            <p class="text-slate-500">{{ auth()->user()->email }}</p>
        </div>
    </div>
</header>
