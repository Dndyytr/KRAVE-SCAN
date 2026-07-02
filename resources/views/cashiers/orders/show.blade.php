<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 t-size3 text-text-muted">
            <a href="{{ route('cashier.orders') }}" class="hover:text-text transition">Pesanan</a>
            <span>›</span>
            <span class="text-text font-bold">#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
        </div>
    </x-slot>

    <div class="space-y-6 anim-fade">

        {{-- Order Progress Timeline --}}
        @php
            $statusSteps = [
                'pending' => 'Diterima',
                'confirmed' => 'Diproses',
                'in_process' => 'Siap',
                'completed' => 'Selesai',
            ];
            $statusOrder = array_keys($statusSteps);
            $currentIndex = array_search($order->status, $statusOrder);
            if ($currentIndex === false) {
                $currentIndex = -1;
            }
            $isCancelled = $order->status === 'cancelled';
        @endphp

        @if (!$isCancelled)
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                <div class="flex items-center justify-between relative">
                    {{-- Progress Line --}}
                    <div class="absolute top-5 left-[10%] right-[10%] h-0.5 bg-border z-0"></div>
                    <div class="absolute top-5 left-[10%] h-0.5 bg-primary z-0 transition-all duration-500"
                        style="width: {{ $currentIndex >= 0 ? min(($currentIndex / (count($statusSteps) - 1)) * 80, 80) : 0 }}%">
                    </div>

                    @foreach ($statusSteps as $key => $label)
                        @php
                            $stepIndex = array_search($key, $statusOrder);
                            $isActive = $stepIndex <= $currentIndex;
                            $isCurrent = $stepIndex === $currentIndex;
                        @endphp
                        <div class="flex flex-col items-center z-10 flex-1">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all
                            {{ $isCurrent ? 'bg-primary border-primary text-white shadow-md scale-110' : ($isActive ? 'bg-primary border-primary text-white' : 'bg-card border-border text-text-muted') }}">
                                @if ($isActive && !$isCurrent)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif ($isCurrent)
                                    <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                                @else
                                    <span class="t-size2 font-bold">{{ $stepIndex + 1 }}</span>
                                @endif
                            </div>
                            <span class="mt-2 t-size2 font-bold {{ $isActive ? 'text-text' : 'text-text-muted' }}">{{ $label }}</span>
                            @if ($isActive && $order->histories->where('status', $key)->first())
                                <span class="t-size1 text-text-muted">{{ $order->histories->where('status', $key)->first()->created_at->format('H:i') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-danger/10 border border-danger/30 rounded-2xl p-6 text-center">
                <span class="text-2xl">🚫</span>
                <h3 class="font-bold t-size5 text-danger mt-2">Pesanan Dibatalkan</h3>
                <p class="text-text-muted t-size3 mt-1">Pesanan ini telah dibatalkan.</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Column 1 & 2: Order Info & Items --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Informasi Pesanan --}}
                <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                    <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-3 mb-4 flex items-center gap-2">
                        <span class="t-size3">📦</span> {{ __('Informasi Pesanan') }}
                    </h3>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center bg-surface border border-border rounded-xl p-4">
                            <span class="text-text-muted t-size2 font-semibold block">Meja</span>
                            <span class="text-accent font-extrabold t-size8 font-heading block mt-1">
                                {{ $order->table_number }}
                            </span>
                        </div>
                        <div class="text-center bg-surface border border-border rounded-xl p-4">
                            <span class="text-text-muted t-size2 font-semibold block">Pelanggan</span>
                            <span class="font-bold text-text t-size4 block mt-2">{{ $order->customer_name ?? '-' }}</span>
                        </div>
                        <div class="text-center bg-surface border border-border rounded-xl p-4">
                            <span class="text-text-muted t-size2 font-semibold block">Tipe Pesanan</span>
                            <span class="font-bold text-text t-size4 block mt-2">Dine In</span>
                        </div>
                    </div>
                </div>

                {{-- Daftar Pesanan --}}
                <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                    <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-3 mb-4 flex items-center gap-2">
                        <span class="t-size3">🍽️</span> {{ __('Daftar Pesanan') }}
                    </h3>

                    {{-- Table Header --}}
                    <div class="hidden sm:grid grid-cols-12 gap-2 text-text-muted t-size2 font-bold uppercase tracking-wider pb-2 border-b border-border">
                        <div class="col-span-5">Menu</div>
                        <div class="col-span-2 text-center">Qty</div>
                        <div class="col-span-2 text-right">Harga</div>
                        <div class="col-span-3 text-right">Total</div>
                    </div>

                    <div class="divide-y divide-border">
                        @foreach ($order->orderItems as $item)
                            <div class="py-3 sm:grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-5 flex items-center gap-3">
                                    @if ($item->menu->image_path)
                                        <img src="{{ Str::startsWith($item->menu->image_path, ['http://', 'https://']) ? $item->menu->image_path : (Str::startsWith($item->menu->image_path, 'storage/') ? asset($item->menu->image_path) : asset('storage/' . $item->menu->image_path)) }}"
                                            alt="{{ $item->menu->name }}" class="w-10 h-10 object-cover rounded-lg border border-border shrink-0">
                                    @else
                                        <div
                                            class="w-10 h-10 bg-surface border border-border rounded-lg flex items-center justify-center font-bold text-accent t-size2 shrink-0">
                                            {{ substr($item->menu->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-bold t-size3 text-text block">{{ $item->menu->name }}</span>
                                        @if ($item->note)
                                            <span
                                                class="t-size1 text-accent bg-primary-soft/30 px-1.5 py-0.5 rounded border border-primary-soft/50 inline-block mt-0.5">{{ $item->note }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-span-2 text-center font-semibold t-size3 text-text">
                                    {{ $item->quantity }}</div>
                                <div class="col-span-2 text-right text-text-muted t-size3">Rp
                                    {{ number_format($item->price, 0, ',', '.') }}</div>
                                <div class="col-span-3 text-right font-bold t-size3 text-text">Rp
                                    {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Ringkasan Pembayaran --}}
                <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                    <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-3 mb-4 flex items-center gap-2">
                        <span class="t-size3">💳</span> {{ __('Ringkasan Pembayaran') }}
                    </h3>

                    <div class="space-y-3 t-size3">
                        <div class="flex justify-between items-center">
                            <span class="text-text-muted">Subtotal</span>
                            <span class="font-semibold text-text">Rp
                                {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-text-muted">Pajak (10%)</span>
                            <span class="font-semibold text-text">Rp 0</span>
                        </div>
                        <div class="border-t border-border pt-3 flex justify-between items-center">
                            <span class="font-bold text-text t-size4">Total</span>
                            <span class="font-extrabold text-accent t-size6">Rp
                                {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Catatan Dapur --}}
                @if ($order->orderItems->whereNotNull('note')->count() > 0)
                    <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                        <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-3 mb-4 flex items-center gap-2">
                            <span class="t-size3">📝</span> {{ __('Catatan Dapur') }}
                        </h3>
                        <div class="space-y-2">
                            @foreach ($order->orderItems->whereNotNull('note') as $item)
                                <div class="bg-surface border border-border rounded-xl p-3 flex items-start gap-3">
                                    <span class="text-warning t-size4">⚠️</span>
                                    <div>
                                        <span class="font-bold t-size3 text-text block">{{ $item->menu->name }}</span>
                                        <span class="t-size2 text-text-muted">{{ $item->note }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- Column 3: Sidebar --}}
            <div class="space-y-6">

                {{-- Status Pembayaran --}}
                <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                    <h3 class="font-bold t-size4 font-heading text-text border-b border-border pb-3 mb-4">
                        {{ __('Status Pembayaran') }}
                    </h3>
                    @if ($order->payments->count() > 0)
                        <div class="text-center py-2">
                            <span
                                class="inline-flex items-center gap-2 bg-success/15 text-success border border-success/30 font-bold px-4 py-2 rounded-full t-size3">
                                ✅ Lunas
                            </span>
                            <p class="text-text-muted t-size2 mt-2">Dibayar penuh</p>
                        </div>
                    @else
                        <div class="text-center py-2">
                            <span
                                class="inline-flex items-center gap-2 bg-warning/15 text-warning border border-warning/30 font-bold px-4 py-2 rounded-full t-size3">
                                ⏳ Belum Bayar
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Informasi Pembayaran --}}
                @if ($order->payments->count() > 0)
                    <div class="bg-card border border-border rounded-2xl p-6 space-y-4 shadow-xs">
                        <h3 class="font-bold t-size4 font-heading text-text border-b border-border pb-3">
                            {{ __('Informasi Pembayaran') }}
                        </h3>

                        @foreach ($order->payments as $payment)
                            <div class="space-y-3 t-size3">
                                <div class="flex justify-between items-center">
                                    <span class="text-text-muted">{{ __('Metode') }}</span>
                                    <span class="font-bold text-text">
                                        {{ $payment->method === 'cash' ? '💵 Tunai' : '📱 QRIS' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-text-muted">{{ __('Status') }}</span>
                                    <x-status-badge :status="$payment->status" />
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-text-muted">{{ __('Uang Bayar') }}</span>
                                    <span class="font-bold text-text">
                                        Rp
                                        {{ number_format($payment->cash_received ?? $payment->amount, 0, ',', '.') }}
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

                            {{-- Receipt links --}}
                            @foreach ($payment->receipts as $receipt)
                                <div class="space-y-3 pt-2 border-t border-border">
                                    <div class="bg-surface border border-border rounded-xl p-3 text-center">
                                        <span class="text-text-muted t-size2 block">{{ __('Nomor Struk') }}</span>
                                        <code
                                            class="font-mono font-bold text-text block t-size3 bg-card border border-border py-1 px-3 rounded-lg mt-1 select-all">
                                            {{ $receipt->receipt_number }}
                                        </code>
                                    </div>

                                    <a href="{{ route('cashier.receipts.show', $receipt->id) }}" target="_blank"
                                        class="w-full bg-primary/10 hover:bg-primary/20 text-primary border border-primary/30 font-bold py-2.5 rounded-xl t-size3 transition flex items-center justify-center gap-2 cursor-pointer">
                                        🖨️ {{ __('Cetak Struk') }}
                                    </a>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                @endif

                {{-- Batalkan Pesanan --}}
                @if (!in_array($order->status, ['completed', 'cancelled']))
                    <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                        <form action="{{ route('cashier.orders.update-status', $order->id) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit"
                                class="w-full bg-danger/10 hover:bg-danger/20 text-danger border border-danger/30 font-bold py-3 rounded-xl t-size3 transition flex items-center justify-center gap-2 cursor-pointer">
                                ❌ {{ __('Batalkan Pesanan') }}
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Riwayat Aktivitas --}}
                <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                    <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-3 mb-4 flex items-center gap-2">
                        <span class="t-size3">📜</span> {{ __('Riwayat Aktivitas') }}
                    </h3>

                    <div class="relative pl-6 border-l-2 border-primary-soft/50 space-y-5">
                        @forelse($order->histories as $history)
                            <div class="relative">
                                <span
                                    class="absolute -left-[31px] top-1 w-4 h-4 rounded-full border-2 border-white 
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
                                        <span class="text-text-muted t-size1">
                                            {{ $history->created_at->format('H:i') }}
                                        </span>
                                    </div>
                                    <p class="text-text-muted t-size2 mt-0.5">{{ $history->notes }}</p>
                                    @if ($history->user)
                                        <span class="t-size1 text-text-muted/60 mt-0.5 block">
                                            oleh {{ $history->user->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-text-muted t-size2 py-2">{{ __('Belum ada riwayat.') }}</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
