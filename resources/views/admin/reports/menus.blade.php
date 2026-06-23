<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Laporan Performa Menu') }}
        </h2>
    </x-slot>

    <div class="space-y-6 anim-fade">
        <!-- Filter & Ekspor Card -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="font-bold t-size5 text-text font-heading">
                        {{ __('Filter & Ekspor Performa Menu') }}
                    </h3>
                    <p class="text-text-muted t-size3 mt-0.5">
                        {{ __('Analisis menu makanan & minuman terlaris berdasarkan kuantitas terjual.') }}
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

            <form method="GET" action="{{ route('admin.reports.menus') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-2">
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
                        <a href="{{ route('admin.reports.menus') }}" class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-4 py-2.5 rounded-xl transition cursor-pointer text-center font-bold flex items-center justify-center">
                            {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Menu Performance Table -->
        <div class="bg-card border border-border rounded-2xl shadow-xs overflow-hidden">
            @if($menuPerformance->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 bg-surface-alt text-text-muted/60 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold t-size4 text-text">{{ __('Tidak Ada Data Performa Menu') }}</h3>
                        <p class="text-text-muted t-size2 max-w-sm mx-auto">
                            {{ __('Belum ada menu yang terjual pada rentang waktu atau cabang yang dipilih.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                @if($isSuperAdmin && !request('branch_id'))
                                    <th class="py-4 px-6">{{ __('Cabang') }}</th>
                                @endif
                                <th class="py-4 px-6">{{ __('Nama Menu') }}</th>
                                <th class="py-4 px-6">{{ __('Kategori') }}</th>
                                <th class="py-4 px-6 text-center">{{ __('Jumlah Terjual') }}</th>
                                <th class="py-4 px-6 text-right">{{ __('Total Penjualan') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($menuPerformance as $row)
                                <tr class="hover:bg-surface/30 transition">
                                    @if($isSuperAdmin && !request('branch_id'))
                                        <td class="py-4 px-6">
                                            <span class="bg-surface border border-border text-text-muted px-2.5 py-0.5 rounded-lg t-size2 font-semibold">
                                                {{ $row->order->branch->name ?? '-' }}
                                            </span>
                                        </td>
                                    @endif
                                    <td class="py-4 px-6 font-bold text-text t-size3">
                                        {{ $row->menu->name ?? '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-text-muted t-size2">
                                        <span class="bg-surface border border-border text-text-muted px-2.5 py-0.5 rounded-lg font-semibold">
                                            {{ $row->menu->category->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center text-text font-semibold t-size3">
                                        {{ $row->total_quantity }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-extrabold text-success t-size3">
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
