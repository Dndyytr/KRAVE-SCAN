<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Kelola Stok Barang') }}
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
                        Daftar Stok Inventaris
                    </h3>
                    <p class="text-text-muted t-size3 mt-0.5">
                        Kelola persediaan bahan baku dan barang siap jual untuk cabang ini secara real-time.
                    </p>
                </div>
                <a href="{{ route('admin.stocks.create') }}" class="bg-primary hover:bg-primary-strong text-white font-bold px-5 py-2.5 rounded-xl transition shadow-xs flex items-center gap-2 shrink-0 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Stok
                </a>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.stocks.index') }}" class="flex flex-col md:flex-row gap-3 pt-2">
                <div class="relative flex-grow">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang..." class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2 t-size4 outline-hidden transition">
                </div>
                <div class="w-full md:w-56">
                    <select name="status" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Stok Menipis</option>
                        <option value="safe" {{ request('status') === 'safe' ? 'selected' : '' }}>Stok Aman</option>
                    </select>
                </div>
                <div class="flex gap-2 w-full md:w-auto shrink-0">
                    <button type="submit" class="flex-grow md:flex-grow-0 bg-primary hover:bg-primary-strong text-white font-bold px-6 py-2.5 rounded-xl transition shadow-xs cursor-pointer">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('admin.stocks.index') }}" class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-4 py-2.5 rounded-xl transition cursor-pointer text-center flex items-center justify-center">
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

        <!-- Stock Table List -->
        <div class="bg-card border border-border rounded-2xl shadow-xs overflow-hidden">
            @if($stocks->isEmpty())
                <div class="py-12 text-center text-text-muted t-size4 font-semibold">
                    Tidak ada barang stok yang ditemukan.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                <th class="py-3 px-6">Nama Barang</th>
                                <th class="py-3 px-6">Jumlah Stok</th>
                                <th class="py-3 px-6">Batas Minimum</th>
                                <th class="py-3 px-6">Satuan</th>
                                <th class="py-3 px-6">Status</th>
                                <th class="py-3 px-6">Terakhir Diperbarui</th>
                                <th class="py-3 px-6 text-right w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($stocks as $stock)
                                @php
                                    $isLow = $stock->quantity <= $stock->minimum_quantity;
                                @endphp
                                <tr class="hover:bg-surface/30 transition">
                                    <!-- Name -->
                                    <td class="py-4 px-6 font-bold text-text t-size4">
                                        {{ $stock->name }}
                                    </td>
                                    <!-- Quantity -->
                                    <td class="py-4 px-6 font-semibold t-size4 {{ $isLow ? 'text-danger' : 'text-text' }}">
                                        {{ $stock->quantity }}
                                    </td>
                                    <!-- Minimum Quantity -->
                                    <td class="py-4 px-6 text-text-muted t-size4">
                                        {{ $stock->minimum_quantity }}
                                    </td>
                                    <!-- Unit -->
                                    <td class="py-4 px-6 text-text-muted t-size4">
                                        {{ $stock->unit }}
                                    </td>
                                    <!-- Status Badge -->
                                    <td class="py-4 px-6">
                                        @if($isLow)
                                            <span class="bg-danger/15 text-danger font-bold px-2.5 py-0.5 rounded-full t-size1 border border-danger/35">
                                                Menipis
                                            </span>
                                        @else
                                            <span class="bg-success/15 text-success font-bold px-2.5 py-0.5 rounded-full t-size1 border border-success/35">
                                                Aman
                                            </span>
                                        @endif
                                    </td>
                                    <!-- Last Updated -->
                                    <td class="py-4 px-6 text-text-muted t-size3">
                                        {{ $stock->updated_at->diffForHumans() }}
                                    </td>
                                    <!-- Actions -->
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('admin.stocks.edit', $stock->id) }}" class="text-accent hover:text-primary font-bold t-size3 transition">
                                                Edit
                                            </a>
                                            <button @click="confirmDelete('{{ route('admin.stocks.destroy', $stock->id) }}')" class="text-danger hover:text-red-600 font-bold t-size3 transition cursor-pointer">
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
                    {{ $stocks->links() }}
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
                    Konfirmasi Hapus Stok
                </h3>
                <p class="text-text-muted t-size3">
                    Apakah Anda yakin ingin menghapus stok barang ini? Penghapusan akan memutus ikatan menu apa pun ke item stok ini.
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
