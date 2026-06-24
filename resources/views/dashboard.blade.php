<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Ringkasan Dasbor') }}
        </h2>
    </x-slot>

    <div class="space-y-8 anim-fade">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-br from-primary-soft/30 via-card to-secondary/40 border border-border rounded-2xl p-6 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-40 h-40 rounded-full bg-primary-soft/20"></div>
            <div class="absolute -left-8 -bottom-8 w-28 h-28 rounded-full bg-secondary/30"></div>
            <div class="relative z-10">
                <h3 class="font-extrabold t-size6 text-text font-heading">
                    {{ __('Selamat Datang Kembali') }}, {{ Auth::user()->name }}!
                </h3>
                <p class="text-text-muted t-size3 mt-1">
                    @if(Auth::user()->branch)
                        {{ __('Memantau aktivitas operasional Cabang') }} <span class="font-bold text-accent">{{ Auth::user()->branch->name }}</span>.
                    @else
                        {{ __('Memantau aktivitas operasional untuk') }} <span class="font-bold text-accent">{{ __('Semua Cabang (HQ)') }}</span>.
                    @endif
                </p>
            </div>
            <div class="relative z-10 bg-card/80 backdrop-blur-sm border border-border px-4 py-2 rounded-xl text-text-muted t-size2 font-semibold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        <!-- Metric Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Pendapatan Hari Ini -->
            <div class="bg-card border border-border rounded-2xl p-5 shadow-xs hover:shadow-md transition-all duration-300 hover:-translate-y-0.5 group">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-text-muted t-size2 font-bold uppercase tracking-wider">
                        {{ __('Pendapatan Hari Ini') }}
                    </span>
                    <div class="w-10 h-10 bg-primary-soft/30 text-accent rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <span class="text-accent font-extrabold t-size6 block">
                    Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                </span>
                @php
                    $revDiff = $todayRevenue - $yesterdayRevenue;
                @endphp
                <div class="mt-2 flex items-center gap-1.5 t-size1 font-semibold {{ $revDiff >= 0 ? 'text-green-600' : 'text-red-500' }}">
                    @if($revDiff >= 0)
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path></svg>
                    @else
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    @endif
                    <span>{{ $yesterdayRevenue > 0 ? abs(round(($revDiff / $yesterdayRevenue) * 100)) . '%' : ($revDiff > 0 ? '∞' : '0%') }} vs kemarin</span>
                </div>
                <!-- Sparkline -->
                <div id="revenue-sparkline" class="mt-3 h-10 w-full"></div>
            </div>

            <!-- Card 2: Total Pesanan Hari Ini -->
            <div class="bg-card border border-border rounded-2xl p-5 shadow-xs hover:shadow-md transition-all duration-300 hover:-translate-y-0.5 group">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-text-muted t-size2 font-bold uppercase tracking-wider">
                        {{ __('Pesanan Hari Ini') }}
                    </span>
                    <div class="w-10 h-10 bg-info-soft text-info rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <span class="text-info font-extrabold t-size6 block">
                    {{ $todayOrdersCount }} {{ __('Pesanan') }}
                </span>
                @php
                    $ordDiff = $todayOrdersCount - $yesterdayOrdersCount;
                @endphp
                <div class="mt-2 flex items-center gap-1.5 t-size1 font-semibold {{ $ordDiff >= 0 ? 'text-green-600' : 'text-red-500' }}">
                    @if($ordDiff >= 0)
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path></svg>
                    @else
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    @endif
                    <span>{{ $yesterdayOrdersCount > 0 ? abs(round(($ordDiff / $yesterdayOrdersCount) * 100)) . '%' : ($ordDiff > 0 ? '∞' : '0%') }} vs kemarin</span>
                </div>
                <!-- Sparkline -->
                <div id="orders-sparkline" class="mt-3 h-10 w-full"></div>
            </div>

            <!-- Card 3: Pesanan Pending -->
            <div class="bg-card border border-border rounded-2xl p-5 shadow-xs hover:shadow-md transition-all duration-300 hover:-translate-y-0.5 group {{ $pendingOrdersCount > 0 ? 'border-l-4 border-l-warning' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-text-muted t-size2 font-bold uppercase tracking-wider">
                        {{ __('Pesanan Pending') }}
                    </span>
                    <div class="w-10 h-10 bg-warning-soft text-warning rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 {{ $pendingOrdersCount > 0 ? 'anim-pulse-soft' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <span class="text-warning font-extrabold t-size6 block">
                    {{ $pendingOrdersCount }} {{ __('Antrean') }}
                </span>
                <div class="mt-2 t-size1 font-semibold text-text-muted">
                    {{ __('Menunggu konfirmasi') }}
                </div>
            </div>

            <!-- Card 4: Peringatan Stok -->
            <div class="bg-card border border-border rounded-2xl p-5 shadow-xs hover:shadow-md transition-all duration-300 hover:-translate-y-0.5 group {{ $lowStockCount > 0 ? 'border-l-4 border-l-danger' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-text-muted t-size2 font-bold uppercase tracking-wider">
                        {{ __('Stok Menipis') }}
                    </span>
                    <div class="w-10 h-10 bg-danger-soft text-danger rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 {{ $lowStockCount > 0 ? 'anim-pulse-soft' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
                <span class="text-danger font-extrabold t-size6 block">
                    {{ $lowStockCount }} {{ __('Item') }}
                </span>
                <div class="mt-2 t-size1 font-semibold text-text-muted">
                    {{ __('Perlu restock segera') }}
                </div>
            </div>
        </div>

        <!-- Revenue & Orders Charts (ECharts) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                <h3 class="font-bold t-size5 text-text font-heading mb-2">
                    {{ __('Tren Pendapatan (7 Hari)') }}
                </h3>
                <p class="text-text-muted t-size2 mb-4">{{ __('Pendapatan harian berdasarkan pembayaran berhasil') }}</p>
                <div id="revenue-chart" class="w-full h-64"></div>
            </div>
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                <h3 class="font-bold t-size5 text-text font-heading mb-2">
                    {{ __('Tren Pesanan (7 Hari)') }}
                </h3>
                <p class="text-text-muted t-size2 mb-4">{{ __('Jumlah pesanan masuk per hari') }}</p>
                <div id="orders-chart" class="w-full h-64"></div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left: Recent Orders -->
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold t-size5 text-text font-heading">
                        {{ __('Pesanan Terbaru') }}
                    </h3>
                    @if(Auth::user()->role?->name === 'cashier')
                        <a href="{{ route('cashier.orders') }}" class="text-accent hover:text-primary font-bold t-size2 transition">
                            {{ __('Lihat Semua') }} &rarr;
                        </a>
                    @endif
                </div>

                @if($recentOrders->isEmpty())
                    <div class="py-8 text-center space-y-3">
                        <div class="w-12 h-12 bg-surface-alt text-text-muted rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <p class="text-text-muted t-size3">{{ __('Belum ada pesanan masuk hari ini.') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 px-6">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4 rounded-tl-lg">{{ __('ID') }}</th>
                                    @if(!Auth::user()->branch)
                                        <th class="py-3 px-4">{{ __('Cabang') }}</th>
                                    @endif
                                    <th class="py-3 px-4">{{ __('Meja') }}</th>
                                    <th class="py-3 px-4">{{ __('Total') }}</th>
                                    <th class="py-3 px-4">{{ __('Status') }}</th>
                                    <th class="py-3 px-4 text-right rounded-tr-lg">{{ __('Waktu') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($recentOrders as $order)
                                    <tr class="hover:bg-primary-soft/5 transition-colors duration-150">
                                        <td class="py-3 px-4 font-bold text-text t-size3">
                                            #{{ $order->id }}
                                        </td>
                                        @if(!Auth::user()->branch)
                                            <td class="py-3 px-4 t-size2 text-text-muted truncate max-w-[120px]">
                                                {{ $order->branch->name ?? 'HQ' }}
                                            </td>
                                        @endif
                                        <td class="py-3 px-4">
                                            <span class="bg-primary-soft/40 text-accent font-bold px-2 py-0.5 rounded-full t-size1 border border-primary-soft/50">
                                                {{ $order->table_number }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 font-bold text-accent t-size3">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <x-status-badge :status="$order->status" />
                                        </td>
                                        <td class="py-3 px-4 text-right t-size2 text-text-muted">
                                            {{ $order->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Right: Stock Warnings / Operational Alerts -->
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold t-size5 text-text font-heading">
                        {{ __('Peringatan Stok Operasional') }}
                    </h3>
                    @if(Auth::user()->role?->name === 'admin')
                        <a href="{{ route('admin.stocks.index') }}" class="text-accent hover:text-primary font-bold t-size2 transition">
                            {{ __('Kelola') }} &rarr;
                        </a>
                    @endif
                </div>

                @if($lowStockItems->isEmpty())
                    <div class="py-8 text-center space-y-3">
                        <div class="w-12 h-12 bg-success-soft text-success rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-text-muted t-size3">
                            {{ __('Semua bahan dan stok berada dalam kondisi aman.') }}
                        </p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($lowStockItems as $item)
                            <div class="flex items-center justify-between p-4 bg-danger-soft/60 border border-danger/20 rounded-xl hover:bg-danger-soft transition-colors duration-150">
                                <div class="space-y-1">
                                    <h4 class="font-bold t-size3 text-text">
                                        {{ $item->name }}
                                    </h4>
                                    @if(!Auth::user()->branch)
                                        <span class="text-[10px] text-text-muted uppercase font-semibold">
                                            Cabang: {{ $item->branch->name ?? 'HQ' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right space-y-1">
                                    <x-status-badge status="low_stock" />
                                    <span class="block text-[10px] text-text-muted/70 font-semibold">
                                        {{ $item->quantity }} / {{ $item->minimum_quantity }} {{ $item->unit }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ECharts Scripts --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const echarts = window.echarts;
            if (!echarts) return;

            initCharts(echarts);
        });

        function initCharts(echarts) {
            const pastelPink = '#e88ca2';
            const pastelPinkSoft = '#f5b7c5';
            const pastelBlue = '#88c7e8';
            const pastelBlueSoft = '#cfe8f6';

            // ── Revenue Sparkline (mini) ──
            const revSparkEl = document.getElementById('revenue-sparkline');
            if (revSparkEl) {
                const revSpark = echarts.init(revSparkEl);
                revSpark.setOption({
                    grid: { left: 0, right: 0, top: 2, bottom: 2 },
                    xAxis: { type: 'category', show: false, data: {!! json_encode($revenueTrend->pluck('date')) !!} },
                    yAxis: { type: 'value', show: false },
                    series: [{
                        type: 'line',
                        data: {!! json_encode($revenueTrend->pluck('revenue')) !!},
                        smooth: true,
                        symbol: 'none',
                        lineStyle: { width: 2, color: pastelPink },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: 'rgba(232,140,162,0.3)' },
                                { offset: 1, color: 'rgba(232,140,162,0.02)' }
                            ])
                        }
                    }],
                    tooltip: { show: false }
                });
                window.addEventListener('resize', () => revSpark.resize());
            }

            // ── Orders Sparkline (mini) ──
            const ordSparkEl = document.getElementById('orders-sparkline');
            if (ordSparkEl) {
                const ordSpark = echarts.init(ordSparkEl);
                ordSpark.setOption({
                    grid: { left: 0, right: 0, top: 2, bottom: 2 },
                    xAxis: { type: 'category', show: false, data: {!! json_encode($ordersTrend->pluck('date')) !!} },
                    yAxis: { type: 'value', show: false },
                    series: [{
                        type: 'line',
                        data: {!! json_encode($ordersTrend->pluck('orders')) !!},
                        smooth: true,
                        symbol: 'none',
                        lineStyle: { width: 2, color: pastelBlue },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: 'rgba(136,199,232,0.3)' },
                                { offset: 1, color: 'rgba(136,199,232,0.02)' }
                            ])
                        }
                    }],
                    tooltip: { show: false }
                });
                window.addEventListener('resize', () => ordSpark.resize());
            }

            // ── Revenue Chart (full) ──
            const revChartEl = document.getElementById('revenue-chart');
            if (revChartEl) {
                const revChart = echarts.init(revChartEl);
                revChart.setOption({
                    grid: { left: 60, right: 20, top: 20, bottom: 30 },
                    xAxis: {
                        type: 'category',
                        data: {!! json_encode($revenueTrend->pluck('date')) !!},
                        axisLine: { lineStyle: { color: '#e9d9d0' } },
                        axisLabel: { color: '#7d6e6e', fontSize: 11, fontFamily: 'Inter' },
                        axisTick: { show: false }
                    },
                    yAxis: {
                        type: 'value',
                        axisLine: { show: false },
                        axisLabel: {
                            color: '#7d6e6e', fontSize: 11, fontFamily: 'Inter',
                            formatter: function(v) { return 'Rp ' + (v / 1000).toFixed(0) + 'k'; }
                        },
                        splitLine: { lineStyle: { color: '#f8efe8', type: 'dashed' } }
                    },
                    tooltip: {
                        trigger: 'axis',
                        backgroundColor: '#fff',
                        borderColor: '#e9d9d0',
                        textStyle: { color: '#3a2e2e', fontFamily: 'Inter', fontSize: 12 },
                        formatter: function(params) {
                            return params[0].name + '<br>Rp ' + params[0].value.toLocaleString('id-ID');
                        }
                    },
                    series: [{
                        type: 'bar',
                        data: {!! json_encode($revenueTrend->pluck('revenue')) !!},
                        itemStyle: {
                            borderRadius: [8, 8, 0, 0],
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: pastelPink },
                                { offset: 1, color: pastelPinkSoft }
                            ])
                        },
                        barWidth: '45%',
                        emphasis: {
                            itemStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                    { offset: 0, color: '#d96b87' },
                                    { offset: 1, color: pastelPink }
                                ])
                            }
                        }
                    }]
                });
                window.addEventListener('resize', () => revChart.resize());
            }

            // ── Orders Chart (full) ──
            const ordChartEl = document.getElementById('orders-chart');
            if (ordChartEl) {
                const ordChart = echarts.init(ordChartEl);
                ordChart.setOption({
                    grid: { left: 40, right: 20, top: 20, bottom: 30 },
                    xAxis: {
                        type: 'category',
                        data: {!! json_encode($ordersTrend->pluck('date')) !!},
                        axisLine: { lineStyle: { color: '#e9d9d0' } },
                        axisLabel: { color: '#7d6e6e', fontSize: 11, fontFamily: 'Inter' },
                        axisTick: { show: false }
                    },
                    yAxis: {
                        type: 'value',
                        axisLine: { show: false },
                        axisLabel: { color: '#7d6e6e', fontSize: 11, fontFamily: 'Inter' },
                        splitLine: { lineStyle: { color: '#f8efe8', type: 'dashed' } },
                        minInterval: 1
                    },
                    tooltip: {
                        trigger: 'axis',
                        backgroundColor: '#fff',
                        borderColor: '#e9d9d0',
                        textStyle: { color: '#3a2e2e', fontFamily: 'Inter', fontSize: 12 },
                        formatter: function(params) {
                            return params[0].name + '<br>' + params[0].value + ' pesanan';
                        }
                    },
                    series: [{
                        type: 'line',
                        data: {!! json_encode($ordersTrend->pluck('orders')) !!},
                        smooth: true,
                        symbol: 'circle',
                        symbolSize: 8,
                        lineStyle: { width: 3, color: pastelBlue },
                        itemStyle: { color: pastelBlue, borderColor: '#fff', borderWidth: 2 },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: 'rgba(136,199,232,0.25)' },
                                { offset: 1, color: 'rgba(136,199,232,0.02)' }
                            ])
                        },
                        emphasis: {
                            itemStyle: { borderColor: pastelBlue, borderWidth: 3, shadowBlur: 10, shadowColor: 'rgba(136,199,232,0.4)' }
                        }
                    }]
                });
                window.addEventListener('resize', () => ordChart.resize());
            }
        }
    </script>
    @endpush
</x-app-layout>
