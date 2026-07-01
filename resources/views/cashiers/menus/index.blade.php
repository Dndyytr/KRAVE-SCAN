<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold t-size8 font-heading text-text flex items-center gap-2">
                    <span class="w-10 h-10 rounded-xl bg-primary-soft/40 flex items-center justify-center text-accent t-size5">🍔</span>
                    {{ __('Kelola Menu Makanan') }}
                </h2>
                <p class="text-text-muted t-size3 mt-1">Kelola data menu masakan, kategori, harga, dan ketersediaan menu secara global.</p>
            </div>
            <a href="{{ route('cashier.menus.create') }}"
                class="bg-primary hover:bg-primary-strong text-white font-extrabold px-6 py-3 rounded-full transition shadow-xs flex items-center justify-center gap-2 cursor-pointer self-start sm:self-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Menu
            </a>
        </div>
    </x-slot>

    <div x-data="{
        showDeleteModal: false,
        deleteRoute: '',
        confirmDelete(route) {
            this.deleteRoute = route;
            this.showDeleteModal = true;
        }
    }" class="space-y-6">
        <div class="space-y-6 anim-fade">
            @if (session('success'))
                <div class="bg-success/10 border border-success/30 text-success p-4 rounded-2xl t-size3 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-danger/10 border border-danger/30 text-danger p-4 rounded-2xl t-size3 font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter & Search Panel -->
            <div class="bg-card border border-border rounded-3xl p-6 shadow-xs space-y-4">
                <h3 class="font-bold t-size5 text-text font-heading">
                    Filter & Pencarian Menu
                </h3>
                <form method="GET" action="{{ route('cashier.menus.index') }}" class="flex flex-col md:flex-row gap-3">
                    <div class="relative flex-grow">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-text-muted">🔍</span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu atau deskripsi..."
                            class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl pl-10 pr-4 py-2.5 t-size4 outline-hidden transition">
                    </div>
                    <div class="w-full md:w-60">
                        <select name="category_id"
                            class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3.5 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto shrink-0">
                        <button type="submit"
                            class="flex-grow md:flex-grow-0 bg-primary hover:bg-primary-strong text-white font-bold px-8 py-2.5 rounded-xl transition shadow-xs cursor-pointer">
                            Filter
                        </button>
                        @if (request()->anyFilled(['search', 'category_id']))
                            <a href="{{ route('cashier.menus.index') }}"
                                class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-6 py-2.5 rounded-xl transition cursor-pointer text-center flex items-center justify-center font-bold">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Menu Table List -->
            <div class="bg-card border border-border rounded-3xl overflow-hidden shadow-xs">
                @if ($menus->isEmpty())
                    <div class="p-12 text-center space-y-3">
                        <div class="w-16 h-16 bg-surface-alt text-text-muted/60 rounded-full flex items-center justify-center mx-auto text-2xl">
                            🍔
                        </div>
                        <h3 class="font-bold t-size4 text-text">{{ __('Menu Tidak Ditemukan') }}</h3>
                        <p class="text-text-muted t-size2 max-w-sm mx-auto">
                            Tidak ada menu masakan yang sesuai dengan kata kunci pencarian atau kategori terpilih.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                    <th class="py-4 px-6 w-24">Gambar</th>
                                    <th class="py-4 px-6">Nama Menu</th>
                                    <th class="py-4 px-6">Kategori</th>
                                    <th class="py-4 px-6">Harga</th>
                                    <th class="py-4 px-6 w-40">Status</th>
                                    <th class="py-4 px-6 text-right w-44">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach ($menus as $menu)
                                    <tr class="hover:bg-surface/30 transition">
                                        <!-- Image Thumbnail -->
                                        <td class="py-4 px-6">
                                            <div
                                                class="w-14 h-14 bg-surface-alt border border-border rounded-xl flex items-center justify-center overflow-hidden shrink-0">
                                                @if ($menu->image_path)
                                                    <img src="{{ Str::startsWith($menu->image_path, ['http://', 'https://']) ? $menu->image_path : (Str::startsWith($menu->image_path, 'storage/') ? asset($menu->image_path) : asset('storage/' . $menu->image_path)) }}"
                                                        alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <svg class="w-6 h-6 text-text-muted/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                @endif
                                            </div>
                                        </td>
                                        <!-- Name & Description -->
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-text t-size3.5">{{ $menu->name }}</div>
                                            <div class="text-text-muted t-size2 line-clamp-1 mt-0.5" title="{{ $menu->description }}">
                                                {{ $menu->description ?: 'Tidak ada deskripsi.' }}
                                            </div>
                                        </td>
                                        <!-- Category badge -->
                                        <td class="py-4 px-6">
                                            <span
                                                class="bg-primary-soft/40 text-accent font-extrabold px-3 py-1 rounded-full t-size1 border border-primary-soft/50">
                                                {{ $menu->category->name }}
                                            </span>
                                        </td>
                                        <!-- Price -->
                                        <td class="py-4 px-6 font-extrabold text-accent t-size3.5">
                                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                                        </td>
                                        <!-- Availability toggle -->
                                        <td class="py-4 px-6">
                                            <div x-data="{
                                                isActive: {{ $menu->is_active ? 'true' : 'false' }},
                                                toggle() {
                                                    fetch('{{ route('cashier.menus.toggle-active', $menu->id) }}', {
                                                            method: 'PATCH',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                            }
                                                        })
                                                        .then(res => res.json())
                                                        .then(data => {
                                                            if (data.success) {
                                                                this.isActive = data.is_active;
                                                            } else {
                                                                alert('Gagal memperbarui status');
                                                            }
                                                        })
                                                        .catch(err => {
                                                            console.error(err);
                                                            alert('Terjadi kesalahan koneksi.');
                                                        });
                                                }
                                            }">
                                                <button @click="toggle()"
                                                    :class="isActive ?
                                                        'bg-success/15 text-success border-success/35 hover:bg-success/25' :
                                                        'bg-danger/15 text-danger border-danger/35 hover:bg-danger/25'"
                                                    class="px-3.5 py-1 rounded-full t-size2 font-bold border transition cursor-pointer">
                                                    <span x-text="isActive ? 'Tersedia' : 'Nonaktif'"></span>
                                                </button>
                                            </div>
                                        </td>
                                        <!-- Actions -->
                                        <td class="py-4 px-6 text-right">
                                            <div class="flex justify-end gap-3.5">
                                                <a href="{{ route('cashier.menus.edit', $menu->id) }}"
                                                    class="bg-surface border border-border text-text hover:bg-surface-alt font-bold px-4 py-2 rounded-xl t-size2 transition">
                                                    Edit
                                                </a>
                                                <button @click="confirmDelete('{{ route('cashier.menus.destroy', $menu->id) }}')"
                                                    class="bg-danger/10 hover:bg-danger/20 text-danger border border-danger/30 font-bold px-4 py-2 rounded-xl t-size2 transition cursor-pointer">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination block -->
                    @if ($menus->hasPages())
                        <div class="px-6 py-4 border-t border-border bg-surface-alt">
                            {{ $menus->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Deletion confirmation modal -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-52 flex items-center justify-center bg-black/40 px-4" style="display: none;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="bg-card border border-border rounded-2xl max-w-md w-full p-6 shadow-lg space-y-4" @click.away="showDeleteModal = false">
                <h3 class="font-bold t-size5 text-text font-heading flex items-center gap-2">
                    ⚠️ Konfirmasi Hapus
                </h3>
                <p class="text-text-muted t-size3 leading-relaxed">
                    Apakah Anda yakin ingin menghapus menu ini? Tindakan ini tidak dapat dibatalkan dan menu akan dihapus secara permanen dari sistem.
                </p>
                <div class="flex justify-end gap-3 pt-2">
                    <button @click="showDeleteModal = false"
                        class="bg-surface border border-border text-text-muted hover:text-text px-5 py-2 rounded-xl transition cursor-pointer font-bold t-size4">
                        Batal
                    </button>
                    <form :action="deleteRoute" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-danger hover:bg-danger/90 text-white font-extrabold px-6 py-2 rounded-xl transition cursor-pointer t-size4">
                            Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
