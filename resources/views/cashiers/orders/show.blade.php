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
            
            <!-- Alert Session Messages -->
            @if(session('success'))
                <div class="bg-success/15 border border-success/30 text-success px-4 py-3 rounded-xl t-size3 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-danger/15 border border-danger/30 text-danger px-4 py-3 rounded-xl t-size3 font-semibold">
                    {{ session('error') }}
                </div>
            @endif

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
                        <span class="text-text-muted t-size2 font-semibold uppercase tracking-wider block text-right">{{ __('Status') }}</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full t-size2 font-bold border mt-1
                            @if($order->status === 'pending') bg-warning/15 text-warning border-warning/30
                            @elseif($order->status === 'confirmed') bg-info/15 text-info border-info/30
                            @elseif($order->status === 'in_process') bg-primary-soft/40 text-accent border-primary-soft
                            @elseif($order->status === 'completed') bg-success/15 text-success border-success/30
                            @else bg-danger/15 text-danger border-danger/30
                            @endif">
                            <span class="w-1.5 h-1.5 rounded-full 
                                @if($order->status === 'pending') bg-warning
                                @elseif($order->status === 'confirmed') bg-info
                                @elseif($order->status === 'in_process') bg-primary
                                @elseif($order->status === 'completed') bg-success
                                @else bg-danger
                                @endif"></span>
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
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
                    @foreach($order->orderItems as $item)
                        <div class="py-4 flex justify-between items-center first:pt-0 last:pb-0">
                            <div class="flex items-center gap-4">
                                @if($item->menu->image_path)
                                    <img src="{{ asset('storage/' . $item->menu->image_path) }}" alt="{{ $item->menu->name }}" class="w-12 h-12 object-cover rounded-xl border border-border">
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
                            <!-- Icon/Bullet indicator -->
                            <span class="absolute -left-[31px] top-1.5 w-4 h-4 rounded-full border-2 border-white 
                                @if($history->status === 'pending') bg-warning
                                @elseif($history->status === 'confirmed') bg-info
                                @elseif($history->status === 'in_process') bg-primary
                                @elseif($history->status === 'completed') bg-success
                                @else bg-danger
                                @endif"></span>
                            
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-text t-size3">
                                        {{ ucfirst(str_replace('_', ' ', $history->status)) }}
                                    </span>
                                    <span class="text-text-muted text-[11px]">
                                        {{ $history->created_at->format('H:i') }} ({{ $history->created_at->diffForHumans() }})
                                    </span>
                                </div>
                                <p class="text-text-muted t-size2 mt-0.5">{{ $history->notes }}</p>
                                @if($history->user)
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

        <!-- Column 3: Payment Section -->
        <div class="space-y-6">
            
            @if($order->status === 'pending')
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
                                     :class="paymentMethod === 'cash' ? 'border-primary bg-primary-soft/30 text-accent font-extrabold shadow-sm' : 'border-border bg-surface text-text-muted hover:bg-surface-alt'">
                                    💵 {{ __('Tunai (Cash)') }}
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="sr-only">
                                <div class="p-3 border rounded-xl text-center font-bold t-size3 transition"
                                     :class="paymentMethod === 'qris' ? 'border-primary bg-primary-soft/30 text-accent font-extrabold shadow-sm' : 'border-border bg-surface text-text-muted hover:bg-surface-alt'">
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
                                    <input type="number" 
                                           id="amount_paid" 
                                           name="amount_paid" 
                                           x-model="amountPaid"
                                           class="w-full pl-12 pr-4 py-3 bg-input-bg border border-input-border focus:border-input-focus focus:ring-1 focus:ring-input-focus rounded-xl font-bold t-size4 text-text"
                                           placeholder="Contoh: 50000"
                                           :required="paymentMethod === 'cash'">
                                </div>
                            </div>

                            <!-- Preset Amount Helpers -->
                            <div class="space-y-1.5">
                                <span class="t-size1 font-semibold text-text-muted block">{{ __('Pilihan Uang Cepat') }}</span>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" 
                                            @click="setPreset(totalAmount)"
                                            class="px-3 py-1.5 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">
                                        {{ __('Uang Pas') }}
                                    </button>
                                    @if($order->total_amount <= 10000)
                                        <button type="button" @click="setPreset(10000)" class="px-3 py-1.5 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">Rp 10k</button>
                                    @endif
                                    @if($order->total_amount <= 20000)
                                        <button type="button" @click="setPreset(20000)" class="px-3 py-1.5 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">Rp 20k</button>
                                    @endif
                                    @if($order->total_amount <= 50000)
                                        <button type="button" @click="setPreset(50000)" class="px-3 py-1.5 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">Rp 50k</button>
                                    @endif
                                    @if($order->total_amount <= 100000)
                                        <button type="button" @click="setPreset(100000)" class="px-3 py-1.5 border border-border bg-surface hover:bg-surface-alt rounded-lg font-semibold t-size2 text-text transition">Rp 100k</button>
                                    @endif
                                </div>
                            </div>

                            <!-- Change Display Calculator -->
                            <div class="bg-surface rounded-xl p-4 border border-border flex items-center justify-between">
                                <span class="text-text-muted font-semibold t-size3">{{ __('Kembalian') }}</span>
                                <span class="font-extrabold t-size5 transition"
                                      :class="isInsufficient ? 'text-danger' : 'text-success'"
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
            @else
                <!-- Payment Completed Display -->
                <div class="bg-card border border-border rounded-2xl p-6 space-y-6 shadow-xs">
                    
                    <div class="border-b border-border pb-3">
                        <h3 class="font-bold t-size4 font-heading text-text">{{ __('Informasi Pembayaran') }}</h3>
                        <p class="text-text-muted t-size2 mt-0.5">{{ __('Pesanan telah terbayar lunas.') }}</p>
                    </div>

                    @foreach($order->payments as $payment)
                        <div class="bg-surface border border-border rounded-xl p-4 space-y-3 t-size3">
                            <div class="flex justify-between items-center">
                                <span class="text-text-muted">{{ __('Metode') }}</span>
                                <span class="font-bold text-text uppercase">
                                    {{ $payment->method === 'cash' ? __('💵 Tunai') : __('📱 QRIS') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-text-muted">{{ __('Status') }}</span>
                                <span class="font-bold text-success uppercase">
                                    {{ $payment->status }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-text-muted">{{ __('Nominal Transaksi') }}</span>
                                <span class="font-extrabold text-accent">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Receipts link -->
                        @foreach($payment->receipts as $receipt)
                            <div class="space-y-3">
                                <div class="bg-card border border-border rounded-xl p-4 text-center space-y-2">
                                    <span class="text-text-muted t-size2 block">{{ __('Nomor Struk') }}</span>
                                    <code class="font-mono font-bold text-text block t-size3 bg-surface border border-border py-1 px-3 rounded-lg select-all">
                                        {{ $receipt->receipt_number }}
                                    </code>
                                </div>
                                
                                <a href="{{ route('cashier.receipts.show', $receipt->id) }}" 
                                   target="_blank"
                                   class="w-full bg-surface border border-border hover:bg-surface-alt text-text font-bold py-3 rounded-xl t-size3 transition flex items-center justify-center gap-2 cursor-pointer shadow-2xs">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    {{ __('Cetak Struk Digital') }}
                                </a>
                            </div>
                        @endforeach
                    @endforeach

                </div>
            @endif

            <!-- Status Management Card -->
            <div class="bg-card border border-border rounded-2xl p-6 space-y-4 shadow-xs">
                <div class="border-b border-border pb-3">
                    <h3 class="font-bold t-size4 font-heading text-text">{{ __('Kelola Status Pesanan') }}</h3>
                    <p class="text-text-muted t-size2 mt-0.5">{{ __('Perbarui progres pengerjaan pesanan.') }}</p>
                </div>

                @if($order->status === 'confirmed')
                    <form action="{{ route('cashier.orders.update-status', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="in_process">
                        <button type="submit" class="w-full bg-accent hover:bg-accent/90 text-white font-extrabold py-3.5 rounded-xl t-size3 transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                            ⚙️ {{ __('Mulai Proses Masak') }}
                        </button>
                    </form>
                @elseif($order->status === 'in_process')
                    <form action="{{ route('cashier.orders.update-status', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="w-full bg-success hover:bg-success-strong text-white font-extrabold py-3.5 rounded-xl t-size3 transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                            ✅ {{ __('Selesaikan & Sajikan') }}
                        </button>
                    </form>
                @endif

                @if(in_array($order->status, ['pending', 'confirmed', 'in_process']))
                    <form action="{{ route('cashier.orders.update-status', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="w-full bg-danger/10 hover:bg-danger/25 text-danger border border-danger/30 font-bold py-2.5 rounded-xl t-size3 transition flex items-center justify-center gap-2 cursor-pointer">
                            ❌ {{ __('Batalkan Pesanan') }}
                        </button>
                    </form>
                @else
                    <div class="text-center py-2 text-text-muted t-size3">
                        @if($order->status === 'completed')
                            🎉 {{ __('Pesanan selesai sepenuhnya.') }}
                        @else
                            🚫 {{ __('Pesanan ini dibatalkan.') }}
                        @endif
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
