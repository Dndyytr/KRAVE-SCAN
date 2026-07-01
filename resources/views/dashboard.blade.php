<x-app-layout>
    @php
        $revenueDifference = $todayRevenue - $yesterdayRevenue;
        $ordersDifference = $todayOrdersCount - $yesterdayOrdersCount;
        $revenueNote =
            $yesterdayRevenue > 0
                ? abs(round(($revenueDifference / $yesterdayRevenue) * 100)) . '% dari kemarin'
                : ($todayRevenue > 0
                    ? 'Ada transaksi hari ini'
                    : 'Belum ada transaksi');
        $ordersNote =
            $yesterdayOrdersCount > 0
                ? abs(round(($ordersDifference / $yesterdayOrdersCount) * 100)) . '% dari kemarin'
                : ($todayOrdersCount > 0
                    ? 'Ada pesanan hari ini'
                    : 'Belum ada pesanan');
        $ordersUrl = match (true) {
            Auth::user()->hasPermission('view_all_orders') => route('admin.orders.index'),
            Auth::user()->hasPermission('access_cashier') => route('cashier.orders'),
            Auth::user()->hasPermission('access_kitchen') => route('kitchen.orders'),
            default => null,
        };
        $isCashierDashboard = Auth::user()->hasPermission('access_cashier');
    @endphp

    <div class="anim-fade space-y-5">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-heading t-size8 font-extrabold tracking-tight text-[#21171a]">
                    Selamat datang, {{ Auth::user()->name }}! <span aria-hidden="true">👋</span>
                </h1>
                <p class="mt-1 t-size2 text-text-muted">
                    @if (Auth::user()->branch)
                        Ringkasan aktivitas {{ Auth::user()->branch->name }} hari ini.
                    @else
                        Ringkasan aktivitas sistem Bakso Cinta hari ini.
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if (Auth::user()->branch_id === null && isset($globalBranches))
                    <form action="{{ route('admin.switch-branch') }}" method="POST" id="branch-switcher-form">
                        @csrf
                        <select name="branch_id" onchange="this.form.submit()"
                            class="h-12 rounded-xl border-[#f2d9df] bg-white px-4 t-size1 font-semibold text-text shadow-sm focus:border-[#ff6385] focus:ring-[#ff6385]">
                            <option value="">Semua Cabang</option>
                            @foreach ($globalBranches as $branch)
                                <option value="{{ $branch->id }}" @selected(session('active_branch_id') == $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif

                <div class="flex h-12 items-center gap-3 rounded-xl border border-[#f2d9df] bg-white px-4 shadow-sm">
                    <svg viewBox="0 0 24 24" class="h-5 w-5 text-[#ff3868]" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="5" width="18" height="16" rx="2" />
                        <path d="M16 3v4M8 3v4M3 10h18" />
                    </svg>
                    <div>
                        <p class="t-size1 font-bold text-text">{{ now()->translatedFormat('l, d F Y') }}</p>
                        <p class="text-[10px] text-text-muted">{{ now()->format('H:i') }} WIB</p>
                    </div>
                </div>
                <div class="hidden lg:block"><x-notification-bell /></div>
            </div>
        </header>

        <section class="grid grid-cols-1 gap-4 bp400:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan hari ini">
            <x-admin.stat-card label="Total Penjualan Hari Ini" :value="'Rp ' . number_format($todayRevenue, 0, ',', '.')" tone="pink" icon="money" :note="$revenueNote" :positive="$revenueDifference >= 0" />
            <x-admin.stat-card label="Total Pesanan Hari Ini" :value="number_format($todayOrdersCount, 0, ',', '.')" tone="orange" icon="orders" :note="$ordersNote" :positive="$ordersDifference >= 0" />
            <x-admin.stat-card label="Pesanan Menunggu" :value="number_format($pendingOrdersCount, 0, ',', '.')" tone="purple" icon="pending" note="Menunggu konfirmasi" :positive="false" />
            <x-admin.stat-card label="Stok Menipis" :value="number_format($lowStockCount, 0, ',', '.')" tone="green" icon="stock" note="Perlu segera ditindaklanjuti" :positive="false" />
        </section>

        <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
            @if ($isCashierDashboard)
                <article class="rounded-2xl border border-[#f3e2e5] bg-white p-4 shadow-[0_8px_30px_rgba(86,37,47,.04)] md:p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h2 class="font-heading t-size4 font-bold">Pesanan Aktif</h2>
                            <span class="rounded-full bg-[#ff3868] px-2 py-0.5 text-[10px] font-bold text-white">{{ $activeOrdersCount }}</span>
                        </div>
                        <a href="{{ route('cashier.orders') }}" class="t-size1 font-bold text-[#ff3868] hover:text-[#dd244e]">Lihat Semua</a>
                    </div>

                    <div class="mt-3 space-y-2.5">
                        @forelse ($activeOrders as $order)
                            <a href="{{ route('cashier.orders.show', $order) }}"
                                class="flex items-center gap-3 rounded-xl border border-[#f5e2e6] bg-[#fffafb] p-3 hover:border-[#ffb3c3] hover:bg-[#fff3f6]">
                                <span
                                    class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[#ffd8e0] bg-white t-size1 font-extrabold text-[#ff3868]">
                                    {{ $order->table_number }}
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate t-size1 font-bold text-text">
                                        {{ $order->orderItems->pluck('menu.name')->filter()->join(', ') ?: 'Pesanan meja ' . $order->table_number }}
                                    </span>
                                    <span class="mt-1 block text-[10px] text-text-muted">{{ $order->created_at->format('H:i') }} WIB</span>
                                </span>
                                <x-status-badge :status="$order->status" />
                            </a>
                        @empty
                            <div class="rounded-xl bg-[#ecfaf1] px-4 py-12 text-center t-size2 font-semibold text-emerald-700">Tidak ada pesanan aktif.</div>
                        @endforelse
                    </div>
                </article>
            @endif

            <article
                class="rounded-2xl border border-[#f3e2e5] bg-white p-4 shadow-[0_8px_30px_rgba(86,37,47,.04)] md:p-5 {{ $isCashierDashboard ? '' : 'xl:col-span-2' }}">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-heading t-size4 font-bold">Grafik Penjualan</h2>
                        <p class="mt-1 t-size1 text-text-muted">Pembayaran berhasil selama 7 hari terakhir</p>
                    </div>
                    <span class="rounded-lg border border-[#f1e2e4] px-3 py-2 t-size1 font-semibold text-text-muted">7 Hari Terakhir</span>
                </div>
                <div id="revenue-chart" class="mt-4 h-[280px] w-full md:h-[330px]"></div>
            </article>

            <article class="rounded-2xl border border-[#f3e2e5] bg-white p-4 shadow-[0_8px_30px_rgba(86,37,47,.04)] md:p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-heading t-size4 font-bold">Pesanan Terbaru</h2>
                    @if ($ordersUrl)
                        <a href="{{ $ordersUrl }}" class="t-size1 font-bold text-[#ff3868] hover:text-[#dd244e]">Lihat Semua</a>
                    @endif
                </div>

                <div class="mt-3 divide-y divide-[#f6e9eb]">
                    @forelse ($recentOrders as $order)
                        <div class="flex items-center gap-3 py-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#fff0f4] text-[#ff3868]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="5" y="3" width="14" height="18" rx="2" />
                                    <path d="M9 7h6M9 11h6" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate t-size1 font-bold text-text">#BC-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                                <p class="truncate text-[10px] text-text-muted">Meja {{ $order->table_number }} · {{ $order->created_at->format('H:i') }} WIB
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="t-size1 font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                <div class="mt-1"><x-status-badge :status="$order->status" /></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center t-size2 text-text-muted">Belum ada pesanan.</div>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
            @if ($isCashierDashboard)
                <article class="rounded-2xl border border-[#f3e2e5] bg-white p-4 shadow-[0_8px_30px_rgba(86,37,47,.04)] md:p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h2 class="font-heading t-size4 font-bold">Pesanan Aktif</h2>
                            <span class="rounded-full bg-[#ff3868] px-2 py-0.5 text-[10px] font-bold text-white">{{ $activeOrdersCount }}</span>
                        </div>
                        <a href="{{ route('cashier.orders') }}" class="t-size1 font-bold text-[#ff3868] hover:text-[#dd244e]">Lihat Semua</a>
                    </div>

                    <div class="mt-3 space-y-2.5">
                        @forelse ($activeOrders as $order)
                            <a href="{{ route('cashier.orders.show', $order) }}"
                                class="flex items-center gap-3 rounded-xl border border-[#f5e2e6] bg-[#fffafb] p-3 hover:border-[#ffb3c3] hover:bg-[#fff3f6]">
                                <span
                                    class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-[#ffd8e0] bg-white t-size1 font-extrabold text-[#ff3868]">
                                    {{ $order->table_number }}
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate t-size1 font-bold text-text">
                                        {{ $order->orderItems->pluck('menu.name')->filter()->join(', ') ?: 'Pesanan meja ' . $order->table_number }}
                                    </span>
                                    <span class="mt-1 block text-[10px] text-text-muted">{{ $order->created_at->format('H:i') }} WIB</span>
                                </span>
                                <x-status-badge :status="$order->status" />
                            </a>
                        @empty
                            <div class="rounded-xl bg-[#ecfaf1] px-4 py-12 text-center t-size2 font-semibold text-emerald-700">Tidak ada pesanan aktif.</div>
                        @endforelse
                    </div>
                </article>
            @endif

            <article
                class="rounded-2xl border border-[#f3e2e5] bg-white p-4 shadow-[0_8px_30px_rgba(86,37,47,.04)] md:p-5 {{ $isCashierDashboard ? '' : 'xl:col-span-2' }}">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-heading t-size4 font-bold">Aktivitas Pesanan</h2>
                        <p class="mt-1 t-size1 text-text-muted">Jumlah pesanan masuk selama 7 hari terakhir</p>
                    </div>
                    <span class="rounded-lg bg-[#fff1f4] px-3 py-2 t-size1 font-bold text-[#ff3868]">{{ $ordersTrend->sum('orders') }} Pesanan</span>
                </div>
                <div id="orders-chart" class="mt-4 h-[245px] w-full"></div>
            </article>

            <article class="rounded-2xl border border-[#f3e2e5] bg-white p-4 shadow-[0_8px_30px_rgba(86,37,47,.04)] md:p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-heading t-size4 font-bold">Peringatan Stok</h2>
                        <p class="mt-1 t-size1 text-text-muted">Item pada batas minimum</p>
                    </div>
                    @if (Auth::user()->hasPermission('manage_stocks'))
                        <a href="{{ route('admin.stocks.index') }}" class="t-size1 font-bold text-[#ff3868]">Kelola</a>
                    @endif
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($lowStockItems as $item)
                        <div class="flex items-center gap-3 rounded-xl bg-[#fff8f4] p-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#fff0df] text-[#f28c28]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="m3 7 9-4 9 4-9 4-9-4Z" />
                                    <path d="m3 7 9 4 9-4v10l-9 4-9-4V7Z" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate t-size1 font-bold">{{ $item->name }}</p>
                                <p class="text-[10px] text-text-muted">Minimum {{ number_format($item->minimum_quantity, 0, ',', '.') }}</p>
                            </div>
                            <span
                                class="rounded-full bg-white px-2.5 py-1 t-size1 font-extrabold text-[#ef6b39]">{{ number_format($item->quantity, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="rounded-xl bg-[#ecfaf1] px-4 py-10 text-center t-size2 font-semibold text-emerald-700">Semua stok dalam kondisi aman.</div>
                    @endforelse
                </div>
            </article>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const dates = @json($revenueTrend->pluck('date'));
                const revenue = @json($revenueTrend->pluck('revenue'));
                const orders = @json($ordersTrend->pluck('orders'));
                const charts = [];
                const axis = {
                    axisLine: {
                        lineStyle: {
                            color: '#f0e2e5'
                        }
                    },
                    axisTick: {
                        show: false
                    },
                    axisLabel: {
                        color: '#8a767b',
                        fontSize: 10
                    }
                };

                const revenueElement = document.getElementById('revenue-chart');
                if (revenueElement) {
                    const chart = echarts.init(revenueElement);
                    chart.setOption({
                        grid: {
                            left: 58,
                            right: 16,
                            top: 20,
                            bottom: 30
                        },
                        tooltip: {
                            trigger: 'axis',
                            formatter: p => `${p[0].name}<br><b>Rp ${Number(p[0].value).toLocaleString('id-ID')}</b>`
                        },
                        xAxis: Object.assign({}, axis, {
                            type: 'category',
                            boundaryGap: false,
                            data: dates
                        }),
                        yAxis: Object.assign({}, axis, {
                            type: 'value',
                            splitLine: {
                                lineStyle: {
                                    color: '#f7ecee',
                                    type: 'dashed'
                                }
                            },
                            axisLabel: {
                                color: '#8a767b',
                                fontSize: 10,
                                formatter: v => `${v / 1000}k`
                            }
                        }),
                        series: [{
                            type: 'line',
                            data: revenue,
                            smooth: true,
                            symbolSize: 8,
                            lineStyle: {
                                width: 3,
                                color: '#ff3868'
                            },
                            itemStyle: {
                                color: '#ff3868',
                                borderColor: '#fff',
                                borderWidth: 2
                            },
                            areaStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: 'rgba(255,56,104,.22)'
                                }, {
                                    offset: 1,
                                    color: 'rgba(255,56,104,.01)'
                                }])
                            }
                        }]
                    });
                    charts.push(chart);
                }

                const ordersElement = document.getElementById('orders-chart');
                if (ordersElement) {
                    const chart = echarts.init(ordersElement);
                    chart.setOption({
                        grid: {
                            left: 38,
                            right: 16,
                            top: 16,
                            bottom: 28
                        },
                        tooltip: {
                            trigger: 'axis'
                        },
                        xAxis: Object.assign({}, axis, {
                            type: 'category',
                            data: dates
                        }),
                        yAxis: Object.assign({}, axis, {
                            type: 'value',
                            minInterval: 1,
                            splitLine: {
                                lineStyle: {
                                    color: '#f7ecee',
                                    type: 'dashed'
                                }
                            }
                        }),
                        series: [{
                            type: 'bar',
                            data: orders,
                            barMaxWidth: 28,
                            itemStyle: {
                                color: '#ff718f',
                                borderRadius: [8, 8, 0, 0]
                            }
                        }]
                    });
                    charts.push(chart);
                }

                window.addEventListener('resize', () => charts.forEach(chart => chart.resize()));
            });
        </script>
    @endpush
</x-app-layout>
