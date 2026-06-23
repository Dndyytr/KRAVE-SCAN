<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Riwayat Transaksi Global') }}
        </h2>
    </x-slot>

    <div class="space-y-6 anim-fade">
        <!-- Aggregation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Revenue -->
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs flex items-center gap-4 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-success/5 rounded-full"></div>
                <div class="w-12 h-12 bg-success/15 text-success rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-text-muted t-size2 font-semibold block uppercase tracking-wider">{{ __('Total Pendapatan') }}</span>
                    <span class="text-text font-black t-size7 block mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-text-muted/60 mt-0.5 block">*{{ __('Hanya transaksi sukses') }}</span>
                </div>
            </div>

            <!-- Cash Revenue -->
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs flex items-center gap-4 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-primary-soft/10 rounded-full"></div>
                <div class="w-12 h-12 bg-primary-soft/40 text-accent rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-text-muted t-size2 font-semibold block uppercase tracking-wider">{{ __('Transaksi Tunai (Cash)') }}</span>
                    <span class="text-text font-black t-size7 block mt-1">Rp {{ number_format($totalCash, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-text-muted/60 mt-0.5 block">*{{ __('Hanya transaksi sukses') }}</span>
                </div>
            </div>

            <!-- QRIS Revenue -->
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs flex items-center gap-4 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-info/5 rounded-full"></div>
                <div class="w-12 h-12 bg-info/15 text-info rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-text-muted t-size2 font-semibold block uppercase tracking-wider">{{ __('Transaksi QRIS') }}</span>
                    <span class="text-text font-black t-size7 block mt-1">Rp {{ number_format($totalQris, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-text-muted/60 mt-0.5 block">*{{ __('Hanya transaksi sukses') }}</span>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs space-y-4">
            <div>
                <h3 class="font-bold t-size5 text-text font-heading">
                    {{ __('Filter Transaksi') }}
                </h3>
                <p class="text-text-muted t-size3 mt-0.5">
                    {{ __('Gunakan filter untuk membatasi hasil pencarian berdasarkan tanggal, metode, dan cabang.') }}
                </p>
            </div>

            <form method="GET" action="{{ route('admin.transactions.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 pt-2">
                <!-- Method Filter -->
                <div class="space-y-1">
                    <label for="method" class="block t-size2 font-semibold text-text-muted">{{ __('Metode') }}</label>
                    <select name="method" id="method" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                        <option value="">{{ __('Semua Metode') }}</option>
                        <option value="cash" {{ $currentMethod === 'cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
                        <option value="qris" {{ $currentMethod === 'qris' ? 'selected' : '' }}>{{ __('QRIS') }}</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="space-y-1">
                    <label for="status" class="block t-size2 font-semibold text-text-muted">{{ __('Status') }}</label>
                    <select name="status" id="status" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                        <option value="">{{ __('Semua Status') }}</option>
                        <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="success" {{ $currentStatus === 'success' ? 'selected' : '' }}>{{ __('Success') }}</option>
                        <option value="failed" {{ $currentStatus === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                    </select>
                </div>

                <!-- Branch Filter (Only for Super Admin) -->
                @if($isSuperAdmin)
                    <div class="space-y-1">
                        <label for="branch_id" class="block t-size2 font-semibold text-text-muted">{{ __('Cabang') }}</label>
                        <select name="branch_id" id="branch_id" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                            <option value="">{{ __('Semua Cabang') }}</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $currentBranchId == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="hidden">
                        <input type="hidden" name="branch_id" value="{{ $currentBranchId }}">
                    </div>
                @endif

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
                <div class="md:col-span-5 flex justify-end gap-3 pt-2">
                    <button type="submit" class="bg-primary hover:bg-primary-strong text-white font-bold px-6 py-2.5 rounded-xl transition shadow-xs cursor-pointer">
                        {{ __('Terapkan Filter') }}
                    </button>
                    @if($currentMethod || $currentStatus || $currentBranchId || $startDate || $endDate)
                        <a href="{{ route('admin.transactions.index') }}" class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-6 py-2.5 rounded-xl transition cursor-pointer text-center flex items-center justify-center font-bold">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Transactions Table Card -->
        <div class="bg-card border border-border rounded-2xl shadow-xs overflow-hidden">
            @if($transactions->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 bg-surface-alt text-text-muted/60 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold t-size4 text-text">{{ __('Tidak Ada Transaksi') }}</h3>
                        <p class="text-text-muted t-size2 max-w-sm mx-auto">
                            {{ __('Tidak menemukan catatan transaksi yang sesuai dengan kriteria filter saat ini.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                <th class="py-4 px-6">{{ __('ID Transaksi') }}</th>
                                <th class="py-4 px-6">{{ __('No Struk') }}</th>
                                @if($isSuperAdmin)
                                    <th class="py-4 px-6">{{ __('Cabang') }}</th>
                                @endif
                                <th class="py-4 px-6">{{ __('Referensi Pesanan') }}</th>
                                <th class="py-4 px-6">{{ __('Metode') }}</th>
                                <th class="py-4 px-6">{{ __('Nominal') }}</th>
                                <th class="py-4 px-6">{{ __('Status') }}</th>
                                <th class="py-4 px-6">{{ __('Waktu') }}</th>
                                <th class="py-4 px-6 text-right">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($transactions as $tx)
                                <tr class="hover:bg-surface/30 transition">
                                    <td class="py-4 px-6 font-bold text-text t-size3">
                                        #{{ $tx->id }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <code class="text-text font-mono t-size2 bg-surface px-2 py-1 rounded border border-border">
                                            {{ $tx->receipts->first()?->receipt_number ?? '-' }}
                                        </code>
                                    </td>
                                    @if($isSuperAdmin)
                                        <td class="py-4 px-6">
                                            <span class="bg-surface border border-border text-text-muted px-2.5 py-0.5 rounded-lg t-size2 font-semibold">
                                                {{ $tx->order->branch->name ?? '-' }}
                                            </span>
                                        </td>
                                    @endif
                                    <td class="py-4 px-6">
                                        <a href="{{ route('admin.orders.show', $tx->order_id) }}" class="text-primary hover:text-primary-strong font-bold t-size3 hover:underline">
                                            #{{ $tx->order_id }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full t-size1 font-bold border
                                            @if($tx->method === 'cash') bg-primary-soft/40 text-accent border-primary-soft
                                            @else bg-info/15 text-info border-info/30
                                            @endif">
                                            @if($tx->method === 'cash')
                                                <svg class="w-3 h-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                Cash
                                            @else
                                                <svg class="w-3 h-3 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                                </svg>
                                                QRIS
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-extrabold text-text t-size3">
                                        Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full t-size1 font-bold border
                                            @if($tx->status === 'pending') bg-warning/15 text-warning border-warning/30
                                            @elseif($tx->status === 'success') bg-success/15 text-success border-success/30
                                            @else bg-danger/15 text-danger border-danger/30
                                            @endif">
                                            <span class="w-1.5 h-1.5 rounded-full 
                                                @if($tx->status === 'pending') bg-warning
                                                @elseif($tx->status === 'success') bg-success
                                                @else bg-danger
                                                @endif"></span>
                                            {{ ucfirst($tx->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 t-size2 text-text-muted">
                                        {{ $tx->created_at->format('H:i') }}
                                        <span class="block text-[10px] text-text-muted/60">{{ $tx->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('admin.orders.show', $tx->order_id) }}" 
                                           class="inline-flex items-center bg-surface border border-border text-text hover:bg-surface-alt font-semibold px-4 py-2 rounded-xl t-size2 transition cursor-pointer">
                                            {{ __('Detail Pesanan') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="px-6 py-4 border-t border-border">
                        {{ $transactions->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
