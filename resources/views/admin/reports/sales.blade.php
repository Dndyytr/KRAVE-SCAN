<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Laporan Penjualan') }}
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
                    <span class="text-[10px] text-text-muted/60 mt-0.5 block">*{{ __('Rentang waktu terpilih') }}</span>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs flex items-center gap-4 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-primary-soft/10 rounded-full"></div>
                <div class="w-12 h-12 bg-primary-soft/40 text-accent rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-text-muted t-size2 font-semibold block uppercase tracking-wider">{{ __('Pesanan Selesai') }}</span>
                    <span class="text-text font-black t-size7 block mt-1">{{ number_format($totalOrders, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-text-muted/60 mt-0.5 block">*{{ __('Transaksi berhasil diselesaikan') }}</span>
                </div>
            </div>

            <!-- Average Transaction -->
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs flex items-center gap-4 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-info/5 rounded-full"></div>
                <div class="w-12 h-12 bg-info/15 text-info rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-text-muted t-size2 font-semibold block uppercase tracking-wider">{{ __('Rata-rata Pendapatan / Order') }}</span>
                    <span class="text-text font-black t-size7 block mt-1">Rp {{ number_format($totalOrders > 0 ? $totalRevenue / $totalOrders : 0, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-text-muted/60 mt-0.5 block">*{{ __('Total pendapatan dibagi total pesanan') }}</span>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="font-bold t-size5 text-text font-heading">
                        {{ __('Filter & Ekspor Laporan') }}
                    </h3>
                    <p class="text-text-muted t-size3 mt-0.5">
                        {{ __('Tentukan rentang tanggal dan cabang untuk menyaring data penjualan.') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="bg-success hover:bg-success/90 text-white font-bold px-4 py-2.5 rounded-xl transition shadow-xs cursor-pointer flex items-center gap-2 t-size3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('Ekspor Excel') }}
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.reports.sales') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-2">
                <!-- Branch Filter (Only for Super Admin) -->
                @if(auth()->user()->branch_id === null)
                    <div class="space-y-1">
                        <label for="branch_id" class="block t-size2 font-semibold text-text-muted">{{ __('Cabang') }}</label>
                        <select name="branch_id" id="branch_id" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2 t-size4 outline-hidden transition cursor-pointer">
                            <option value="">{{ __('Semua Cabang') }}</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
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

                <!-- Action Button -->
                <div class="flex items-end gap-2 md:col-span-1">
                    <button type="submit" class="w-full bg-primary hover:bg-primary-strong text-white font-bold px-6 py-2.5 rounded-xl transition shadow-xs cursor-pointer text-center">
                        {{ __('Terapkan') }}
                    </button>
                    @if(request('branch_id') || request('start_date') || request('end_date'))
                        <a href="{{ route('admin.reports.sales') }}" class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-4 py-2.5 rounded-xl transition cursor-pointer text-center font-bold flex items-center justify-center">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Sales Data Table -->
        <div class="bg-card border border-border rounded-2xl shadow-xs overflow-hidden">
            @if($salesData->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 bg-surface-alt text-text-muted/60 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold t-size4 text-text">{{ __('Tidak Ada Data Penjualan') }}</h3>
                        <p class="text-text-muted t-size2 max-w-sm mx-auto">
                            {{ __('Belum ada transaksi selesai pada rentang waktu atau cabang yang dipilih.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                @if(auth()->user()->branch_id === null && !request('branch_id'))
                                    <th class="py-4 px-6">{{ __('Cabang') }}</th>
                                @endif
                                <th class="py-4 px-6">{{ __('Tanggal') }}</th>
                                <th class="py-4 px-6">{{ __('Total Pesanan Selesai') }}</th>
                                <th class="py-4 px-6">{{ __('Total Pendapatan') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($salesData as $row)
                                <tr class="hover:bg-surface/30 transition">
                                    @if(auth()->user()->branch_id === null && !request('branch_id'))
                                        <td class="py-4 px-6">
                                            <span class="bg-surface border border-border text-text-muted px-2.5 py-0.5 rounded-lg t-size2 font-semibold">
                                                {{ $row->branch->name ?? '-' }}
                                            </span>
                                        </td>
                                    @endif
                                    <td class="py-4 px-6 font-bold text-text t-size3">
                                        {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                                    </td>
                                    <td class="py-4 px-6 text-text t-size3">
                                        {{ $row->total_orders }}
                                    </td>
                                    <td class="py-4 px-6 font-extrabold text-success t-size3">
                                        Rp {{ number_format($row->total_revenue, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
