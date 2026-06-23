<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Daftar Pesanan') }}
        </h2>
    </x-slot>

    <div x-data="{
        init() {
            // Auto refresh order list every 15 seconds to fetch new orders
            setInterval(() => {
                window.location.reload();
            }, 15000);
        }
    }" class="space-y-6">

        <!-- Status Filter Bar -->
        <div class="flex flex-wrap gap-2 items-center justify-between bg-card border border-border rounded-2xl p-4 shadow-xs">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('cashier.orders') }}" 
                   class="px-4 py-2 rounded-xl t-size3 font-semibold transition {{ is_null($currentStatus) ? 'bg-primary text-white' : 'bg-surface text-text-muted hover:bg-surface-alt hover:text-text' }}">
                    {{ __('Semua') }}
                </a>
                <a href="{{ route('cashier.orders', ['status' => 'pending']) }}" 
                   class="px-4 py-2 rounded-xl t-size3 font-semibold transition {{ $currentStatus === 'pending' ? 'bg-warning text-white' : 'bg-surface text-text-muted hover:bg-surface-alt hover:text-text' }}">
                    {{ __('Pending') }}
                </a>
                <a href="{{ route('cashier.orders', ['status' => 'confirmed']) }}" 
                   class="px-4 py-2 rounded-xl t-size3 font-semibold transition {{ $currentStatus === 'confirmed' ? 'bg-info text-white' : 'bg-surface text-text-muted hover:bg-surface-alt hover:text-text' }}">
                    {{ __('Confirmed') }}
                </a>
                <a href="{{ route('cashier.orders', ['status' => 'completed']) }}" 
                   class="px-4 py-2 rounded-xl t-size3 font-semibold transition {{ $currentStatus === 'completed' ? 'bg-success text-white' : 'bg-surface text-text-muted hover:bg-surface-alt hover:text-text' }}">
                    {{ __('Completed') }}
                </a>
                <a href="{{ route('cashier.orders', ['status' => 'cancelled']) }}" 
                   class="px-4 py-2 rounded-xl t-size3 font-semibold transition {{ $currentStatus === 'cancelled' ? 'bg-danger text-white' : 'bg-surface text-text-muted hover:bg-surface-alt hover:text-text' }}">
                    {{ __('Cancelled') }}
                </a>
            </div>
            
            <div class="text-text-muted t-size2 font-semibold">
                {{ __('Auto-refresh aktif (15s)') }}
            </div>
        </div>

        <!-- Orders Table / List -->
        <div class="bg-card border border-border rounded-2xl overflow-hidden shadow-xs">
            @if($orders->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 bg-surface-alt text-text-muted/60 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold t-size4 text-text">{{ __('Tidak Ada Pesanan') }}</h3>
                        <p class="text-text-muted t-size2 max-w-sm mx-auto">
                            {{ __('Belum ada pesanan dengan status terpilih untuk saat ini di cabang Anda.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                <th class="py-4 px-6">{{ __('Pesanan') }}</th>
                                <th class="py-4 px-6">{{ __('Meja') }}</th>
                                <th class="py-4 px-6">{{ __('Item Pesanan') }}</th>
                                <th class="py-4 px-6">{{ __('Total') }}</th>
                                <th class="py-4 px-6">{{ __('Status') }}</th>
                                <th class="py-4 px-6">{{ __('Waktu') }}</th>
                                <th class="py-4 px-6 text-right">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($orders as $order)
                                <tr class="hover:bg-surface/50 transition">
                                    <!-- Order ID -->
                                    <td class="py-4 px-6 font-bold text-text t-size3">
                                        #{{ $order->id }}
                                    </td>
                                    
                                    <!-- Table Number -->
                                    <td class="py-4 px-6">
                                        <span class="bg-primary-soft/50 text-accent font-extrabold px-3 py-1 rounded-full t-size2 border border-primary-soft">
                                            {{ __('Meja') }} {{ $order->table_number }}
                                        </span>
                                    </td>
                                    
                                    <!-- Order Items summary -->
                                    <td class="py-4 px-6 max-w-xs truncate t-size3 text-text-muted">
                                        @foreach($order->orderItems as $item)
                                            {{ $item->menu->name }} ({{ $item->quantity }}){{ !$loop->last ? ',' : '' }}
                                        @endforeach
                                    </td>
                                    
                                    <!-- Total Amount -->
                                    <td class="py-4 px-6 font-extrabold text-accent t-size3">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                    
                                    <!-- Status -->
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full t-size1 font-bold border
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
                                    </td>
                                    
                                    <!-- Order Time -->
                                    <td class="py-4 px-6 t-size2 text-text-muted">
                                        {{ $order->created_at->format('H:i') }}
                                        <span class="block text-[10px] text-text-muted/60">{{ $order->created_at->format('d M Y') }}</span>
                                    </td>
                                    
                                    <!-- Action -->
                                    <td class="py-4 px-6 text-right">
                                        @if($order->status === 'pending')
                                            <a href="{{ route('cashier.orders.show', $order->id) }}" 
                                               class="inline-flex items-center bg-primary hover:bg-primary-strong text-white font-extrabold px-4 py-2 rounded-xl t-size2 transition shadow-xs cursor-pointer">
                                                {{ __('Proses Pembayaran') }}
                                            </a>
                                        @else
                                            <a href="{{ route('cashier.orders.show', $order->id) }}" 
                                               class="inline-flex items-center bg-surface border border-border text-text hover:bg-surface-alt font-semibold px-4 py-2 rounded-xl t-size2 transition cursor-pointer">
                                                {{ __('Detail') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($orders->hasPages())
                    <div class="bg-surface-alt border-t border-border px-6 py-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</x-app-layout>
