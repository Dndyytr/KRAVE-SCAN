<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('cashier.transactions') }}" class="text-text-muted hover:text-text transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-bold t-size7 font-heading text-text">
                {{ __('Proses Pembayaran Transaksi') }} #{{ $order->id }}
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
                        <span class="text-text-muted block text-right">{{ __('Total Tagihan') }}</span>
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
                                </div>
                            </div>
                            <span class="font-bold text-text t-size3">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-border pt-4 flex justify-between items-center">
                    <span class="font-bold text-text t-size4">{{ __('Total') }}</span>
                    <span class="font-extrabold text-accent t-size5">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

        </div>

        <!-- Column 3: Payment Section -->
        <div class="space-y-6">

            <!-- Interactive Payment Form -->
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
                setPreset(amount) {
                    this.amountPaid = amount;
                },
                formatRupiah(amount) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(amount);
                }
            }" class="bg-card border border-border rounded-2xl p-6 space-y-6 shadow-xs">

                <div class="border-b border-border pb-3">
                    <h3 class="font-bold t-size4 font-heading text-text">{{ __('Metode Pembayaran') }}</h3>
                    <p class="text-text-muted t-size2 mt-0.5">{{ __('Pilih metode transaksi pembayaran kasir.') }}</p>
                </div>

                <form action="{{ route('cashier.orders.payment', $order->id) }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Toggle Buttons for Payment Method -->
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="sr-only">
                            <div class="p-3 border rounded-xl text-center font-bold t-size3 transition"
                                :class="paymentMethod === 'cash' ?
                                    'border-primary bg-primary-soft/30 text-accent font-extrabold shadow-sm' :
                                    'border-border bg-surface text-text-muted hover:bg-surface-alt'">
                                💵 {{ __('Tunai (Cash)') }}
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="sr-only">
                            <div class="p-3 border rounded-xl text-center font-bold t-size3 transition"
                                :class="paymentMethod === 'qris' ?
                                    'border-primary bg-primary-soft/30 text-accent font-extrabold shadow-sm' :
                                    'border-border bg-surface text-text-muted hover:bg-surface-alt'">
                                📱 {{ __('QRIS') }}
                            </div>
                        </label>
                    </div>

                    <!-- Cash Section -->
                    <div x-show="paymentMethod === 'cash'" x-transition class="space-y-4">
                        <div class="space-y-2">
                            <label for="amount_paid" class="font-semibold text-text t-size3 block">
                                {{ __('Uang Tunai Diterima') }}
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-text-muted font-bold t-size3">
                                    Rp
                                </span>
                                <input type="number" id="amount_paid" name="amount_paid" x-model="amountPaid"
                                    class="w-full pl-12 pr-4 py-3 bg-input-bg border border-input-border focus:border-input-focus focus:ring-1 focus:ring-input-focus rounded-xl font-bold t-size4 text-text"
                                    placeholder="Contoh: 50000" :required="paymentMethod === 'cash'">
                            </div>
                        </div>

                        <!-- Preset Amount Helpers -->
                        <div class="space-y-1.5">
                            <span class="t-size1 font-semibold text-text-muted block">{{ __('Pilihan Uang Cepat') }}</span>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="setPreset(totalAmount)"
                                    class="px-3 py-1.5 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">
                                    {{ __('Uang Pas') }}
                                </button>
                                @if ($order->total_amount <= 10000)
                                    <button type="button" @click="setPreset(10000)"
                                        class="px-3 py-1.5 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">Rp 10k</button>
                                @endif
                                @if ($order->total_amount <= 20000)
                                    <button type="button" @click="setPreset(20000)"
                                        class="px-3 py-1.5 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">Rp 20k</button>
                                @endif
                                @if ($order->total_amount <= 50000)
                                    <button type="button" @click="setPreset(50000)"
                                        class="px-3 py-1.5 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">Rp 50k</button>
                                @endif
                                @if ($order->total_amount <= 100000)
                                    <button type="button" @click="setPreset(100000)"
                                        class="px-3 py-1.5 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">Rp 100k</button>
                                @endif
                            </div>
                        </div>

                        <!-- Change Display Calculator -->
                        <div class="bg-surface rounded-xl p-4 border border-border flex items-center justify-between">
                            <span class="text-text-muted font-semibold t-size3">{{ __('Kembalian') }}</span>
                            <span class="font-extrabold t-size5 transition" :class="isInsufficient ? 'text-danger' : 'text-success'"
                                x-text="isInsufficient ? 'Uang kurang' : formatRupiah(change)">
                            </span>
                        </div>
                    </div>

                    <!-- QRIS Section -->
                    <div x-show="paymentMethod === 'qris'" x-transition class="bg-surface rounded-xl p-4 border border-border text-center space-y-2">
                        <span class="text-3xl block">📱</span>
                        <h4 class="font-bold text-text t-size3">{{ __('Simulasi QRIS') }}</h4>
                        <p class="text-text-muted t-size2 px-2">
                            {{ __('Harap verifikasi keberhasilan pembayaran QRIS pada EDC / Aplikasi Merchant Anda secara manual.') }}
                        </p>
                        <div class="inline-block bg-success/15 border border-success/30 text-success text-[10px] uppercase font-bold px-2 py-0.5 rounded">
                            {{ __('Pembayaran Instan Sukses') }}
                        </div>
                    </div>

                    <!-- Confirm Button -->
                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary-strong text-white font-extrabold py-3.5 rounded-xl t-size3 transition shadow-xs flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="isInsufficient">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('Konfirmasi Pembayaran') }}
                    </button>

                </form>
            </div>

            <!-- Cancel Option Card -->
            <div class="bg-card border border-border rounded-2xl p-6 space-y-4 shadow-xs">
                <form action="{{ route('cashier.orders.update-status', $order->id) }}" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit"
                        class="w-full bg-danger/10 hover:bg-danger/25 text-danger border border-danger/30 font-bold py-2.5 rounded-xl t-size3 transition flex items-center justify-center gap-2 cursor-pointer">
                        ❌ {{ __('Batalkan Pesanan') }}
                    </button>
                </form>
            </div>

        </div>

    </div>
</x-app-layout>
