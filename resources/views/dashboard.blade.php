<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Ringkasan Dasbor') }}
        </h2>
    </x-slot>

    <div class="space-y-8 anim-fade">
        <!-- Welcome Section -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
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
            <div class="bg-surface-alt border border-border px-4 py-2 rounded-xl text-text-muted t-size2 font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        <!-- Metric Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Pendapatan Hari Ini -->
            <div class="bg-primary-soft/20 border border-primary-soft/40 rounded-2xl p-6 shadow-xs flex items-center justify-between transition hover:-translate-y-0.5 duration-200">
                <div class="space-y-2">
                    <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">
                        {{ __('Pendapatan Hari Ini') }}
                    </span>
                    <span class="text-accent font-extrabold t-size6 block">
                        Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                    </span>
                </div>
                <div class="w-12 h-12 bg-primary-soft/45 text-accent rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Card 2: Total Pesanan Hari Ini -->
            <div class="bg-info/10 border border-info/30 rounded-2xl p-6 shadow-xs flex items-center justify-between transition hover:-translate-y-0.5 duration-200">
                <div class="space-y-2">
                    <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">
                        {{ __('Pesanan Hari Ini') }}
                    </span>
                    <span class="text-info font-extrabold t-size6 block">
                        {{ $todayOrdersCount }} {{ __('Pesanan') }}
                    </span>
                </div>
                <div class="w-12 h-12 bg-info/20 text-info rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>

            <!-- Card 3: Pesanan Pending -->
            <div class="bg-warning/10 border border-warning/30 rounded-2xl p-6 shadow-xs flex items-center justify-between transition hover:-translate-y-0.5 duration-200">
                <div class="space-y-2">
                    <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">
                        {{ __('Pesanan Pending') }}
                    </span>
                    <span class="text-warning font-extrabold t-size6 block">
                        {{ $pendingOrdersCount }} {{ __('Antrean') }}
                    </span>
                </div>
                <div class="w-12 h-12 bg-warning/20 text-warning rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Card 4: Peringatan Stok -->
            <div class="bg-danger/10 border border-danger/30 rounded-2xl p-6 shadow-xs flex items-center justify-between transition hover:-translate-y-0.5 duration-200">
                <div class="space-y-2">
                    <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">
                        {{ __('Stok Menipis') }}
                    </span>
                    <span class="text-danger font-extrabold t-size6 block">
                        {{ $lowStockCount }} {{ __('Item') }}
                    </span>
                </div>
                <div class="w-12 h-12 bg-danger/20 text-danger rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
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
                    <div class="py-8 text-center text-text-muted t-size3">
                        {{ __('Belum ada pesanan masuk hari ini.') }}
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                    <th class="py-3 px-4">{{ __('ID') }}</th>
                                    @if(!Auth::user()->branch)
                                        <th class="py-3 px-4">{{ __('Cabang') }}</th>
                                    @endif
                                    <th class="py-3 px-4">{{ __('Meja') }}</th>
                                    <th class="py-3 px-4">{{ __('Total') }}</th>
                                    <th class="py-3 px-4">{{ __('Status') }}</th>
                                    <th class="py-3 px-4 text-right">{{ __('Aksi') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($recentOrders as $order)
                                    <tr class="hover:bg-surface/30 transition">
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
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border
                                                @if($order->status === 'pending') bg-warning/10 text-warning border-warning/20
                                                @elseif($order->status === 'confirmed') bg-info/10 text-info border-info/20
                                                @elseif($order->status === 'in_process') bg-primary-soft/30 text-accent border-primary-soft/50
                                                @elseif($order->status === 'completed') bg-success/10 text-success border-success/20
                                                @else bg-danger/10 text-danger border-danger/20
                                                @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            @if(Auth::user()->role?->name === 'cashier')
                                                <a href="{{ route('cashier.orders.show', $order->id) }}" class="text-accent hover:text-primary font-bold t-size2 transition">
                                                    {{ __('Detail') }}
                                                </a>
                                            @else
                                                <span class="text-text-muted/50 t-size2">{{ __('Staff Only') }}</span>
                                            @endif
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
                <h3 class="font-bold t-size5 text-text font-heading">
                    {{ __('Peringatan Stok Operasional') }}
                </h3>

                @if($lowStockItems->isEmpty())
                    <div class="py-8 text-center space-y-3">
                        <div class="w-12 h-12 bg-success/10 text-success rounded-full flex items-center justify-center mx-auto">
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
                            <div class="flex items-center justify-between p-4 bg-danger/5 border border-danger/20 rounded-xl">
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
                                    <span class="bg-danger/10 text-danger font-extrabold px-3 py-1 rounded-full t-size2 border border-danger/20">
                                        {{ $item->quantity }} / {{ $item->minimum_quantity }} {{ $item->unit }}
                                    </span>
                                    <span class="block text-[10px] text-text-muted/70">
                                        {{ __('Batas Minimum') }}: {{ $item->minimum_quantity }} {{ $item->unit }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
