<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold t-size8 font-heading text-text flex items-center gap-2">
                    <span class="w-10 h-10 rounded-xl bg-primary-soft/40 flex items-center justify-center text-accent t-size5">📋</span>
                    {{ __('Kelola Pesanan') }}
                </h2>
                <p class="text-text-muted t-size3 mt-1">Pantau, proses, dan konfirmasi pesanan pelanggan secara real-time.</p>
            </div>
            <div class="hidden sm:block">
                <div class="bg-card border border-border px-4 py-2 rounded-2xl flex items-center gap-2 t-size2 font-semibold text-text">
                    <span class="text-primary">📅</span>
                    {{ now()->translatedFormat('l, d M Y') }}
                </div>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        init() {
            // Auto refresh order list every 15 seconds to fetch new orders
            setInterval(() => {
                window.location.reload();
            }, 15000);
        }
    }" class="space-y-6 anim-fade">

        <!-- Date Filter Form -->
        <div class="bg-card border border-border rounded-3xl p-6 shadow-xs space-y-4">
            <h3 class="font-bold t-size5 text-text font-heading">
                Filter Rentang Tanggal
            </h3>
            <form method="GET" action="{{ route('cashier.orders') }}" class="flex flex-wrap items-center gap-3">
                @if ($currentStatus)
                    <input type="hidden" name="status" value="{{ $currentStatus }}">
                @endif

                <div class="flex items-center bg-card border border-border rounded-xl px-3.5 py-1.5 gap-2 shrink-0">
                    <span class="text-text-muted">📅</span>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                        class="bg-transparent border-0 p-0 text-text font-semibold t-size3 focus:ring-0 focus:outline-none">
                    <span class="text-text-muted font-bold t-size2">-</span>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                        class="bg-transparent border-0 p-0 text-text font-semibold t-size3 focus:ring-0 focus:outline-none">
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-primary hover:bg-primary-strong text-white font-extrabold px-6 py-2.5 rounded-xl t-size3 transition cursor-pointer shadow-xs">
                        Filter
                    </button>
                    @if ($startDate || $endDate)
                        <a href="{{ route('cashier.orders', array_filter(['status' => $currentStatus])) }}"
                            class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-5 py-2.5 rounded-xl transition cursor-pointer text-center flex items-center justify-center font-bold">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Status Filter Bar (Excludes Pending) -->
        <div class="flex flex-wrap gap-3 items-center justify-between bg-card border border-border rounded-3xl p-6 shadow-xs">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('cashier.orders', array_filter(['start_date' => $startDate, 'end_date' => $endDate])) }}"
                    class="px-4 py-2 rounded-xl t-size3 font-semibold transition border {{ is_null($currentStatus) ? 'bg-primary text-white border-primary' : 'bg-surface border-border text-text-muted hover:bg-surface-alt hover:text-text' }}">
                    {{ __('Semua Aktif') }}
                </a>
                <a href="{{ route('cashier.orders', array_filter(['status' => 'confirmed', 'start_date' => $startDate, 'end_date' => $endDate])) }}"
                    class="px-4 py-2 rounded-xl t-size3 font-semibold transition border {{ $currentStatus === 'confirmed' ? 'bg-info text-white border-info' : 'bg-surface border-border text-text-muted hover:bg-surface-alt hover:text-text' }}">
                    {{ __('Confirmed') }}
                </a>
                <a href="{{ route('cashier.orders', array_filter(['status' => 'in_process', 'start_date' => $startDate, 'end_date' => $endDate])) }}"
                    class="px-4 py-2 rounded-xl t-size3 font-semibold transition border {{ $currentStatus === 'in_process' ? 'bg-accent text-white border-accent' : 'bg-surface border-border text-text-muted hover:bg-surface-alt hover:text-text' }}">
                    {{ __('In Process') }}
                </a>
                <a href="{{ route('cashier.orders', array_filter(['status' => 'completed', 'start_date' => $startDate, 'end_date' => $endDate])) }}"
                    class="px-4 py-2 rounded-xl t-size3 font-semibold transition border {{ $currentStatus === 'completed' ? 'bg-success text-white border-success' : 'bg-surface border-border text-text-muted hover:bg-surface-alt hover:text-text' }}">
                    {{ __('Completed') }}
                </a>
                <a href="{{ route('cashier.orders', array_filter(['status' => 'cancelled', 'start_date' => $startDate, 'end_date' => $endDate])) }}"
                    class="px-4 py-2 rounded-xl t-size3 font-semibold transition border {{ $currentStatus === 'cancelled' ? 'bg-danger text-white border-danger' : 'bg-surface border-border text-text-muted hover:bg-surface-alt hover:text-text' }}">
                    {{ __('Cancelled') }}
                </a>
            </div>

            <div class="text-text-muted t-size2 font-semibold bg-surface border border-border px-3.5 py-1.5 rounded-xl">
                🔄 {{ __('Auto-refresh aktif (15s)') }}
            </div>
        </div>

        <!-- Orders Table / List -->
        <div class="bg-card border border-border rounded-3xl overflow-hidden shadow-xs">
            @if ($orders->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 bg-surface-alt text-text-muted/60 rounded-full flex items-center justify-center mx-auto text-2xl">
                        📋
                    </div>
                    <h3 class="font-bold t-size4 text-text">{{ __('Tidak Ada Pesanan') }}</h3>
                    <p class="text-text-muted t-size2 max-w-sm mx-auto">
                        Belum ada pesanan dengan status terpilih untuk saat ini di cabang Anda.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                <th class="py-4 px-6">{{ __('Pesanan') }}</th>
                                <th class="py-4 px-6">{{ __('Pelanggan') }}</th>
                                <th class="py-4 px-6">{{ __('Meja') }}</th>
                                <th class="py-4 px-6">{{ __('Item Pesanan') }}</th>
                                <th class="py-4 px-6">{{ __('Total') }}</th>
                                <th class="py-4 px-6">{{ __('Status') }}</th>
                                <th class="py-4 px-6">{{ __('Waktu') }}</th>
                                <th class="py-4 px-6 text-right">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($orders as $order)
                                <tr class="hover:bg-surface/30 transition">
                                    <!-- Order ID -->
                                    <td class="py-4 px-6 font-bold text-text t-size3">
                                        #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                    </td>

                                    <!-- Customer details -->
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-text t-size3.5">{{ $order->customer_name ?? '-' }}</div>
                                        <div class="text-text-muted t-size1 mt-0.5">{{ $order->customer_contact ?? '-' }}</div>
                                    </td>

                                    <!-- Table Number -->
                                    <td class="py-4 px-6">
                                        <span class="bg-primary-soft/50 text-accent font-extrabold px-3 py-1 rounded-full t-size2 border border-primary-soft">
                                            {{ __('Meja') }} {{ $order->table_number }}
                                        </span>
                                    </td>

                                    <!-- Order Items summary -->
                                    <td class="py-4 px-6 max-w-xs truncate t-size3 text-text-muted"
                                        title="@foreach ($order->orderItems as $item){{ $item->menu->name }} ({{ $item->quantity }}){{ !$loop->last ? ', ' : '' }} @endforeach">
                                        @foreach ($order->orderItems as $item)
                                            {{ $item->menu->name }}
                                            <span class="font-bold text-accent">({{ $item->quantity }})</span>{{ !$loop->last ? ',' : '' }}
                                        @endforeach
                                    </td>

                                    <!-- Total Amount -->
                                    <td class="py-4 px-6 font-extrabold text-accent t-size3.5">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4 px-6">
                                        <x-status-badge :status="$order->status" />
                                    </td>

                                    <!-- Order Time -->
                                    <td class="py-4 px-6 t-size2 text-text-muted">
                                        <div class="font-bold text-text">{{ $order->created_at->format('H:i') }} WIB</div>
                                        <div class="text-[10px] text-text-muted/60 mt-0.5">{{ $order->created_at->format('d M Y') }}</div>
                                    </td>

                                    <!-- Action -->
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('cashier.orders.show', $order->id) }}"
                                            class="bg-surface border border-border text-text hover:bg-surface-alt font-extrabold px-4 py-2 rounded-xl t-size2 transition">
                                            {{ __('Detail') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($orders->hasPages())
                    <div class="bg-surface-alt border-t border-border px-6 py-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</x-app-layout>
