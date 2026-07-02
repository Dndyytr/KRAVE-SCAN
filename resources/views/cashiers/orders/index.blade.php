<x-app-layout>
    <div class="anim-fade space-y-6" x-data="{
        init() {
            setInterval(() => window.location.reload(), 15000);
        }
    }">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="t-size4 font-bold text-primary-strong">Kasir</p>
                <h1 class="mt-1 font-heading t-size8 font-extrabold tracking-tight text-text">Kelola Pesanan</h1>
                <p class="mt-2 max-w-2xl t-size3 leading-7 text-text-muted">Pantau pesanan, item, status proses, dan waktu masuk secara real-time.</p>
            </div>
            <div class="inline-flex min-h-12 items-center gap-3 rounded-xl border border-border bg-card px-4 shadow-sm">
                <svg viewBox="0 0 24 24" class="h-5 w-5 text-primary-strong" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="5" width="18" height="16" rx="2" />
                    <path d="M16 3v4M8 3v4M3 10h18" />
                </svg>
                <span class="t-size3 font-bold text-text">{{ now()->translatedFormat('d M Y') }}</span>
            </div>
        </header>

        <section class="rounded-2xl border border-border bg-card p-4 shadow-[0_10px_30px_var(--color-shadow)] md:p-5" aria-label="Filter pesanan">
            <form method="GET" action="{{ route('cashier.orders') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_1fr_auto]">
                    <label>
                        <span class="mb-1.5 block t-size2 font-bold text-text-muted">Tanggal awal</span>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="h-12 w-full rounded-xl border-border bg-card px-4 t-size3 font-semibold text-text focus:border-primary focus:ring-primary">
                    </label>
                    <label>
                        <span class="mb-1.5 block t-size2 font-bold text-text-muted">Tanggal akhir</span>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="h-12 w-full rounded-xl border-border bg-card px-4 t-size3 font-semibold text-text focus:border-primary focus:ring-primary">
                    </label>
                    <div class="flex gap-2 md:self-end">
                        @if ($currentStatus)
                            <input type="hidden" name="status" value="{{ $currentStatus }}">
                        @endif
                        <button type="submit"
                            class="min-h-12 flex-1 rounded-xl bg-primary px-5 t-size3 font-bold text-white hover:bg-primary-strong md:flex-none">Terapkan</button>
                        @if ($startDate || $endDate)
                            <a href="{{ route('cashier.orders', array_filter(['status' => $currentStatus])) }}"
                                class="inline-flex min-h-12 items-center justify-center rounded-xl border border-border bg-card px-4 t-size3 font-bold text-text-muted hover:bg-surface hover:text-text">Reset</a>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                    @php
                        $statusFilters = [
                            '' => 'Semua Aktif',
                            'confirmed' => 'Dikonfirmasi',
                            'in_process' => 'Diproses',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                        ];
                    @endphp
                    @foreach ($statusFilters as $status => $label)
                        <a href="{{ route('cashier.orders', array_filter(['status' => $status, 'start_date' => $startDate, 'end_date' => $endDate], fn($value) => $value !== null && $value !== '')) }}"
                            class="rounded-xl border px-4 py-2 t-size3 font-bold transition {{ ($status === '' && is_null($currentStatus)) || $currentStatus === $status ? 'border-primary bg-primary text-white' : 'border-border bg-surface text-text-muted hover:border-primary-soft hover:text-text' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                    <span class="ml-auto inline-flex items-center gap-2 rounded-xl bg-surface px-3 py-2 t-size2 font-semibold text-text-muted">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-success"></span>Auto-refresh 15 detik
                    </span>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-border bg-card shadow-[0_10px_32px_var(--color-shadow)]" aria-labelledby="orders-table-title">
            <div class="flex items-center justify-between border-b border-border px-5 py-4 md:px-6">
                <div>
                    <h2 id="orders-table-title" class="font-heading t-size6 font-bold text-text">Data Pesanan</h2>
                    <p class="mt-1 t-size3 text-text-muted">{{ $orders->total() }} pesanan ditemukan</p>
                </div>
            </div>

            @if ($orders->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-surface text-primary-strong">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M6 3h12v18H6zM9 7h6M9 11h6M9 15h4" />
                        </svg>
                    </div>
                    <p class="mt-3 t-size5 font-bold text-text">Pesanan tidak ditemukan</p>
                    <p class="mt-1 t-size3 text-text-muted">Coba ubah tanggal atau status pesanan.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1180px] border-separate border-spacing-0 text-left">
                        <thead class="bg-surface-alt">
                            <tr>
                                <th class="border-b border-border px-6 py-4 t-size3 font-bold text-text">Pesanan</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Pelanggan</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Meja</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Item Pesanan</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Total</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Status</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Waktu</th>
                                <th class="border-b border-border px-6 py-4 text-right t-size3 font-bold text-text">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($orders as $order)
                                <tr class="transition-colors duration-200 hover:bg-surface/60">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-soft/30 text-primary-strong">
                                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M6 3h12v18H6zM9 7h6M9 11h6" />
                                                </svg>
                                            </span>
                                            <span class="whitespace-nowrap t-size3 font-extrabold text-text">#{{ $order->id }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="t-size3 font-bold text-text">{{ $order->customer_name ?? 'Pelanggan umum' }}</p>
                                        <p class="mt-1 t-size2 text-text-muted">{{ $order->customer_contact ?? 'Kontak tidak tersedia' }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full border border-primary-soft bg-primary-soft/30 px-3 py-1.5 t-size3 font-bold text-accent">Meja
                                            {{ $order->table_number }}</span>
                                    </td>
                                    <td class="max-w-sm px-5 py-4">
                                        <p class="line-clamp-2 t-size3 leading-6 text-text-muted"
                                            title="@foreach ($order->orderItems as $item){{ $item->menu?->name }} ({{ $item->quantity }}){{ !$loop->last ? ', ' : '' }} @endforeach">
                                            @foreach ($order->orderItems as $item)
                                                <span class="font-semibold text-text">{{ $item->menu?->name ?? 'Menu dihapus' }}</span>
                                                <span class="font-bold text-primary-strong">({{ $item->quantity }})</span>{{ !$loop->last ? ',' : '' }}
                                            @endforeach
                                        </p>
                                    </td>
                                    <td class="px-5 py-4 t-size4 font-extrabold text-primary-strong">Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-4"><x-status-badge :status="$order->status" /></td>
                                    <td class="px-5 py-4">
                                        <p class="t-size3 font-bold text-text">{{ $order->created_at->format('H:i') }} WIB</p>
                                        <p class="mt-1 t-size2 text-text-muted">{{ $order->created_at->translatedFormat('d M Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('cashier.orders.show', $order) }}" title="Lihat detail"
                                            aria-label="Lihat pesanan {{ $order->id }}"
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-primary bg-card text-primary-strong hover:bg-primary-soft/20">
                                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" />
                                                <circle cx="12" cy="12" r="2.5" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <footer class="flex flex-col gap-3 border-t border-border bg-bg/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between md:px-6">
                    <p class="t-size3 font-medium text-text-muted">Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari
                        {{ $orders->total() }} pesanan</p>
                    {{ $orders->links() }}
                </footer>
            @endif
        </section>
    </div>
</x-app-layout>
