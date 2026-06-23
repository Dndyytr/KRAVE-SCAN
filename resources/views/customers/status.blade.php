<x-customer-layout :branch="$branch" :table="$order->table_number">
    <div x-data="{
        status: '{{ $order->status }}',
        init() {
            // Poll for order status updates every 15 seconds
            setInterval(() => {
                window.location.reload();
            }, 15000);
        }
    }" class="space-y-6">

        <div class="bg-card border border-border rounded-3xl p-6 text-center space-y-4 shadow-xs">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto
                @if($order->status === 'pending') bg-warning/15 text-warning
                @elseif($order->status === 'confirmed') bg-info/15 text-info
                @elseif($order->status === 'in_process') bg-primary-soft/40 text-accent
                @elseif($order->status === 'completed') bg-success/15 text-success
                @else bg-danger/15 text-danger
                @endif">
                
                @if($order->status === 'pending')
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @elseif($order->status === 'confirmed' || $order->status === 'in_process')
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                @elseif($order->status === 'completed')
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @else
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @endif
            </div>

            <div class="space-y-1">
                <span class="text-text-muted t-size2 font-semibold">Pesanan #{{ $order->id }}</span>
                <h3 class="font-extrabold t-size5 font-heading text-text">
                    @if($order->status === 'pending') Menunggu Pembayaran
                    @elseif($order->status === 'confirmed') Pembayaran Dikonfirmasi
                    @elseif($order->status === 'in_process') Sedang Disiapkan
                    @elseif($order->status === 'completed') Selesai
                    @else Dibatalkan
                    @endif
                </h3>
                <p class="text-text-muted t-size2 px-4">
                    @if($order->status === 'pending')
                        Pesanan Anda telah diterima. Harap tunjukkan halaman ini ke Kasir untuk memproses pembayaran.
                    @elseif($order->status === 'confirmed')
                        Pembayaran berhasil dikonfirmasi. Pesanan Anda segera disiapkan di dapur.
                    @elseif($order->status === 'in_process')
                        Menu lezat Anda sedang dimasak. Harap tunggu sebentar di Meja {{ $order->table_number }}.
                    @elseif($order->status === 'completed')
                        Pesanan telah disajikan. Terima kasih telah menikmati hidangan di {{ $branch }}.
                    @else
                        Pesanan Anda telah dibatalkan. Silakan hubungi staf jika ada kendala.
                    @endif
                </p>
            </div>

            <div class="inline-flex items-center gap-1.5 border border-border px-3 py-1 rounded-full text-text-muted t-size2 font-semibold bg-surface">
                <span class="w-2 h-2 rounded-full 
                    @if($order->status === 'pending') bg-warning
                    @elseif($order->status === 'confirmed') bg-info
                    @elseif($order->status === 'in_process') bg-primary
                    @elseif($order->status === 'completed') bg-success
                    @else bg-danger
                    @endif"></span>
                Status: {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </div>
        </div>

        <!-- Order Items Card -->
        <div class="bg-card border border-border rounded-3xl p-5 space-y-4 shadow-xs">
            <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-2">Rincian Item</h3>
            
            <div class="divide-y divide-border">
                @foreach($order->orderItems as $item)
                    <div class="py-3 flex justify-between items-center first:pt-0 last:pb-0">
                        <div>
                            <h4 class="font-bold t-size3 text-text">{{ $item->menu->name }}</h4>
                            <span class="text-text-muted t-size2">
                                {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                            </span>
                        </div>
                        <span class="font-bold text-text t-size3">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-border pt-4 flex justify-between items-center">
                <span class="font-bold text-text t-size4">Total Pembayaran</span>
                <span class="font-extrabold text-accent t-size5">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Action Button -->
        <div class="text-center pt-2">
            <a href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $order->table_number]) }}" 
               class="inline-flex items-center gap-2 bg-primary hover:bg-primary-strong text-white font-extrabold px-8 py-3 rounded-full t-size3 transition shadow-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Pesan Menu Lain
            </a>
        </div>

    </div>
</x-customer-layout>
