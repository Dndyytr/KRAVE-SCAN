<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold t-size8 font-heading text-text">
                    {{ __('Laporan Metode Pembayaran 💖') }}
                </h2>
                <p class="text-text-muted t-size3 mt-1">
                    {{ __('Analisis proporsi penggunaan metode pembayaran (Cash vs QRIS) berdasarkan transaksi sukses.') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <div class="hidden sm:block">
                    <div class="bg-card border border-border px-4 py-2 rounded-2xl flex items-center gap-2 t-size2 font-semibold text-text">
                        <span class="text-primary">📅</span>
                        {{ now()->translatedFormat('l, d M Y') }}
                    </div>
                </div>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}"
                    class="bg-primary hover:bg-primary-strong text-white font-bold px-4 py-2.5 rounded-xl transition shadow-xs cursor-pointer flex items-center gap-2 t-size3">
                    📤 {{ __('Export Report') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 anim-fade">

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2 border-b border-border pb-px">
            <a href="{{ route('admin.reports.sales', ['type' => 'sales', 'branch_id' => request('branch_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                class="px-5 py-3 font-bold t-size3 transition-all border-b-2 border-transparent text-text-muted hover:text-text">
                📈 Sales Report
            </a>
            <a href="{{ route('admin.reports.sales', ['type' => 'financial', 'branch_id' => request('branch_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                class="px-5 py-3 font-bold t-size3 transition-all border-b-2 border-transparent text-text-muted hover:text-text">
                💵 Financial Report
            </a>
            <a href="{{ route('admin.reports.menus', ['branch_id' => request('branch_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                class="px-5 py-3 font-bold t-size3 transition-all border-b-2 border-transparent text-text-muted hover:text-text">
                🍔 Performa Menu
            </a>
            <a href="{{ route('admin.reports.payments', ['branch_id' => request('branch_id'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                class="px-5 py-3 font-bold t-size3 transition-all border-b-2 border-primary text-accent bg-primary-soft/10 rounded-t-xl">
                💳 Metode Pembayaran
            </a>
        </div>

        <!-- Filters Row -->
        <div class="bg-card border border-border rounded-2xl p-4 shadow-xs">
            <form method="GET" action="{{ route('admin.reports.payments') }}" class="flex flex-wrap items-center gap-3">

                <!-- Date Range picker -->
                <div class="flex items-center bg-card border border-border rounded-xl px-3 py-1.5 gap-2 shrink-0">
                    <span class="text-text-muted">📅</span>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                        class="bg-transparent border-0 p-0 text-text font-semibold t-size3 focus:ring-0 focus:outline-none">
                    <span class="text-text-muted font-bold t-size2">-</span>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                        class="bg-transparent border-0 p-0 text-text font-semibold t-size3 focus:ring-0 focus:outline-none">
                </div>

                <!-- Outlet Dropdown (Only for Super Admin) -->
                @if ($isSuperAdmin)
                    <select name="branch_id"
                        class="bg-card border border-border rounded-xl px-3 py-2 t-size3 font-semibold text-text focus:ring-primary focus:border-primary cursor-pointer">
                        <option value="">Semua Outlet</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <button type="submit"
                    class="bg-card border border-primary hover:bg-primary-soft/20 text-accent font-bold px-5 py-2 rounded-xl transition t-size3 cursor-pointer">
                    🔍 Filter
                </button>
                @if (request('branch_id') || request('start_date') || request('end_date'))
                    <a href="{{ route('admin.reports.payments') }}"
                        class="bg-surface border border-border text-text-muted hover:text-text px-4 py-2 rounded-xl transition t-size3 font-semibold">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Payment Methods Table -->
        <div class="bg-card border border-border rounded-3xl overflow-hidden shadow-xs">
            @if ($paymentReport->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 bg-surface-alt text-text-muted/60 rounded-full flex items-center justify-center mx-auto">
                        💳
                    </div>
                    <div class="space-y-1">
                        <h3 class="font-bold t-size4 text-text">{{ __('Tidak Ada Data Metode Pembayaran') }}</h3>
                        <p class="text-text-muted t-size2 max-w-sm mx-auto">
                            {{ __('Belum ada pembayaran sukses pada rentang waktu atau cabang yang dipilih.') }}
                        </p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                @if ($isSuperAdmin && !request('branch_id'))
                                    <th class="py-4 px-6">{{ __('Cabang') }}</th>
                                @endif
                                <th class="py-4 px-6">{{ __('Metode Pembayaran') }}</th>
                                <th class="py-4 px-6 text-center">{{ __('Jumlah Transaksi Sukses') }}</th>
                                <th class="py-4 px-6 text-right">{{ __('Total Pendapatan') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($paymentReport as $row)
                                <tr class="hover:bg-surface/30 transition">
                                    @if ($isSuperAdmin && !request('branch_id'))
                                        <td class="py-4 px-6">
                                            <span class="bg-surface border border-border text-text-muted px-2.5 py-0.5 rounded-lg t-size2 font-semibold">
                                                {{ $row->order->branch->name ?? '-' }}
                                            </span>
                                        </td>
                                    @endif
                                    <td class="py-4 px-6 font-bold text-text t-size3">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full t-size2 font-bold border
                                            @if ($row->method === 'cash') bg-primary-soft/40 text-accent border-primary-soft
                                            @else bg-info/15 text-info border-info/30 @endif">
                                            @if ($row->method === 'cash')
                                                💵 Cash
                                            @else
                                                📱 QRIS
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center text-text font-semibold t-size3">
                                        {{ $row->total_transactions }}
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
