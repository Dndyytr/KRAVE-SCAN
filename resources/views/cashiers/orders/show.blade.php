<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('cashier.orders') }}" class="text-text-muted hover:text-text transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-bold t-size7 font-heading text-text">
                {{ __('Detail Pesanan') }} #{{ $order->id }}
            </h2>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1 & 2: Order Info & Items -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Customer Info Card -->
            <div class="bg-card border border-border rounded-2xl p-6 space-y-4 shadow-xs">
                <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-3">
                    {{ __('Informasi Pelanggan') }}
                </h3>
                <div class="grid grid-cols-2 gap-4 t-size3">
                    <div>
                        <span class="text-text-muted block">{{ __('Nama Pelanggan') }}</span>
                        <span class="font-bold text-text">{{ $order->customer_name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-text-muted block">{{ __('No. WA / Email') }}</span>
                        <span class="font-bold text-text">{{ $order->customer_contact ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Order General Card -->
            <div class="bg-card border border-border rounded-2xl p-6 space-y-4 shadow-xs">
                <div class="flex items-center justify-between border-b border-border pb-4">
                    <div>
                        <span class="text-text-muted t-size2 font-semibold uppercase tracking-wider block">{{ __('Nomor Meja') }}</span>
                        <span class="text-accent font-extrabold t-size8 font-heading">
                            {{ __('Meja') }} {{ $order->table_number }}
                        </span>
                    </div>
                    <div>
                        <div class="mt-1">
                            <x-status-badge :status="$order->status" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 t-size3">
                    <div>
                        <span class="text-text-muted block">{{ __('Waktu Dibuat') }}</span>
                        <span class="font-semibold text-text">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-text-muted block text-right">{{ __('Total Pesanan') }}</span>
                        <span class="font-extrabold text-accent block text-right t-size5">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Items Card -->
            <div class="bg-card border border-border rounded-2xl p-6 space-y-4 shadow-xs">
                <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-3">
                    {{ __('Daftar Hidangan') }}
                </h3>

                <div class="divide-y divide-border">
                    @foreach ($order->orderItems as $item)
                        <div class="py-4 flex justify-between items-center first:pt-0 last:pb-0">
                            <div class="flex items-center gap-4">
                                @if ($item->menu->image_path)
                                    <img src="{{ Str::startsWith($item->menu->image_path, ['http://', 'https://']) ? $item->menu->image_path : (Str::startsWith($item->menu->image_path, 'storage/') ? asset($item->menu->image_path) : asset('storage/' . $item->menu->image_path)) }}"
                                        alt="{{ $item->menu->name }}" class="w-12 h-12 object-cover rounded-xl border border-border">
                                @else
                                    <div class="w-12 h-12 bg-surface border border-border rounded-xl flex items-center justify-center font-bold text-accent">
                                        {{ substr($item->menu->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-bold t-size3 text-text">{{ $item->menu->name }}</h4>
                                    <span class="text-text-muted t-size2">
                                        {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </span>
                                    @if ($item->note)
                                        <span
                                            class="block text-accent t-size1 font-semibold mt-0.5 bg-primary-soft/30 px-2 py-0.5 rounded-md inline-block border border-primary-soft/50">
                                            Catatan: <span class="text-text font-bold">"{{ $item->note }}"</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <span class="font-bold text-text t-size3">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-border pt-4 flex justify-between items-center">
                    <span class="font-bold text-text t-size4">{{ __('Subtotal') }}</span>
                    <span class="font-extrabold text-accent t-size5">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Order Timeline Card -->
            <div class="bg-card border border-border rounded-2xl p-6 space-y-4 shadow-xs">
                <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-3">
                    {{ __('Riwayat Aktivitas Pesanan') }}
                </h3>

                <div class="relative pl-6 border-l-2 border-primary-soft/50 space-y-6">
                    @forelse($order->histories as $history)
                        <div class="relative">
                            <span
                                class="absolute -left-[31px] top-1.5 w-4 h-4 rounded-full border-2 border-white 
                                @if ($history->status === 'pending') bg-warning
                                @elseif($history->status === 'confirmed') bg-info
                                @elseif($history->status === 'in_process') bg-accent
                                @elseif($history->status === 'completed') bg-success
                                @else bg-danger @endif"></span>

                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-text t-size3">
                                        {{ ucfirst(str_replace('_', ' ', $history->status)) }}
                                    </span>
                                    <span class="text-text-muted text-[11px]">
                                        {{ $history->created_at->format('H:i') }}
                                        ({{ $history->created_at->diffForHumans() }})
                                    </span>
                                </div>
                                <p class="text-text-muted t-size2 mt-0.5">{{ $history->notes }}</p>
                                @if ($history->user)
                                    <span class="text-[10px] text-text-muted/60 mt-1 block">
                                        👤 {{ __('Diperbarui oleh') }}: {{ $history->user->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-text-muted t-size2 py-2">{{ __('Belum ada riwayat aktivitas tercatat.') }}</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Column 3: Status & Payment Display -->
        <div class="space-y-6">

            <!-- Payment Completed Display -->
            <div class="bg-card border border-border rounded-2xl p-6 space-y-6 shadow-xs">
                <div class="border-b border-border pb-3">
                    <h3 class="font-bold t-size4 font-heading text-text">{{ __('Informasi Pembayaran') }}</h3>
                    <p class="text-text-muted t-size2 mt-0.5">{{ __('Pesanan telah terbayar lunas.') }}</p>
                </div>

                @forelse ($order->payments as $payment)
                    <div class="bg-surface border border-border rounded-xl p-4 space-y-3 t-size3">
                        <div class="flex justify-between items-center">
                            <span class="text-text-muted">{{ __('Metode') }}</span>
                            <span class="font-bold text-text uppercase">
                                {{ $payment->method === 'cash' ? __('💵 Tunai') : __('📱 QRIS') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-text-muted">{{ __('Status') }}</span>
                            <x-status-badge :status="$payment->status" />
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-text-muted">{{ __('Uang Bayar') }}</span>
                            <span class="font-bold text-text">
                                Rp {{ number_format($payment->cash_received ?? $payment->amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center border-t border-border pt-2">
                            <span class="text-text-muted">{{ __('Total Harga') }}</span>
                            <span class="font-bold text-text">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-text-muted">{{ __('Kembalian') }}</span>
                            <span class="font-extrabold text-success">
                                Rp {{ number_format($payment->change ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Receipts link -->
                    @foreach ($payment->receipts as $receipt)
                        <div class="space-y-3">
                            <div class="bg-card border border-border rounded-xl p-4 text-center space-y-2">
                                <span class="text-text-muted t-size2 block">{{ __('Nomor Struk') }}</span>
                                <code class="font-mono font-bold text-text block t-size3 bg-surface border border-border py-1 px-3 rounded-lg select-all">
                                    {{ $receipt->receipt_number }}
                                </code>
                            </div>

                            <a href="{{ route('cashier.receipts.show', $receipt->id) }}" target="_blank"
                                class="w-full bg-surface border border-border hover:bg-surface-alt text-text font-bold py-3 rounded-xl t-size3 transition flex items-center justify-center gap-2 cursor-pointer shadow-2xs">
                                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                {{ __('Cetak Struk Digital') }}
                            </a>
                        </div>
                    @endforeach
                @empty
                    <div class="text-center py-2 text-text-muted t-size2">
                        {{ __('Tidak ada data pembayaran.') }}
                    </div>
                @endforelse
            </div>

            <!-- Status Monitoring Card -->
            <div class="bg-card border border-border rounded-2xl p-6 space-y-4 shadow-xs">
                <div class="border-b border-border pb-3">
                    <h3 class="font-bold t-size4 font-heading text-text">{{ __('Status Pengerjaan Dapur') }}</h3>
                    <p class="text-text-muted t-size2 mt-0.5">{{ __('Status progres pengerjaan di dapur saat ini.') }}</p>
                </div>

                <div class="text-center py-4 text-text-muted t-size3 font-semibold">
                    @if ($order->status === 'confirmed')
                        👨‍🍳 {{ __('Pesanan dalam antrean pengerjaan dapur.') }}
                    @elseif ($order->status === 'in_process')
                        🍳 {{ __('Pesanan sedang disiapkan di dapur.') }}
                    @elseif ($order->status === 'completed')
                        🎉 {{ __('Pesanan selesai sepenuhnya.') }}
                    @elseif ($order->status === 'cancelled')
                        🚫 {{ __('Pesanan ini dibatalkan.') }}
                    @else
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    @endif
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
