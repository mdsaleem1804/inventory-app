<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="space-y-6" x-data="dashboardWidget(@js($dashboardData), @js(route('dashboard.stats')))" x-init="init()">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Total Products</p>
                <p class="mt-3 text-3xl font-bold text-slate-900" x-text="formatNumber(data.kpis.total_products)"></p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Total Stock</p>
                <p class="mt-3 text-3xl font-bold text-slate-900" x-text="formatNumber(data.kpis.total_stock)"></p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Total Sales</p>
                <p class="mt-3 text-3xl font-bold text-emerald-600" x-text="formatCurrency(data.kpis.total_sales)"></p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-slate-500">Total Purchases</p>
                <p class="mt-3 text-3xl font-bold text-cyan-700" x-text="formatCurrency(data.kpis.total_purchases)"></p>
            </article>
            <article class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <p class="text-xs uppercase tracking-wide text-amber-700">Low Stock Count</p>
                <p class="mt-3 text-3xl font-bold text-amber-700" x-text="formatNumber(data.kpis.low_stock_count)"></p>
            </article>
        </section>

        <section class="grid gap-4 lg:grid-cols-3">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-semibold text-slate-900">Sales vs Purchases</h2>
                    <span class="text-xs text-slate-500">Updates every 12 seconds</span>
                </div>
                <div class="h-72"><canvas id="salesPurchasesChart"></canvas></div>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-slate-900">Stock by Category</h2>
                <div class="h-72"><canvas id="stockByCategoryChart"></canvas></div>
            </article>
        </section>

        <section class="grid gap-4 lg:grid-cols-3">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                <h2 class="mb-4 text-lg font-semibold text-slate-900">Top Selling Products</h2>
                <div class="h-72"><canvas id="topProductsChart"></canvas></div>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-700 p-5 text-white shadow-sm">
                <h2 class="text-lg font-semibold">Live Snapshot</h2>
                <p class="mt-2 text-sm text-slate-200">A quick pulse of your inventory operations with near real-time updates.</p>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex items-center justify-between"><span>Low stock risk</span><span class="font-semibold" x-text="data.kpis.low_stock_count"></span></div>
                    <div class="flex items-center justify-between"><span>Sales volume</span><span class="font-semibold" x-text="formatCurrency(data.kpis.total_sales)"></span></div>
                    <div class="flex items-center justify-between"><span>Purchase spend</span><span class="font-semibold" x-text="formatCurrency(data.kpis.total_purchases)"></span></div>
                    <div class="mt-4 text-xs text-slate-300">Last synced: <span x-text="data.generated_at"></span></div>
                </div>
            </article>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function dashboardWidget(initialData, refreshUrl) {
            return {
                data: initialData,
                refreshUrl,
                charts: {
                    salesPurchases: null,
                    topProducts: null,
                    stockByCategory: null,
                },
                init() {
                    this.buildCharts();
                    setInterval(() => this.refreshData(), 12000);
                },
                formatNumber(value) {
                    return new Intl.NumberFormat().format(value || 0);
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat(undefined, {
                        style: 'currency',
                        currency: 'USD',
                        maximumFractionDigits: 2,
                    }).format(value || 0);
                },
                buildCharts() {
                    const salesCtx = document.getElementById('salesPurchasesChart');
                    const topCtx = document.getElementById('topProductsChart');
                    const stockCtx = document.getElementById('stockByCategoryChart');

                    this.charts.salesPurchases = new Chart(salesCtx, {
                        type: 'line',
                        data: {
                            labels: this.data.charts.sales_vs_purchases.labels,
                            datasets: [
                                {
                                    label: 'Sales',
                                    data: this.data.charts.sales_vs_purchases.sales,
                                    borderColor: '#059669',
                                    backgroundColor: 'rgba(5, 150, 105, 0.18)',
                                    fill: true,
                                    tension: 0.35,
                                },
                                {
                                    label: 'Purchases',
                                    data: this.data.charts.sales_vs_purchases.purchases,
                                    borderColor: '#0f766e',
                                    backgroundColor: 'rgba(15, 118, 110, 0.12)',
                                    fill: true,
                                    tension: 0.35,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'top' } },
                        },
                    });

                    this.charts.topProducts = new Chart(topCtx, {
                        type: 'bar',
                        data: {
                            labels: this.data.charts.top_selling_products.labels,
                            datasets: [{
                                label: 'Quantity Sold',
                                data: this.data.charts.top_selling_products.values,
                                backgroundColor: '#1d4ed8',
                                borderRadius: 6,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                        },
                    });

                    this.charts.stockByCategory = new Chart(stockCtx, {
                        type: 'pie',
                        data: {
                            labels: this.data.charts.stock_by_category.labels,
                            datasets: [{
                                data: this.data.charts.stock_by_category.values,
                                backgroundColor: ['#0f766e', '#0ea5e9', '#2563eb', '#7c3aed', '#ca8a04', '#be123c', '#4d7c0f'],
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } },
                        },
                    });
                },
                async refreshData() {
                    try {
                        const response = await fetch(this.refreshUrl, {
                            headers: { Accept: 'application/json' },
                        });

                        if (!response.ok) {
                            return;
                        }

                        const nextData = await response.json();
                        this.data = nextData;
                        this.syncCharts();
                    } catch (error) {
                        // Quietly ignore refresh failures to keep dashboard usable.
                    }
                },
                syncCharts() {
                    this.charts.salesPurchases.data.labels = this.data.charts.sales_vs_purchases.labels;
                    this.charts.salesPurchases.data.datasets[0].data = this.data.charts.sales_vs_purchases.sales;
                    this.charts.salesPurchases.data.datasets[1].data = this.data.charts.sales_vs_purchases.purchases;
                    this.charts.salesPurchases.update();

                    this.charts.topProducts.data.labels = this.data.charts.top_selling_products.labels;
                    this.charts.topProducts.data.datasets[0].data = this.data.charts.top_selling_products.values;
                    this.charts.topProducts.update();

                    this.charts.stockByCategory.data.labels = this.data.charts.stock_by_category.labels;
                    this.charts.stockByCategory.data.datasets[0].data = this.data.charts.stock_by_category.values;
                    this.charts.stockByCategory.update();
                },
            };
        }
    </script>
</x-app-layout>
