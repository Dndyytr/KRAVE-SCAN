<x-app-layout>
    <div class="anim-fade space-y-6" x-data="{
        showDeleteModal: false,
        deleteRoute: '',
        confirmDelete(route) {
            this.deleteRoute = route;
            this.showDeleteModal = true;
        }
    }">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="t-size4 font-bold text-primary-strong">Manajemen Menu</p>
                <h1 class="mt-1 font-heading t-size8 font-extrabold tracking-tight text-text">Daftar Menu</h1>
                <p class="mt-2 max-w-2xl t-size3 leading-7 text-text-muted">Kelola informasi menu, kategori, harga, gambar, dan ketersediaannya.</p>
            </div>
            <a href="{{ route('cashier.menus.create') }}"
                class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 t-size3 font-bold text-white shadow-sm hover:bg-primary-strong">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Tambah Menu
            </a>
        </header>

        <section class="rounded-2xl border border-border bg-card p-4 shadow-[0_10px_30px_var(--color-shadow)] md:p-5" aria-label="Filter menu">
            <form method="GET" action="{{ route('cashier.menus.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(280px,1fr)_240px_auto]">
                <label class="relative block">
                    <span class="sr-only">Cari menu</span>
                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-text-muted" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7" />
                        <path d="m20 20-3.5-3.5" />
                    </svg>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama atau deskripsi menu..."
                        class="h-12 w-full rounded-xl border-border bg-card pl-12 pr-4 t-size3 font-medium text-text placeholder:text-text-muted/70 focus:border-primary focus:ring-primary">
                </label>
                <label>
                    <span class="sr-only">Filter kategori</span>
                    <select name="category_id"
                        class="h-12 w-full rounded-xl border-border bg-card px-4 t-size3 font-semibold text-text focus:border-primary focus:ring-primary">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="flex gap-2">
                    <button type="submit"
                        class="min-h-12 flex-1 rounded-xl bg-primary px-5 t-size3 font-bold text-white hover:bg-primary-strong md:flex-none">Terapkan</button>
                    @if (request()->anyFilled(['search', 'category_id']))
                        <a href="{{ route('cashier.menus.index') }}"
                            class="inline-flex min-h-12 items-center justify-center rounded-xl border border-border bg-card px-4 t-size3 font-bold text-text-muted hover:bg-surface hover:text-text">Reset</a>
                    @endif
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-border bg-card shadow-[0_10px_32px_var(--color-shadow)]" aria-labelledby="menus-table-title">
            <div class="flex items-center justify-between border-b border-border px-5 py-4 md:px-6">
                <div>
                    <h2 id="menus-table-title" class="font-heading t-size6 font-bold text-text">Data Menu</h2>
                    <p class="mt-1 t-size3 text-text-muted">{{ $menus->total() }} menu ditemukan</p>
                </div>
            </div>

            @if ($menus->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-surface text-primary-strong">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 10h16M5 10l1 10h12l1-10M8 10V7a4 4 0 0 1 8 0v3" />
                        </svg>
                    </div>
                    <p class="mt-3 t-size5 font-bold text-text">Menu tidak ditemukan</p>
                    <p class="mt-1 t-size3 text-text-muted">Coba ubah pencarian atau kategori.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[940px] border-separate border-spacing-0 text-left">
                        <thead class="bg-surface-alt">
                            <tr>
                                <th class="border-b border-border px-6 py-4 t-size3 font-bold text-text">Menu</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Kategori</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Harga</th>
                                <th class="border-b border-border px-5 py-4 t-size3 font-bold text-text">Ketersediaan</th>
                                <th class="border-b border-border px-6 py-4 text-right t-size3 font-bold text-text">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($menus as $menu)
                                <tr class="transition-colors duration-200 hover:bg-surface/60">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-border bg-surface">
                                                @if ($menu->image_path)
                                                    <img src="{{ Str::startsWith($menu->image_path, ['http://', 'https://']) ? $menu->image_path : (Str::startsWith($menu->image_path, 'storage/') ? asset($menu->image_path) : asset('storage/' . $menu->image_path)) }}"
                                                        alt="{{ $menu->name }}" class="h-full w-full object-cover">
                                                @else
                                                    <svg viewBox="0 0 24 24" class="h-6 w-6 text-text-muted/40" fill="none" stroke="currentColor"
                                                        stroke-width="1.8">
                                                        <path
                                                            d="m4 16 4-4 4 4 3-3 5 5M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                                                        <circle cx="9" cy="9" r="1" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="min-w-0 max-w-md">
                                                <p class="truncate t-size4 font-bold text-text">{{ $menu->name }}</p>
                                                <p class="mt-1 line-clamp-2 t-size2 leading-5 text-text-muted">
                                                    {{ $menu->description ?: 'Tidak ada deskripsi.' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full border border-primary-soft bg-primary-soft/30 px-3 py-1.5 t-size2 font-bold text-accent">{{ $menu->category?->name ?? 'Tanpa kategori' }}</span>
                                    </td>
                                    <td class="px-5 py-4 t-size4 font-extrabold text-primary-strong">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                                    <td class="px-5 py-4">
                                        <div x-data="{
                                            isActive: {{ $menu->is_active ? 'true' : 'false' }},
                                            toggle() {
                                                fetch('{{ route('cashier.menus.toggle-active', $menu) }}', {
                                                    method: 'PATCH',
                                                    headers: {
                                                        'Accept': 'application/json',
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    }
                                                }).then(response => response.json()).then(data => {
                                                    if (data.success) this.isActive = data.is_active;
                                                });
                                            }
                                        }">
                                            <button type="button" @click="toggle()"
                                                :class="isActive ? 'bg-success-soft text-green-700' : 'bg-danger-soft text-red-700'"
                                                class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 t-size3 font-bold">
                                                <span class="h-2 w-2 rounded-full" :class="isActive ? 'bg-success' : 'bg-danger'"></span>
                                                <span x-text="isActive ? 'Tersedia' : 'Nonaktif'"></span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('cashier.menus.edit', $menu) }}" title="Edit menu" aria-label="Edit {{ $menu->name }}"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-primary-soft bg-card text-primary-strong hover:bg-surface">
                                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Z" />
                                                    <path d="m14 7 3 3" />
                                                </svg>
                                            </a>
                                            <button type="button" @click="confirmDelete('{{ route('cashier.menus.destroy', $menu) }}')" title="Hapus menu"
                                                aria-label="Hapus {{ $menu->name }}"
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
                    <p class="t-size3 font-medium text-text-muted">Menampilkan {{ $menus->firstItem() }}–{{ $menus->lastItem() }} dari {{ $menus->total() }}
                        menu</p>
                    {{ $menus->links() }}
                </footer>
            @endif
        </section>

        <div x-cloak x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/35 px-4">
            <div class="w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-lg" @click.outside="showDeleteModal = false">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-danger-soft text-danger">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 9v4M12 17h.01" />
                        <path d="M10.3 3.8 2.7 17a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3L13.7 3.8a2 2 0 0 0-3.4 0Z" />
                    </svg>
                </div>
                <h3 class="mt-4 font-heading t-size6 font-bold text-text">Hapus menu?</h3>
                <p class="mt-2 t-size3 leading-7 text-text-muted">Menu akan dihapus dari daftar. Tindakan ini tidak dapat dibatalkan.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="min-h-11 rounded-xl border border-border px-5 t-size3 font-bold text-text-muted hover:bg-surface">Batal</button>
                    <form :action="deleteRoute" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="min-h-11 rounded-xl bg-danger px-5 t-size3 font-bold text-white hover:bg-red-600">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
