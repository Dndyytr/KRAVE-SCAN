<x-app-layout>
    <div x-data="{
        showDeleteModal: false,
        deleteRoute: '',
        confirmDelete(route) {
            this.deleteRoute = route;
            this.showDeleteModal = true;
        }
    }" class="space-y-6">
        <div class="anim-fade space-y-6">
            <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-base font-bold text-primary-strong">Manajemen Stok</p>
                    <h1 class="mt-1 font-heading text-2xl font-extrabold tracking-tight text-text md:text-3xl">Daftar Stok
                        Inventaris</h1>
                    <p class="mt-2 max-w-2xl text-base leading-7 text-text-muted">Pantau jumlah bahan baku, batas minimum,
                        dan kondisi persediaan cabang.</p>
                </div>
                <a href="{{ route('admin.stocks.create') }}"
                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-base font-bold text-white shadow-sm hover:bg-primary-strong">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Tambah Stok
                </a>
            </header>

            <section class="rounded-2xl border border-border bg-card p-4 shadow-[0_10px_30px_var(--color-shadow)] md:p-5" aria-label="Filter stok">
                <form method="GET" action="{{ route('admin.stocks.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(280px,1fr)_220px_auto]">
                    <label class="relative block">
                        <span class="sr-only">Cari stok</span>
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-text-muted" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m20 20-3.5-3.5" />
                        </svg>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama barang..."
                            class="h-12 w-full rounded-xl border-border bg-card pl-12 pr-4 text-base font-medium text-text placeholder:text-text-muted/70 focus:border-primary focus:ring-primary">
                    </label>
                    <label>
                        <span class="sr-only">Filter kondisi stok</span>
                        <select name="status"
                            class="h-12 w-full rounded-xl border-border bg-card px-4 text-base font-semibold text-text focus:border-primary focus:ring-primary">
                            <option value="">Semua Status</option>
                            <option value="low" @selected(request('status') === 'low')>Stok Menipis</option>
                            <option value="safe" @selected(request('status') === 'safe')>Stok Aman</option>
                        </select>
                    </label>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="min-h-12 flex-1 rounded-xl bg-primary px-5 text-base font-bold text-white hover:bg-primary-strong md:flex-none">Terapkan</button>
                        @if (request()->anyFilled(['search', 'status']))
                            <a href="{{ route('admin.stocks.index') }}"
                                class="inline-flex min-h-12 items-center justify-center rounded-xl border border-border bg-card px-4 text-base font-bold text-text-muted hover:bg-surface hover:text-text">Reset</a>
                        @endif
                    </div>
                </form>
            </section>

            <section class="overflow-hidden rounded-2xl border border-border bg-card shadow-[0_10px_32px_var(--color-shadow)]"
                aria-labelledby="stocks-table-title">
                <div class="flex items-center justify-between border-b border-border px-5 py-4 md:px-6">
                    <div>
                        <h2 id="stocks-table-title" class="font-heading text-xl font-bold text-text">Data Stok</h2>
                        <p class="mt-1 text-base text-text-muted">{{ $stocks->total() }} item inventaris ditemukan</p>
                    </div>
                </div>

                @if ($stocks->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-surface text-primary-strong">
                            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m3 7 9-4 9 4-9 4-9-4Z" />
                                <path d="m3 7 9 4 9-4v10l-9 4-9-4V7Z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-lg font-bold text-text">Stok tidak ditemukan</p>
                        <p class="mt-1 text-base text-text-muted">Coba ubah pencarian atau filter status.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[980px] border-separate border-spacing-0 text-left">
                            <thead class="bg-gradient-to-r from-bg to-surface">
                                <tr>
                                    <th class="border-b border-border px-6 py-4 text-base font-bold text-text">Barang</th>
                                    <th class="border-b border-border px-5 py-4 text-base font-bold text-text">Stok Saat Ini
                                    </th>
                                    <th class="border-b border-border px-5 py-4 text-base font-bold text-text">Batas Minimum
                                    </th>
                                    <th class="border-b border-border px-5 py-4 text-base font-bold text-text">Kondisi</th>
                                    <th class="border-b border-border px-5 py-4 text-base font-bold text-text">Diperbarui
                                    </th>
                                    <th class="border-b border-border px-6 py-4 text-right text-base font-bold text-text">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach ($stocks as $stock)
                                    @php($isLow = $stock->quantity <= $stock->minimum_quantity)
                                    <tr class="transition-colors duration-200 hover:bg-surface/60">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $isLow ? 'bg-danger-soft text-danger' : 'bg-success-soft text-green-700' }}">
                                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="m3 7 9-4 9 4-9 4-9-4Z" />
                                                        <path d="m3 7 9 4 9-4v10l-9 4-9-4V7Z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-base font-bold text-text">{{ $stock->name }}</p>
                                                    <p class="mt-1 text-sm text-text-muted">Satuan {{ $stock->unit }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span
                                                class="text-lg font-extrabold {{ $isLow ? 'text-danger' : 'text-text' }}">{{ number_format($stock->quantity, 0, ',', '.') }}</span>
                                            <span class="ml-1 text-base text-text-muted">{{ $stock->unit }}</span>
                                        </td>
                                        <td class="px-5 py-4 text-base font-semibold text-text-muted">
                                            {{ number_format($stock->minimum_quantity, 0, ',', '.') }} {{ $stock->unit }}
                                        </td>
                                        <td class="px-5 py-4"><x-status-badge :status="$isLow ? 'low_stock' : 'available'" class="!text-base" /></td>
                                        <td class="px-5 py-4">
                                            <p class="text-base font-semibold text-text">
                                                {{ $stock->updated_at->translatedFormat('d M Y') }}</p>
                                            <p class="mt-1 text-sm text-text-muted">
                                                {{ $stock->updated_at->diffForHumans() }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.stocks.edit', $stock) }}" title="Edit stok" aria-label="Edit {{ $stock->name }}"
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-primary-soft bg-card text-primary-strong hover:bg-surface">
                                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Z" />
                                                        <path d="m14 7 3 3" />
                                                    </svg>
                                                </a>
                                                <button type="button" @click="confirmDelete('{{ route('admin.stocks.destroy', $stock) }}')"
                                                    title="Hapus stok" aria-label="Hapus {{ $stock->name }}"
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-danger/40 bg-card text-danger hover:bg-danger-soft">
                                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M4 7h16M9 7V4h6v3M7 7l1 14h8l1-14M10 11v6M14 11v6" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <footer class="flex flex-col gap-3 border-t border-border bg-bg/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between md:px-6">
                        <p class="text-base font-medium text-text-muted">Menampilkan
                            {{ $stocks->firstItem() }}–{{ $stocks->lastItem() }} dari {{ $stocks->total() }} item</p>
                        {{ $stocks->links() }}
                    </footer>
                @endif
            </section>
        </div>

        <div x-cloak x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 z-52 flex items-center justify-center bg-black/35 px-4">
            <div class="w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-lg" @click.outside="showDeleteModal = false">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-danger-soft text-danger">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 9v4M12 17h.01" />
                        <path d="M10.3 3.8 2.7 17a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3L13.7 3.8a2 2 0 0 0-3.4 0Z" />
                    </svg>
                </div>
                <h3 class="mt-4 font-heading text-xl font-bold text-text">Hapus stok?</h3>
                <p class="mt-2 text-base leading-7 text-text-muted">Item stok akan dihapus dan hubungan dengan menu
                    terkait dapat terputus.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="min-h-11 rounded-xl border border-border px-5 text-base font-bold text-text-muted hover:bg-surface">Batal</button>
                    <form :action="deleteRoute" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="min-h-11 rounded-xl bg-danger px-5 text-base font-bold text-white hover:bg-red-600">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
