<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Kelola Menu Makanan') }}
        </h2>
    </x-slot>

    <div class="space-y-6 anim-fade" x-data="{ 
        showDeleteModal: false, 
        deleteRoute: '',
        confirmDelete(route) {
            this.deleteRoute = route;
            this.showDeleteModal = true;
        }
    }">
        <!-- Top Actions / Filter Panel -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="font-bold t-size5 text-text font-heading">
                        Daftar Menu Krave Scan
                    </h3>
                    <p class="text-text-muted t-size3 mt-0.5">
                        Kelola data menu masakan, kategori, harga, dan ketersediaan menu secara global.
                    </p>
                </div>
                <a href="{{ route('admin.menus.create') }}" class="bg-primary hover:bg-primary-strong text-white font-bold px-5 py-2.5 rounded-xl transition shadow-xs flex items-center gap-2 shrink-0 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Menu
                </a>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.menus.index') }}" class="flex flex-col md:flex-row gap-3 pt-2">
                <div class="relative flex-grow">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu atau deskripsi..." class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2 t-size4 outline-hidden transition">
                </div>
                <div class="w-full md:w-56">
                    <select name="category_id" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2 w-full md:w-auto shrink-0">
                    <button type="submit" class="flex-grow md:flex-grow-0 bg-primary hover:bg-primary-strong text-white font-bold px-6 py-2.5 rounded-xl transition shadow-xs cursor-pointer">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'category_id']))
                        <a href="{{ route('admin.menus.index') }}" class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-4 py-2.5 rounded-xl transition cursor-pointer text-center flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Success Toast Alert -->
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-success/15 border border-success/30 text-success p-4 rounded-xl flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold t-size3">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-success hover:text-success/80">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Menu Table List -->
        <div class="bg-card border border-border rounded-2xl shadow-xs overflow-hidden">
            @if($menus->isEmpty())
                <div class="py-12 text-center text-text-muted t-size4 font-semibold">
                    Tidak ada menu masakan yang ditemukan.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                <th class="py-3 px-6 w-20">Gambar</th>
                                <th class="py-3 px-6">Nama Menu</th>
                                <th class="py-3 px-6">Kategori</th>
                                <th class="py-3 px-6">Harga</th>
                                <th class="py-3 px-6 w-32">Status Ketersediaan</th>
                                <th class="py-3 px-6 text-right w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($menus as $menu)
                                <tr class="hover:bg-surface/30 transition">
                                    <!-- Image Thumbnail -->
                                    <td class="py-4 px-6">
                                        <div class="w-14 h-14 bg-surface-alt border border-border rounded-xl flex items-center justify-center overflow-hidden shrink-0">
                                            @if($menu->image_path)
                                                <img src="{{ asset($menu->image_path) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-text-muted/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Name & Description -->
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-text t-size4">{{ $menu->name }}</div>
                                        <div class="text-text-muted t-size2 line-clamp-1 mt-0.5" title="{{ $menu->description }}">
                                            {{ $menu->description ?: 'Tidak ada deskripsi.' }}
                                        </div>
                                    </td>
                                    <!-- Category badge -->
                                    <td class="py-4 px-6">
                                        <span class="bg-primary-soft/40 text-accent font-bold px-2.5 py-0.5 rounded-full t-size1 border border-primary-soft/50">
                                            {{ $menu->category->name }}
                                        </span>
                                    </td>
                                    <!-- Price -->
                                    <td class="py-4 px-6 font-bold text-accent t-size4">
                                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                                    </td>
                                    <!-- Availability toggle -->
                                    <td class="py-4 px-6">
                                        <div x-data="{ 
                                            isActive: {{ $menu->is_active ? 'true' : 'false' }},
                                            toggle() {
                                                fetch('{{ route('admin.menus.toggle-active', $menu->id) }}', {
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
                                                    :class="isActive ? 'bg-success/15 text-success border-success/35 hover:bg-success/25' : 'bg-danger/15 text-danger border-danger/35 hover:bg-danger/25'" 
                                                    class="px-3 py-1 rounded-full t-size2 font-bold border transition cursor-pointer">
                                                <span x-text="isActive ? 'Aktif' : 'Nonaktif'"></span>
                                            </button>
                                        </div>
                                    </td>
                                    <!-- Actions -->
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('admin.menus.edit', $menu->id) }}" class="text-accent hover:text-primary font-bold t-size3 transition">
                                                Edit
                                            </a>
                                            <button @click="confirmDelete('{{ route('admin.menus.destroy', $menu->id) }}')" class="text-danger hover:text-red-600 font-bold t-size3 transition cursor-pointer">
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
                <div class="px-6 py-4 border-t border-border">
                    {{ $menus->links() }}
                </div>
            @endif
        </div>

        <!-- Deletion confirmation modal -->
        <div x-show="showDeleteModal" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="bg-card border border-border rounded-2xl max-w-md w-full p-6 shadow-lg space-y-4" @click.away="showDeleteModal = false">
                <h3 class="font-bold t-size5 text-text font-heading">
                    Konfirmasi Hapus Menu
                </h3>
                <p class="text-text-muted t-size3">
                    Apakah Anda yakin ingin menghapus menu ini dari database? Menu ini tidak akan tersedia lagi dan tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex justify-end gap-3 pt-2">
                    <button @click="showDeleteModal = false" class="bg-surface border border-border text-text-muted hover:text-text px-4 py-2 rounded-xl transition cursor-pointer font-semibold t-size4">
                        Batal
                    </button>
                    <form :action="deleteRoute" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-danger hover:bg-red-600 text-white font-bold px-4 py-2 rounded-xl transition cursor-pointer t-size4">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
