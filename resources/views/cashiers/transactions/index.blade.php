<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold t-size8 font-heading text-text flex items-center gap-2">
                    <span class="w-10 h-10 rounded-xl bg-primary-soft/40 flex items-center justify-center text-accent t-size5">💵</span>
                    {{ __('Kelola Transaksi') }}
                </h2>
                <p class="text-text-muted t-size3 mt-1">Konfirmasi pembayaran dan kelola riwayat transaksi masuk.</p>
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
            // Auto refresh list every 15 seconds to fetch new pending payments
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
            <form method="GET" action="{{ route('cashier.transactions') }}" class="flex flex-wrap items-center gap-3">
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
                        <a href="{{ route('cashier.transactions') }}"
                            class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-5 py-2.5 rounded-xl transition cursor-pointer text-center flex items-center justify-center font-bold">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Status Filter Bar -->
        <div class="flex items-center justify-between bg-card border border-border rounded-3xl p-6 shadow-xs">
            <div class="flex items-center gap-2">
                <span class="bg-warning/15 text-warning border border-warning/30 font-bold px-4 py-2 rounded-xl t-size3">
                    ⏳ {{ __('Menunggu Pembayaran (Pending)') }}
                </span>
            </div>

            <div class="text-text-muted t-size2 font-semibold bg-surface border border-border px-3.5 py-1.5 rounded-xl">
                🔄 {{ __('Auto-refresh aktif (15s)') }}
            </div>
        </div>

        <!-- Transactions Table / List -->
        <div class="bg-card border border-border rounded-3xl overflow-hidden shadow-xs">
            @if ($orders->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 bg-surface-alt text-text-muted/60 rounded-full flex items-center justify-center mx-auto text-2xl">
                        💵
                    </div>
                    <h3 class="font-bold t-size4 text-text">{{ __('Tidak Ada Transaksi Pending') }}</h3>
                    <p class="text-text-muted t-size2 max-w-sm mx-auto">
                        Belum ada pesanan masuk yang menunggu pembayaran saat ini.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                <th class="py-4 px-6">{{ __('No. Transaksi') }}</th>
                                <th class="py-4 px-6">{{ __('Waktu') }}</th>
                                <th class="py-4 px-6">{{ __('Pelanggan') }}</th>
                                <th class="py-4 px-6">{{ __('Metode Pembayaran') }}</th>
                                <th class="py-4 px-6">{{ __('Total') }}</th>
                                <th class="py-4 px-6">{{ __('Status') }}</th>
                                <th class="py-4 px-6 text-right w-36">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($orders as $order)
                                <tr class="hover:bg-surface/30 transition">
                                    <!-- No. Transaksi -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2 font-bold text-accent t-size3">
                                            <span class="text-accent/60">📄</span>
                                            TRX-{{ $order->created_at->format('dmy') }}-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </td>

                                    <!-- Waktu -->
                                    <td class="py-4 px-6 text-text font-semibold t-size3">
                                        {{ $order->created_at->format('H:i') }} WIB
                                    </td>

                                    <!-- Pelanggan -->
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-text t-size3.5">
                                            @if ($order->table_number)
                                                Dine In - Meja {{ $order->table_number }}
                                            @else
                                                Take Away
                                            @endif
                                        </div>
                                        <div class="text-text-muted t-size1 mt-0.5">
                                            Pelanggan: {{ $order->customer_name ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Metode Pembayaran -->
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2 font-semibold text-text-muted t-size3">
                                            <span>💳</span>
                                            <div>
                                                <div>Belum Ditentukan</div>
                                                <div class="text-[10px] text-text-muted/60 mt-0.5">QRIS / Tunai</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Total -->
                                    <td class="py-4 px-6 font-extrabold text-accent t-size3.5">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4 px-6">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full t-size2 font-bold bg-warning/15 text-warning border border-warning/35">
                                            Pending
                                        </span>
                                    </td>

                                    <!-- Action -->
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('cashier.transactions.show', $order->id) }}"
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-card border border-primary hover:bg-primary-soft/10 text-accent transition shadow-2xs"
                                            title="Proses Pembayaran">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
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
