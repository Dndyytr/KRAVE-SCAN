<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Monitoring Pesanan Global') }}
        </h2>
    </x-slot>

    <div class="space-y-6 anim-fade">
        <!-- Filter Card -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs space-y-4">
            <div>
                <h3 class="font-bold t-size5 text-text font-heading">
                    {{ __('Filter & Pencarian Pesanan') }}
                </h3>
                <p class="text-text-muted t-size3 mt-0.5">
                    {{ __('Pantau aktivitas transaksi pesanan dari seluruh cabang yang terintegrasi.') }}
                </p>
            </div>

            <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-2">
                <!-- Status Filter -->
                <div class="space-y-1">
                    <label for="status" class="block t-size2 font-semibold text-text-muted">{{ __('Status') }}</label>
                    <select name="status" id="status" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                        <option value="">{{ __('Semua Status') }}</option>
                        <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="confirmed" {{ $currentStatus === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                        <option value="in_process" {{ $currentStatus === 'in_process' ? 'selected' : '' }}>{{ __('In Process') }}</option>
                        <option value="completed" {{ $currentStatus === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        <option value="cancelled" {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>

                <!-- Branch Filter -->
                <div class="space-y-1">
                    <label for="branch_id" class="block t-size2 font-semibold text-text-muted">{{ __('Cabang') }}</label>
                    <select name="branch_id" id="branch_id" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                        <option value="">{{ __('Semua Cabang') }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $currentBranchId == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }} ({{ $branch->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Date -->
                <div class="space-y-1">
                    <label for="start_date" class="block t-size2 font-semibold text-text-muted">{{ __('Tanggal Mulai') }}</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                           class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2 t-size4 outline-hidden transition">
                </div>

                <!-- End Date -->
                <div class="space-y-1">
                    <label for="end_date" class="block t-size2 font-semibold text-text-muted">{{ __('Tanggal Selesai') }}</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                           class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2 t-size4 outline-hidden transition">
                </div>

                <!-- Action Buttons -->
                <div class="md:col-span-4 flex justify-end gap-3 pt-2">
                    <button type="submit" class="bg-primary hover:bg-primary-strong text-white font-bold px-6 py-2.5 rounded-xl transition shadow-xs cursor-pointer">
                        {{ __('Terapkan Filter') }}
                    </button>
                    @if($currentStatus || $currentBranchId || $startDate || $endDate)
                        <a href="{{ route('admin.orders.index') }}" class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-6 py-2.5 rounded-xl transition cursor-pointer text-center flex items-center justify-center font-bold">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Orders Table Card -->
        <div class="bg-card border border-border rounded-2xl shadow-xs overflow-hidden">
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
                            {{ __('Tidak menemukan pesanan yang sesuai dengan filter saat ini.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                <th class="py-4 px-6">{{ __('ID Pesanan') }}</th>
                                <th class="py-4 px-6">{{ __('Cabang') }}</th>
                                <th class="py-4 px-6">{{ __('Meja') }}</th>
                                <th class="py-4 px-6">{{ __('Item Pesanan') }}</th>
                                <th class="py-4 px-6">{{ __('Total') }}</th>
                                <th class="py-4 px-6">{{ __('Status') }}</th>
                                <th class="py-4 px-6">{{ __('Waktu Transaksi') }}</th>
                                <th class="py-4 px-6 text-right">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($orders as $order)
                                <tr class="hover:bg-surface/30 transition">
                                    <td class="py-4 px-6 font-bold text-text t-size3">
                                        #{{ $order->id }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="bg-surface border border-border text-text-muted px-2.5 py-0.5 rounded-lg t-size2 font-semibold">
                                            {{ $order->branch->name }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="bg-primary-soft/50 text-accent font-extrabold px-3 py-1 rounded-full t-size2 border border-primary-soft">
                                            {{ __('Meja') }} {{ $order->table_number }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 max-w-xs truncate t-size3 text-text-muted">
                                        @foreach($order->orderItems as $item)
                                            {{ $item->menu->name }} ({{ $item->quantity }}){{ !$loop->last ? ',' : '' }}
                                        @endforeach
                                    </td>
                                    <td class="py-4 px-6 font-extrabold text-accent t-size3">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <x-status-badge :status="$order->status" />
                                    </td>
                                    <td class="py-4 px-6 t-size2 text-text-muted">
                                        {{ $order->created_at->format('H:i') }}
                                        <span class="block text-[10px] text-text-muted/60">{{ $order->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" 
                                           class="inline-flex items-center bg-surface border border-border text-text hover:bg-surface-alt font-semibold px-4 py-2 rounded-xl t-size2 transition cursor-pointer">
                                            {{ __('Detail') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-border">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
