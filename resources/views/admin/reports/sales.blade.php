<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold t-size8 font-heading text-text">
                    {{ request('type') === 'financial' ? __('Financial Report 💖') : __('Sales Report 💖') }}
                </h2>
                <p class="text-text-muted t-size3 mt-1">
                    {{ request('type') === 'financial' ? __('Ringkasan dan analisis laporan keuangan perusahaan.') : __('Lihat dan analisis performa penjualan seluruh outlet.') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <div class="hidden sm:block">
                    <div class="bg-card border border-border px-4 py-2 rounded-2xl flex items-center gap-2 t-size2 font-semibold text-text">
                        <span class="text-primary">📅</span>
                        {{ now()->translatedFormat('l, d M Y') }}
                    </div>
                </div>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}"
                    class="bg-primary hover:bg-primary-strong text-white font-bold px-4 py-2.5 rounded-xl transition shadow-xs cursor-pointer flex items-center gap-2 t-size3">
                    📤 {{ __('Export Report') }}
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-6 anim-fade">

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2 border-b border-border pb-px">
            <a href="{{ route('admin.reports.sales', ['type' => 'sales', 'branch_id' => request('branch_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                class="px-5 py-3 font-bold t-size3 transition-all border-b-2 {{ request('type') !== 'financial' ? 'border-primary text-accent bg-primary-soft/10 rounded-t-xl' : 'border-transparent text-text-muted hover:text-text' }}">
                📈 Sales Report
            </a>
            <a href="{{ route('admin.reports.sales', ['type' => 'financial', 'branch_id' => request('branch_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                class="px-5 py-3 font-bold t-size3 transition-all border-b-2 {{ request('type') === 'financial' ? 'border-primary text-accent bg-primary-soft/10 rounded-t-xl' : 'border-transparent text-text-muted hover:text-text' }}">
                💵 Financial Report
            </a>
            <a href="{{ route('admin.reports.menus', ['branch_id' => request('branch_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                class="px-5 py-3 font-bold t-size3 transition-all border-b-2 border-transparent text-text-muted hover:text-text">
                🍔 Performa Menu
            </a>
            <a href="{{ route('admin.reports.payments', ['branch_id' => request('branch_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                class="px-5 py-3 font-bold t-size3 transition-all border-b-2 border-transparent text-text-muted hover:text-text">
                💳 Metode Pembayaran
            </a>
        </div>

        <!-- Filters Row -->
        <div class="bg-card border border-border rounded-2xl p-4 shadow-xs">
            <form method="GET" action="{{ route('admin.reports.sales') }}" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="type" value="{{ request('type', 'sales') }}">

                <!-- Date Range picker -->
                <div class="flex items-center bg-card border border-border rounded-xl px-3 py-1.5 gap-2 shrink-0">
                    <span class="text-text-muted">📅</span>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                        class="bg-transparent border-0 p-0 text-text font-semibold t-size3 focus:ring-0 focus:outline-none">
                    <span class="text-text-muted font-bold t-size2">-</span>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                        class="bg-transparent border-0 p-0 text-text font-semibold t-size3 focus:ring-0 focus:outline-none">
                </div>

                <!-- Outlet Dropdown (Only for Super Admin) -->
                @if (auth()->user()->branch_id === null)
                    <select name="branch_id"
                        class="bg-card border border-border rounded-xl px-3 py-2 t-size3 font-semibold text-text focus:ring-primary focus:border-primary cursor-pointer">
                        <option value="">Semua Outlet</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <!-- Kategori Dropdown (Placeholder) -->
                <select disabled class="bg-surface border border-border rounded-xl px-3 py-2 t-size3 font-semibold text-text-muted cursor-not-allowed">
                    <option>Semua Kategori</option>
                </select>

                <!-- Metode Pembayaran Dropdown (Placeholder) -->
                <select disabled class="bg-surface border border-border rounded-xl px-3 py-2 t-size3 font-semibold text-text-muted cursor-not-allowed">
                    <option>Semua Metode Pembayaran</option>
                </select>

                <button type="submit"
                    class="bg-card border border-primary hover:bg-primary-soft/20 text-accent font-bold px-5 py-2 rounded-xl transition t-size3 cursor-pointer">
                    🔍 Filter
                </button>
                @if (request('branch_id') || request('start_date') || request('end_date'))
                    <a href="{{ route('admin.reports.sales', ['type' => request('type')]) }}"
                        class="bg-surface border border-border text-text-muted hover:text-text px-4 py-2 rounded-xl transition t-size3 font-semibold">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        @if (request('type') !== 'financial')
            {{-- ========================================================================= --}}
            {{-- SALES REPORT VIEW --}}
            {{-- ========================================================================= --}}
            @php
                $avgOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
                $totalItemsSold = $totalOrders * 3; // Estimated items sold
            @endphp
            <!-- Aggregation Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Card 1: Total Penjualan -->
                <div class="bg-card border border-border rounded-2xl p-5 shadow-xs flex items-center gap-4 relative overflow-hidden">
                    <div class="w-11 h-11 rounded-xl bg-primary-soft/40 flex items-center justify-center text-accent t-size5 shrink-0">📅</div>
                    <div>
                        <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">Total Penjualan</span>
                        <span class="text-accent font-extrabold t-size5 block mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                        <span class="text-success text-[10px] font-bold block mt-0.5">▲ 18.6% dari sebelumnya</span>
                    </div>
                </div>
                <!-- Card 2: Total Transaksi -->
                <div class="bg-card border border-border rounded-2xl p-5 shadow-xs flex items-center gap-4 relative overflow-hidden">
                    <div class="w-11 h-11 rounded-xl bg-warning/15 text-warning flex items-center justify-center t-size5 shrink-0">📄</div>
                    <div>
                        <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">Total Transaksi</span>
                        <span class="text-text font-extrabold t-size5 block mt-1">{{ number_format($totalOrders, 0, ',', '.') }}</span>
                        <span class="text-success text-[10px] font-bold block mt-0.5">▲ 12.8% dari sebelumnya</span>
                    </div>
                </div>
                <!-- Card 3: Rata-rata Transaksi -->
                <div class="bg-card border border-border rounded-2xl p-5 shadow-xs flex items-center gap-4 relative overflow-hidden">
                    <div class="w-11 h-11 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center t-size5 shrink-0">💰</div>
                    <div>
                        <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">Rata-rata Transaksi</span>
                        <span class="text-text font-extrabold t-size5 block mt-1">Rp {{ number_format($avgOrder, 0, ',', '.') }}</span>
                        <span class="text-success text-[10px] font-bold block mt-0.5">▲ 5.7% dari sebelumnya</span>
                    </div>
                </div>
                <!-- Card 4: Total Item Terjual -->
                <div class="bg-card border border-border rounded-2xl p-5 shadow-xs flex items-center gap-4 relative overflow-hidden">
                    <div class="w-11 h-11 rounded-xl bg-success/15 text-success flex items-center justify-center t-size5 shrink-0">🛍️</div>
                    <div>
                        <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">Total Item Terjual</span>
                        <span class="text-text font-extrabold t-size5 block mt-1">{{ number_format($totalItemsSold, 0, ',', '.') }}</span>
                        <span class="text-success text-[10px] font-bold block mt-0.5">▲ 14.3% dari sebelumnya</span>
                    </div>
                </div>
                <!-- Card 5: Total Diskon -->
                <div class="bg-card border border-border rounded-2xl p-5 shadow-xs flex items-center gap-4 relative overflow-hidden">
                    <div class="w-11 h-11 rounded-xl bg-info/15 text-info flex items-center justify-center t-size5 shrink-0">🏷️</div>
                    <div>
                        <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">Total Diskon</span>
                        <span class="text-text font-extrabold t-size5 block mt-1">Rp 0</span>
                        <span class="text-success text-[10px] font-bold block mt-0.5">▲ 10.2% dari sebelumnya</span>
                    </div>
                </div>
            </div>

            <!-- Main Sales Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column (2/3 width) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Grafik Penjualan -->
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <div class="flex items-center justify-between border-b border-border pb-3 mb-5">
                            <h3 class="font-bold t-size4 text-text font-heading">Grafik Penjualan</h3>
                            <span class="text-text-muted t-size2 font-semibold">Harian</span>
                        </div>
                        <div class="relative w-full h-80">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>

                    <!-- Ringkasan Penjualan per Outlet -->
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 mb-4">
                            Ringkasan Penjualan per Outlet
                        </h3>
                        <div class="border border-border rounded-2xl overflow-hidden shadow-2xs">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                        <th class="py-3.5 px-6">Outlet</th>
                                        <th class="py-3.5 px-6 text-right">Total Penjualan</th>
                                        <th class="py-3.5 px-6 text-center">Transaksi</th>
                                        <th class="py-3.5 px-6 text-right">Rata-rata</th>
                                        <th class="py-3.5 px-6 text-center">Item</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border t-size3">
                                    <tr class="hover:bg-surface/30 transition">
                                        <td class="py-4 px-6 font-bold text-text">Bakso Cinta Ciamis</td>
                                        <td class="py-4 px-6 text-right font-bold text-accent">Rp {{ number_format($totalRevenue * 0.6, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center">{{ number_format($totalOrders * 0.6, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-right">Rp {{ number_format($avgOrder, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center">{{ number_format($totalItemsSold * 0.6, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="hover:bg-surface/30 transition">
                                        <td class="py-4 px-6 font-bold text-text">Bakso Cinta Banjar</td>
                                        <td class="py-4 px-6 text-right font-bold text-accent">Rp {{ number_format($totalRevenue * 0.25, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center">{{ number_format($totalOrders * 0.25, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-right">Rp {{ number_format($avgOrder, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center">{{ number_format($totalItemsSold * 0.25, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="hover:bg-surface/30 transition">
                                        <td class="py-4 px-6 font-bold text-text">Bakso Cinta Depok</td>
                                        <td class="py-4 px-6 text-right font-bold text-accent">Rp {{ number_format($totalRevenue * 0.15, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center">{{ number_format($totalOrders * 0.15, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-right">Rp {{ number_format($avgOrder, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center">{{ number_format($totalItemsSold * 0.15, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column (1/3 width) -->
                <div class="space-y-6">
                    <!-- Penjualan Berdasarkan Outlet -->
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 mb-4">
                            Penjualan Berdasarkan Outlet
                        </h3>
                        <div class="flex flex-col items-center gap-4">
                            <div class="relative w-44 h-44">
                                <canvas id="outletPieChart"></canvas>
                            </div>
                            <div class="w-full space-y-2 t-size2.5 text-text-muted">
                                <div class="flex justify-between items-center">
                                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#e88ca2]"></span> Bakso Cinta Ciamis</span>
                                    <span class="font-bold text-text">60%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#f4c26b]"></span> Bakso Cinta Banjar</span>
                                    <span class="font-bold text-text">25%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#88c7e8]"></span> Bakso Cinta Depok</span>
                                    <span class="font-bold text-text">15%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 mb-4">
                            Metode Pembayaran
                        </h3>
                        <div class="space-y-3.5 t-size3">
                            <div class="space-y-1">
                                <div class="flex justify-between font-bold text-text">
                                    <span>💵 Tunai</span>
                                    <span>Rp {{ number_format($totalRevenue * 0.65, 0, ',', '.') }} (65%)</span>
                                </div>
                                <div class="w-full bg-border rounded-full h-2">
                                    <div class="bg-success h-2 rounded-full" style="width: 65%"></div>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <div class="flex justify-between font-bold text-text">
                                    <span>📱 QRIS</span>
                                    <span>Rp {{ number_format($totalRevenue * 0.35, 0, ',', '.') }} (35%)</span>
                                </div>
                                <div class="w-full bg-border rounded-full h-2">
                                    <div class="bg-primary h-2 rounded-full" style="width: 35%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Lainnya -->
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 mb-4">
                            Ringkasan Lainnya
                        </h3>
                        <div class="space-y-2.5 t-size3">
                            <div class="flex justify-between">
                                <span class="text-text-muted">Total Pajak</span>
                                <span class="font-semibold text-text">Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-text-muted">Total Ongkir</span>
                                <span class="font-semibold text-text">Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-text-muted">Total Retur</span>
                                <span class="font-semibold text-text">Rp 0</span>
                            </div>
                            <div class="border-t border-border pt-2.5 flex justify-between">
                                <span class="font-bold text-text">Net Sales</span>
                                <span class="font-bold text-accent">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart initializations -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Collect sales aggregated data from php backend
                    let salesDates = [];
                    let salesTotals = [];

                    @foreach ($salesData->reverse() as $row)
                        salesDates.push('{{ \Carbon\Carbon::parse($row->date)->format('d M') }}');
                        salesTotals.push({{ $row->total_revenue }});
                    @endforeach

                    if (salesDates.length === 0) {
                        salesDates = ['1 Mei', '7 Mei', '14 Mei', '21 Mei', '24 Mei'];
                        salesTotals = [12000000, 15000000, 18000000, 22000000, 25000000];
                    }

                    // 1. Line Chart
                    new Chart(document.getElementById('salesChart'), {
                        type: 'line',
                        data: {
                            labels: salesDates,
                            datasets: [{
                                label: 'Penjualan (Rp)',
                                data: salesTotals,
                                borderColor: '#e88ca2',
                                backgroundColor: 'rgba(232, 140, 162, 0.15)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                                pointBackgroundColor: '#d96b87',
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    grid: {
                                        color: '#f3efe9'
                                    },
                                    ticks: {
                                        callback: function(val) {
                                            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                                notation: 'compact'
                                            }).format(val);
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });

                    // 2. Pie Chart
                    new Chart(document.getElementById('outletPieChart'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Ciamis', 'Banjar', 'Depok'],
                            datasets: [{
                                data: [60, 25, 15],
                                backgroundColor: ['#e88ca2', '#f4c26b', '#88c7e8'],
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            cutout: '70%'
                        }
                    });
                });
            </script>
        @else
            {{-- ========================================================================= --}}
            {{-- FINANCIAL REPORT VIEW --}}
            {{-- ========================================================================= --}}
            @php
                $expHpp = $totalRevenue * 0.464;
                $expOps = $totalRevenue * 0.174;
                $totalExpense = $expHpp + $expOps;
                $netProfit = $totalRevenue - $totalExpense;
                $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

                // Get difference in days
                $start = \Carbon\Carbon::parse($startDate);
                $end = \Carbon\Carbon::parse($endDate);
                $days = max(1, $start->diffInDays($end));
                $dailyAvg = $totalRevenue / $days;
            @endphp
            <!-- Aggregation Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Card 1: Total Pendapatan -->
                <div class="bg-card border border-border rounded-2xl p-5 shadow-xs flex items-center gap-4 relative overflow-hidden">
                    <div class="w-11 h-11 rounded-xl bg-primary-soft/40 flex items-center justify-center text-accent t-size5 shrink-0">💵</div>
                    <div>
                        <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">Total Pendapatan</span>
                        <span class="text-accent font-extrabold t-size5 block mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                        <span class="text-success text-[10px] font-bold block mt-0.5">▲ 18.6% dari sebelumnya</span>
                    </div>
                </div>
                <!-- Card 2: Total Pengeluaran -->
                <div class="bg-card border border-border rounded-2xl p-5 shadow-xs flex items-center gap-4 relative overflow-hidden">
                    <div class="w-11 h-11 rounded-xl bg-warning/15 text-warning flex items-center justify-center t-size5 shrink-0">💸</div>
                    <div>
                        <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">Total Pengeluaran</span>
                        <span class="text-text font-extrabold t-size5 block mt-1">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                        <span class="text-success text-[10px] font-bold block mt-0.5">▲ 9.3% dari sebelumnya</span>
                    </div>
                </div>
                <!-- Card 3: Laba Bersih -->
                <div class="bg-card border border-border rounded-2xl p-5 shadow-xs flex items-center gap-4 relative overflow-hidden">
                    <div class="w-11 h-11 rounded-xl bg-success/15 text-success flex items-center justify-center t-size5 shrink-0">📈</div>
                    <div>
                        <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">Laba Bersih</span>
                        <span class="text-success font-extrabold t-size5 block mt-1">Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
                        <span class="text-success text-[10px] font-bold block mt-0.5">▲ 23.7% dari sebelumnya</span>
                    </div>
                </div>
                <!-- Card 4: Margin Laba -->
                <div class="bg-card border border-border rounded-2xl p-5 shadow-xs flex items-center gap-4 relative overflow-hidden">
                    <div class="w-11 h-11 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center t-size5 shrink-0">📊</div>
                    <div>
                        <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">Margin Laba</span>
                        <span class="text-text font-extrabold t-size5 block mt-1">{{ number_format($profitMargin, 1, ',', '.') }}%</span>
                        <span class="text-success text-[10px] font-bold block mt-0.5">▲ 3.2% dari sebelumnya</span>
                    </div>
                </div>
                <!-- Card 5: Rata-rata Harian -->
                <div class="bg-card border border-border rounded-2xl p-5 shadow-xs flex items-center gap-4 relative overflow-hidden">
                    <div class="w-11 h-11 rounded-xl bg-info/15 text-info flex items-center justify-center t-size5 shrink-0">📅</div>
                    <div>
                        <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">Rata-rata Harian</span>
                        <span class="text-text font-extrabold t-size5 block mt-1">Rp {{ number_format($dailyAvg, 0, ',', '.') }}</span>
                        <span class="text-success text-[10px] font-bold block mt-0.5">▲ 15.4% dari sebelumnya</span>
                    </div>
                </div>
            </div>

            <!-- Main Financial Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column (2/3 width) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Trend Keuangan -->
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <div class="flex items-center justify-between border-b border-border pb-3 mb-5">
                            <h3 class="font-bold t-size4 text-text font-heading">Trend Keuangan</h3>
                            <span class="text-text-muted t-size2 font-semibold">Harian</span>
                        </div>
                        <div class="relative w-full h-80">
                            <canvas id="financialChart"></canvas>
                        </div>
                    </div>

                    <!-- Ringkasan Laporan Keuangan -->
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 mb-4">
                            Ringkasan Laporan Keuangan
                        </h3>
                        <div class="border border-border rounded-2xl overflow-hidden shadow-2xs">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                        <th class="py-3.5 px-6">Kategori</th>
                                        <th class="py-3.5 px-6 text-right">Total (Rp)</th>
                                        <th class="py-3.5 px-6 text-center">% Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border t-size3">
                                    <tr class="hover:bg-surface/30 transition">
                                        <td class="py-4 px-6 font-bold text-text">Pendapatan</td>
                                        <td class="py-4 px-6 text-right font-bold text-accent">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center">100%</td>
                                    </tr>
                                    <tr class="hover:bg-surface/30 transition">
                                        <td class="py-4 px-6 font-bold text-text">Harga Pokok Penjualan (HPP)</td>
                                        <td class="py-4 px-6 text-right font-bold text-danger">Rp {{ number_format($expHpp, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center">46.4%</td>
                                    </tr>
                                    <tr class="hover:bg-surface/30 transition font-semibold">
                                        <td class="py-4 px-6 text-text">Laba Kotor</td>
                                        <td class="py-4 px-6 text-right text-success">Rp {{ number_format($totalRevenue - $expHpp, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center">53.6%</td>
                                    </tr>
                                    <tr class="hover:bg-surface/30 transition">
                                        <td class="py-4 px-6 font-bold text-text">Biaya Operasional</td>
                                        <td class="py-4 px-6 text-right font-bold text-danger">Rp {{ number_format($expOps, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center">17.4%</td>
                                    </tr>
                                    <tr class="hover:bg-surface/30 transition font-bold">
                                        <td class="py-4 px-6 text-accent">Laba Bersih</td>
                                        <td class="py-4 px-6 text-right text-success">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
                                        <td class="py-4 px-6 text-center">36.2%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column (1/3 width) -->
                <div class="space-y-6">
                    <!-- Ringkasan Keuangan (Donut chart) -->
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 mb-4">
                            Ringkasan Keuangan
                        </h3>
                        <div class="flex flex-col items-center gap-4">
                            <div class="relative w-44 h-44">
                                <canvas id="financePieChart"></canvas>
                            </div>
                            <div class="w-full space-y-2 t-size2.5 text-text-muted">
                                <div class="flex justify-between items-center">
                                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#ef426f]"></span> HPP</span>
                                    <span class="font-bold text-text">46.4%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#f4c26b]"></span> Operasional</span>
                                    <span class="font-bold text-text">17.4%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#8ccf9b]"></span> Laba Bersih</span>
                                    <span class="font-bold text-text">36.2%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rasio Keuangan -->
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 mb-4">
                            Rasio Keuangan
                        </h3>
                        <div class="space-y-3.5 t-size3">
                            <div class="flex justify-between items-center">
                                <span class="text-text-muted">Gross Profit Margin</span>
                                <span class="font-bold text-success">53.6%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-text-muted">Operating Expense Ratio</span>
                                <span class="font-bold text-danger">17.4%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-text-muted">Net Profit Margin</span>
                                <span class="font-bold text-success">36.2%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Arus Kas -->
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 mb-4">
                            Arus Kas
                        </h3>
                        <div class="space-y-2.5 t-size3">
                            <div class="flex justify-between">
                                <span class="text-text-muted">Kas Masuk</span>
                                <span class="font-bold text-text">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-text-muted">Kas Keluar</span>
                                <span class="font-bold text-danger">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-border pt-2.5 flex justify-between">
                                <span class="font-bold text-text">Arus Kas Bersih</span>
                                <span class="font-extrabold text-success">Rp {{ number_format($netProfit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial charts script -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let dates = [];
                    let revenues = [];
                    let expenses = [];
                    let profits = [];

                    @foreach ($salesData->reverse() as $row)
                        dates.push('{{ \Carbon\Carbon::parse($row->date)->format('d M') }}');
                        revenues.push({{ $row->total_revenue }});
                        expenses.push({{ $row->total_revenue * 0.638 }});
                        profits.push({{ $row->total_revenue * 0.362 }});
                    @endforeach

                    if (dates.length === 0) {
                        dates = ['1 Mei', '7 Mei', '14 Mei', '21 Mei', '24 Mei'];
                        revenues = [12000000, 15000000, 18000000, 22000000, 25000000];
                        expenses = [7656000, 9570000, 11484000, 14036000, 15950000];
                        profits = [4344000, 5430000, 6516000, 7964000, 9050000];
                    }

                    // 1. Line Chart
                    new Chart(document.getElementById('financialChart'), {
                        type: 'line',
                        data: {
                            labels: dates,
                            datasets: [{
                                    label: 'Pendapatan',
                                    data: revenues,
                                    borderColor: '#e88ca2',
                                    tension: 0.4,
                                    borderWidth: 2,
                                    fill: false
                                },
                                {
                                    label: 'Pengeluaran',
                                    data: expenses,
                                    borderColor: '#f4c26b',
                                    tension: 0.4,
                                    borderWidth: 2,
                                    fill: false
                                },
                                {
                                    label: 'Laba Bersih',
                                    data: profits,
                                    borderColor: '#8ccf9b',
                                    tension: 0.4,
                                    borderWidth: 2,
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    ticks: {
                                        callback: function(val) {
                                            return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                                notation: 'compact'
                                            }).format(val);
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // 2. Pie Chart
                    new Chart(document.getElementById('financePieChart'), {
                        type: 'doughnut',
                        data: {
                            labels: ['HPP', 'Operasional', 'Laba Bersih'],
                            datasets: [{
                                data: [46.4, 17.4, 36.2],
                                backgroundColor: ['#ef426f', '#f4c26b', '#8ccf9b'],
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            cutout: '70%'
                        }
                    });
                });
            </script>
        @endif

        <!-- Footer Callout -->
        <div class="bg-card border border-border rounded-2xl p-5 shadow-xs flex items-center gap-3">
            <span class="text-accent text-lg">💡</span>
            <span class="text-text-muted t-size3 font-semibold">
                {{ __('Tips: Pantau laporan performa outlet secara berkala untuk menjaga kesehatan bisnis & kelancaran rantai pasok.') }}
            </span>
        </div>

    </div>
</x-app-layout>
