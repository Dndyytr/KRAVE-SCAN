<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 t-size3 text-text-muted">
            <a href="{{ route('cashier.transactions') }}" class="hover:text-text transition">Transaksi</a>
            <span>›</span>
            <span class="text-text font-bold">INV-{{ date('Y', strtotime($order->created_at)) }}-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 anim-fade">

        {{-- Column 1 & 2: Invoice & Payment --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Invoice Header Card --}}
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-border pb-5 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-primary-soft/40 rounded-full flex items-center justify-center">
                            <span class="text-accent font-extrabold t-size5">🍽️</span>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-text t-size5 font-heading">{{ config('app.name', 'KraveScan') }}</h3>
                            <p class="text-text-muted t-size2">Sistem Pemesanan Digital</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-text-muted t-size2 block font-semibold uppercase tracking-wider">INVOICE</span>
                        <span class="font-extrabold text-accent t-size5 font-heading block">
                            INV-{{ date('Y', strtotime($order->created_at)) }}-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 t-size3">
                    <div>
                        <span class="text-text-muted block font-semibold">Tanggal</span>
                        <span class="font-bold text-text">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-text-muted block font-semibold">Pelanggan</span>
                        <span class="font-bold text-text">{{ $order->customer_name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-text-muted block font-semibold">Meja</span>
                        <span class="font-bold text-text">{{ $order->table_number }}</span>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-3 mb-4 flex items-center gap-2">
                    <span class="t-size3">🧾</span> {{ __('Daftar Item') }}
                </h3>

                {{-- Table Header --}}
                <div class="hidden sm:grid grid-cols-12 gap-2 text-text-muted t-size2 font-bold uppercase tracking-wider pb-2 border-b border-border">
                    <div class="col-span-6">Menu</div>
                    <div class="col-span-2 text-center">Qty</div>
                    <div class="col-span-4 text-right">Total</div>
                </div>

                <div class="divide-y divide-border">
                    @foreach ($order->orderItems as $item)
                        <div class="py-3 sm:grid grid-cols-12 gap-2 items-center">
                            <div class="col-span-6 flex items-center gap-3">
                                @if ($item->menu->image_path)
                                    <img src="{{ Str::startsWith($item->menu->image_path, ['http://', 'https://']) ? $item->menu->image_path : (Str::startsWith($item->menu->image_path, 'storage/') ? asset($item->menu->image_path) : asset('storage/' . $item->menu->image_path)) }}"
                                        alt="{{ $item->menu->name }}" class="w-10 h-10 object-cover rounded-lg border border-border shrink-0">
                                @else
                                    <div
                                        class="w-10 h-10 bg-surface border border-border rounded-lg flex items-center justify-center font-bold text-accent t-size2 shrink-0">
                                        {{ substr($item->menu->name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="font-bold t-size3 text-text">{{ $item->menu->name }}</span>
                            </div>
                            <div class="col-span-2 text-center font-semibold t-size3 text-text">{{ $item->quantity }}</div>
                            <div class="col-span-4 text-right font-bold t-size3 text-text">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="border-t border-border pt-4 space-y-2 t-size3">
                    <div class="flex justify-between items-center">
                        <span class="text-text-muted">Subtotal</span>
                        <span class="font-semibold text-text">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-text-muted">Pajak (10%)</span>
                        <span class="font-semibold text-text">Rp 0</span>
                    </div>
                    <div class="border-t border-border pt-3 flex justify-between items-center">
                        <span class="font-extrabold text-text t-size5">TOTAL BAYAR</span>
                        <span class="font-extrabold text-accent t-size6">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Thank you message --}}
            @if ($order->payments->count() > 0)
                <div class="bg-card border border-border rounded-2xl p-6 shadow-xs text-center">
                    <p class="text-accent font-heading font-bold t-size5 italic">
                        Terima kasih 💕
                    </p>
                    <p class="text-text-muted t-size3 mt-1">atas kunjungan Anda!</p>
                </div>
            @endif

        </div>

        {{-- Column 3: Sidebar --}}
        <div class="space-y-6">

            {{-- Payment Info Summary --}}
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                <h3 class="font-bold t-size4 font-heading text-text border-b border-border pb-3 mb-4">
                    {{ __('Informasi Pembayaran') }}
                </h3>

                @if ($order->payments->count() > 0)
                    @foreach ($order->payments as $payment)
                        <div class="space-y-3 t-size3">
                            <div class="flex justify-between items-center">
                                <span class="text-text-muted">Metode</span>
                                <span class="font-bold text-text">{{ $payment->method === 'cash' ? 'Tunai' : 'QRIS' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-text-muted">Status</span>
                                <span class="bg-success/15 text-success border border-success/30 font-bold px-3 py-0.5 rounded-full t-size2">Lunas</span>
                            </div>
                            <div class="border-t border-border pt-2 flex justify-between items-center">
                                <span class="text-text-muted">Total Bayar</span>
                                <span class="font-bold text-text">Rp {{ number_format($payment->cash_received ?? $payment->amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-text-muted">Kembalian</span>
                                <span class="font-bold text-text">Rp {{ number_format($payment->change ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Payment Form (if not yet paid) --}}
                    <div x-data="{
                        paymentMethod: 'cash',
                        totalAmount: {{ $order->total_amount }},
                        amountPaid: '',
                        get change() {
                            if (!this.amountPaid || this.amountPaid === '') return 0;
                            let numericPaid = parseFloat(this.amountPaid);
                            if (isNaN(numericPaid)) return 0;
                            return Math.max(0, numericPaid - this.totalAmount);
                        },
                        get isInsufficient() {
                            if (this.paymentMethod !== 'cash') return false;
                            if (!this.amountPaid || this.amountPaid === '') return true;
                            let numericPaid = parseFloat(this.amountPaid);
                            return isNaN(numericPaid) || numericPaid < this.totalAmount;
                        },
                        setPreset(amount) { this.amountPaid = amount; },
                        formatRupiah(amount) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(amount);
                        }
                    }" class="space-y-5">

                        <form action="{{ route('cashier.orders.payment', $order->id) }}" method="POST" class="space-y-5">
                            @csrf

                            {{-- Method Toggle --}}
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="sr-only">
                                    <div class="p-2.5 border rounded-xl text-center font-bold t-size3 transition"
                                        :class="paymentMethod === 'cash' ?
                                            'border-primary bg-primary-soft/30 text-accent' :
                                            'border-border bg-surface text-text-muted hover:bg-surface-alt'">
                                        💵 Tunai
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="sr-only">
                                    <div class="p-2.5 border rounded-xl text-center font-bold t-size3 transition"
                                        :class="paymentMethod === 'qris' ?
                                            'border-primary bg-primary-soft/30 text-accent' :
                                            'border-border bg-surface text-text-muted hover:bg-surface-alt'">
                                        📱 QRIS
                                    </div>
                                </label>
                            </div>

                            {{-- Cash Section --}}
                            <div x-show="paymentMethod === 'cash'" x-transition class="space-y-3">
                                <div class="space-y-1.5">
                                    <label class="font-semibold text-text t-size3 block">Uang Diterima</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-text-muted font-bold t-size3">Rp</span>
                                        <input type="number" name="amount_paid" x-model="amountPaid"
                                            class="w-full pl-10 pr-4 py-2.5 bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl font-bold t-size4 text-text"
                                            placeholder="50000" :required="paymentMethod === 'cash'">
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" @click="setPreset(totalAmount)"
                                        class="px-2.5 py-1 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">Uang
                                        Pas</button>
                                    @if ($order->total_amount <= 50000)
                                        <button type="button" @click="setPreset(50000)"
                                            class="px-2.5 py-1 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">50k</button>
                                    @endif
                                    @if ($order->total_amount <= 100000)
                                        <button type="button" @click="setPreset(100000)"
                                            class="px-2.5 py-1 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">100k</button>
                                    @endif
                                </div>

                                <div class="bg-surface rounded-xl p-3 border border-border flex items-center justify-between">
                                    <span class="text-text-muted font-semibold t-size3">Kembalian</span>
                                    <span class="font-extrabold t-size4 transition" :class="isInsufficient ? 'text-danger' : 'text-success'"
                                        x-text="isInsufficient ? 'Kurang' : formatRupiah(change)"></span>
                                </div>
                            </div>

                            {{-- QRIS Section --}}
                            <div x-show="paymentMethod === 'qris'" x-transition class="bg-surface rounded-xl p-4 border border-border text-center space-y-2">
                                <span class="text-2xl block">📱</span>
                                <h4 class="font-bold text-text t-size3">Simulasi QRIS</h4>
                                <p class="text-text-muted t-size2">Verifikasi pembayaran QRIS pada EDC / Aplikasi Merchant.</p>
                            </div>

                            <button type="submit"
                                class="w-full bg-primary hover:bg-primary-strong text-white font-extrabold py-3 rounded-xl t-size3 transition shadow-xs flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="isInsufficient">
                                ✅ Konfirmasi Pembayaran
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Action Buttons (when paid) --}}
            @if ($order->payments->count() > 0)
                <div class="space-y-3">
                    @foreach ($order->payments as $payment)
                        @foreach ($payment->receipts as $receipt)
                            <a href="{{ route('cashier.receipts.show', $receipt->id) }}" target="_blank"
                                class="w-full bg-success/10 hover:bg-success/20 text-success border border-success/30 font-bold py-3 rounded-xl t-size3 transition flex items-center justify-center gap-2 cursor-pointer">
                                📥 Download Invoice
                            </a>
                            <a href="{{ route('cashier.receipts.show', $receipt->id) }}" target="_blank"
                                class="w-full bg-warning/10 hover:bg-warning/20 text-warning border border-warning/30 font-bold py-3 rounded-xl t-size3 transition flex items-center justify-center gap-2 cursor-pointer">
                                🖨️ Cetak Ulang
                            </a>
                        @endforeach
                    @endforeach

                    @if ($order->customer_contact)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_contact) }}?text={{ urlencode('Terima kasih sudah berkunjung! Struk pembayaran Anda: INV-' . date('Y', strtotime($order->created_at)) . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT)) }}"
                            target="_blank"
                            class="w-full bg-[#25D366]/10 hover:bg-[#25D366]/20 text-[#25D366] border border-[#25D366]/30 font-bold py-3 rounded-xl t-size3 transition flex items-center justify-center gap-2 cursor-pointer">
                            💬 Kirim ke WhatsApp
                        </a>
                    @endif
                </div>
            @else
                {{-- Cancel Option (when not paid) --}}
                <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                    <form action="{{ route('cashier.orders.update-status', $order->id) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit"
                            class="w-full bg-danger/10 hover:bg-danger/20 text-danger border border-danger/30 font-bold py-3 rounded-xl t-size3 transition flex items-center justify-center gap-2 cursor-pointer">
                            ❌ Batalkan Pesanan
                        </button>
                    </form>
                </div>
            @endif

            {{-- Pesanan Terkait --}}
            <div class="bg-card border border-border rounded-2xl p-5 shadow-xs">
                <h3 class="font-bold t-size4 font-heading text-text border-b border-border pb-3 mb-3">
                    {{ __('Pesanan Terkait') }}
                </h3>
                <a href="{{ route('cashier.orders.show', $order->id) }}"
                    class="flex items-center justify-between bg-surface border border-border rounded-xl p-3 hover:bg-surface-alt transition group">
                    <div>
                        <span class="font-extrabold text-accent t-size3 block">#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-text-muted t-size2">Meja {{ $order->table_number }} • {{ $order->customer_name ?? '-' }} •
                            {{ $order->orderItems->count() }} item</span>
                    </div>
                    <svg class="w-5 h-5 text-text-muted group-hover:text-accent transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>

            {{-- Riwayat Transaksi --}}
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
                <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-3 mb-4 flex items-center gap-2">
                    <span class="t-size3">📜</span> {{ __('Riwayat Transaksi') }}
                </h3>

                <div class="relative pl-6 border-l-2 border-primary-soft/50 space-y-5">
                    {{-- Order Created --}}
                    <div class="relative">
                        <span class="absolute -left-[31px] top-1 w-4 h-4 rounded-full border-2 border-white bg-info"></span>
                        <div>
                            <span class="font-extrabold text-text t-size3">Pesanan dibuat</span>
                            <span class="text-text-muted text-[11px] ml-1">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            @if ($order->histories->first() && $order->histories->first()->user)
                                <span class="text-[10px] text-text-muted/60 block mt-0.5">oleh
                                    {{ $order->histories->first()->user->name ?? $order->customer_name }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Payment --}}
                    @foreach ($order->payments as $payment)
                        <div class="relative">
                            <span class="absolute -left-[31px] top-1 w-4 h-4 rounded-full border-2 border-white bg-success"></span>
                            <div>
                                <span class="font-extrabold text-text t-size3">Pembayaran diterima
                                    ({{ $payment->method === 'cash' ? 'Tunai' : 'QRIS' }})</span>
                                <span class="text-text-muted text-[11px] ml-1">{{ $payment->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    @endforeach

                    {{-- Receipts --}}
                    @foreach ($order->payments as $payment)
                        @foreach ($payment->receipts as $receipt)
                            <div class="relative">
                                <span class="absolute -left-[31px] top-1 w-4 h-4 rounded-full border-2 border-white bg-accent"></span>
                                <div>
                                    <span class="font-extrabold text-text t-size3">Struk dicetak</span>
                                    <span class="text-text-muted text-[11px] ml-1">{{ $receipt->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
