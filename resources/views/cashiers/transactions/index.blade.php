<x-app-layout>
    <div class="anim-fade space-y-6" x-data="{
        init() {
            setInterval(() => window.location.reload(), 15000);
        }
    }">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="t-size4 font-bold text-primary-strong">Kasir</p>
                <h1 class="mt-1 font-heading t-size8 font-extrabold tracking-tight text-text">Kelola Transaksi</h1>
                <p class="mt-2 max-w-2xl t-size3 leading-7 text-text-muted">Proses pesanan yang masih menunggu pembayaran.</p>
            </div>
            <div class="inline-flex min-h-12 items-center gap-3 rounded-xl border border-border bg-card px-4 shadow-sm">
                <svg viewBox="0 0 24 24" class="h-5 w-5 text-primary-strong" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="5" width="18" height="16" rx="2" />
                    <path d="M16 3v4M8 3v4M3 10h18" />
                </svg>
                <span class="t-size3 font-bold text-text">{{ now()->translatedFormat('d M Y') }}</span>
            </div>
        </header>

        <section class="rounded-2xl border border-border bg-card p-4 shadow-[0_10px_30px_var(--color-shadow)] md:p-5" aria-label="Filter transaksi">
            <form method="GET" action="{{ route('cashier.transactions') }}" class="flex flex-col gap-3 md:flex-row md:items-center">
                <div class="grid flex-1 grid-cols-1 gap-3 bp400:grid-cols-2">
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
                </div>
                <div class="flex gap-2 md:self-end">
                    <button type="submit"
                        class="min-h-12 flex-1 rounded-xl bg-primary px-5 t-size3 font-bold text-white hover:bg-primary-strong md:flex-none">Terapkan</button>
                    @if ($startDate || $endDate)
                        <a href="{{ route('cashier.transactions') }}"
                            class="inline-flex min-h-12 items-center justify-center rounded-xl border border-border bg-card px-4 t-size3 font-bold text-text-muted hover:bg-surface hover:text-text">Reset</a>
                    @endif
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-border bg-card shadow-[0_10px_32px_var(--color-shadow)]"
            aria-labelledby="transactions-table-title">
            <div class="flex flex-col gap-3 border-b border-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between md:px-6">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 id="transactions-table-title" class="font-heading t-size6 font-bold text-text">Transaksi Menunggu</h2>
                        <span class="rounded-full bg-warning-soft px-2.5 py-1 t-size2 font-bold text-amber-700">{{ $orders->total() }}</span>
                    </div>
                    <p class="mt-1 t-size3 text-text-muted">Pesanan berstatus pending yang belum dibayar</p>
                </div>
                <span class="inline-flex items-center gap-2 rounded-xl bg-surface px-3 py-2 t-size2 font-semibold text-text-muted">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-success"></span>Auto-refresh 15 detik
                </span>
            </div>

            @if ($orders->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-success-soft text-green-700">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 12.5 9 16l10-10" />
                        </svg>
                    </div>
                    <p class="mt-3 t-size5 font-bold text-text">Tidak ada transaksi pending</p>
                    <p class="mt-1 t-size3 text-text-muted">Semua transaksi sudah diproses.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1080px] border-separate border-spacing-0 text-left">
                        <thead class="bg-surface-alt">
                            <tr>
                                <th class="border-b border-border px-6 py-4 t-size3 font-bold text-text">No. Transaksi</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Waktu</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Pelanggan</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Pembayaran</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Total</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Status</th>
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
                                                    <path d="M6 3h12v18H6zM9 7h6M9 11h6M9 15h4" />
                                                </svg>
                                            </span>
                                            <span
                                                class="whitespace-nowrap t-size3 font-extrabold text-primary-strong">TRX-{{ $order->created_at->format('dmy') }}-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="t-size3 font-bold text-text">{{ $order->created_at->format('H:i') }} WIB</p>
                                        <p class="mt-1 t-size2 text-text-muted">{{ $order->created_at->translatedFormat('d M Y') }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="t-size3 font-bold text-text">
                                            {{ $order->table_number ? 'Dine In · Meja ' . $order->table_number : 'Take Away' }}</p>
                                        <p class="mt-1 t-size2 text-text-muted">{{ $order->customer_name ?? 'Pelanggan umum' }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="t-size3 font-semibold text-text">Belum dipilih</p>
                                        <p class="mt-1 t-size2 text-text-muted">QRIS atau tunai</p>
                                    </td>
                                    <td class="px-5 py-4 t-size4 font-extrabold text-primary-strong">Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-4"><x-status-badge status="pending" class="t-size3" /></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('cashier.transactions.show', $order) }}" title="Proses pembayaran"
                                            aria-label="Proses transaksi {{ $order->id }}"
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
                        {{ $orders->total() }} transaksi</p>
                    {{ $orders->links() }}
                </footer>
            @endif
        </section>
    </div>
</x-app-layout>
