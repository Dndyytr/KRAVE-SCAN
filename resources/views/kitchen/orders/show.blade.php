<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('kitchen.orders') }}" class="text-text-muted hover:text-text transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-bold t-size7 font-heading text-text">
                {{ __('Proses Hidangan') }} #{{ $order->id }}
            </h2>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1 & 2: Order Info & Items -->
        <div class="lg:col-span-2 space-y-6">

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
                        <span class="text-text-muted block">{{ __('Waktu Masuk') }}</span>
                        <span class="font-semibold text-text">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Items Card -->
            <div class="bg-card border border-border rounded-2xl p-6 space-y-4 shadow-xs">
                <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-3">
                    {{ __('Daftar Pesanan Hidangan') }}
                </h3>

                <div class="divide-y divide-border">
                    @foreach ($order->orderItems as $item)
                        <div class="py-4 flex justify-between items-start first:pt-0 last:pb-0">
                            <div class="flex items-start gap-4">
                                @if ($item->menu->image_path)
                                    <img src="{{ Str::startsWith($item->menu->image_path, ['http://', 'https://']) ? $item->menu->image_path : (Str::startsWith($item->menu->image_path, 'storage/') ? asset($item->menu->image_path) : asset('storage/' . $item->menu->image_path)) }}"
                                        alt="{{ $item->menu->name }}" class="w-16 h-16 object-cover rounded-xl border border-border">
                                @else
                                    <div class="w-16 h-16 bg-surface border border-border rounded-xl flex items-center justify-center font-bold text-accent">
                                        {{ substr($item->menu->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-extrabold t-size4 text-text">{{ $item->menu->name }}</h4>
                                    <span class="text-text-muted t-size2 font-semibold block mt-0.5">
                                        Porsi: <span class="text-accent font-extrabold t-size3">{{ $item->quantity }}x</span>
                                    </span>
                                    @if ($item->note)
                                        <div class="mt-2 bg-danger-soft/15 border border-danger-soft text-danger px-3 py-1.5 rounded-xl t-size2 font-semibold">
                                            ⚠️ Catatan Pelanggan: "{{ $item->note }}"
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
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
                                @elseif($history->status === 'in_process') bg-primary
                                @elseif($history->status === 'completed') bg-success
                                @else bg-danger @endif"></span>

                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-text t-size3">
                                        {{ ucfirst(str_replace('_', ' ', $history->status)) }}
                                    </span>
                                    <span class="text-text-muted t-size1">
                                        {{ $history->created_at->format('H:i') }}
                                        ({{ $history->created_at->diffForHumans() }})
                                    </span>
                                </div>
                                <p class="text-text-muted t-size2 mt-0.5">{{ $history->notes }}</p>
                                @if ($history->user)
                                    <span class="t-size1 text-text-muted/60 mt-1 block">
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

        <!-- Column 3: Control Panel -->
        <div class="space-y-6">

            <!-- Status Management Card -->
            <div class="bg-card border border-border rounded-2xl p-6 space-y-4 shadow-xs">
                <div class="border-b border-border pb-3">
                    <h3 class="font-bold t-size4 font-heading text-text">{{ __('Alur Pengerjaan Hidangan') }}</h3>
                    <p class="text-text-muted t-size2 mt-0.5">{{ __('Kendalikan status masak masakan di dapur.') }}</p>
                </div>

                @if ($order->status === 'confirmed')
                    <form action="{{ route('kitchen.orders.update-status', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="in_process">
                        <button type="submit"
                            class="w-full bg-accent hover:bg-accent/90 text-white font-extrabold py-3.5 rounded-xl t-size3 transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                            🍳 {{ __('Mulai Proses Masak') }}
                        </button>
                    </form>
                @elseif($order->status === 'in_process')
                    <form action="{{ route('kitchen.orders.update-status', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit"
                            class="w-full bg-success hover:bg-success-strong text-white font-extrabold py-3.5 rounded-xl t-size3 transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                            ✅ {{ __('Selesaikan & Sajikan') }}
                        </button>
                    </form>
                @else
                    <div class="text-center py-4 bg-surface rounded-xl border border-border text-text-muted t-size3 font-semibold">
                        @if ($order->status === 'completed')
                            🎉 {{ __('Hidangan selesai disajikan.') }}
                        @elseif($order->status === 'pending')
                            ⏳ {{ __('Menunggu konfirmasi pembayaran kasir.') }}
                        @else
                            🚫 {{ __('Pesanan dibatalkan.') }}
                        @endif
                    </div>
                @endif

                @if (in_array($order->status, ['confirmed', 'in_process']))
                    <form action="{{ route('kitchen.orders.update-status', $order->id) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?');" class="mt-2">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit"
                            class="w-full bg-danger/10 hover:bg-danger/25 text-danger border border-danger/30 font-bold py-2.5 rounded-xl t-size3 transition flex items-center justify-center gap-2 cursor-pointer shadow-2xs">
                            ❌ {{ __('Batalkan Pesanan') }}
                        </button>
                    </form>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
