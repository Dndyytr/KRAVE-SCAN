<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Kelola Hak Akses & Peran') }}
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
        @if (session('success'))
            <div class="bg-success/10 border border-success/30 text-success p-4 rounded-xl t-size3 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-danger/10 border border-danger/30 text-danger p-4 rounded-xl t-size3 font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <!-- Top Actions Panel -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h3 class="font-bold t-size5 text-text font-heading">
                        Daftar Peran Staf
                    </h3>
                    <p class="text-text-muted t-size3 mt-0.5">
                        Kelola peran (roles) dinamis untuk staf serta penugasan hak akses (permissions) masing-masing peran.
                    </p>
                </div>
                <a href="{{ route('admin.roles.create') }}"
                    class="bg-primary hover:bg-primary-strong text-white font-bold px-5 py-2.5 rounded-xl transition shadow-xs flex items-center gap-2 shrink-0 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Peran Baru
                </a>
            </div>
        </div>

        <!-- Roles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($roles as $role)
                <div class="bg-card border border-border rounded-2xl p-6 shadow-xs flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="font-extrabold t-size5 font-heading text-text capitalize">
                                {{ $role->name }}
                            </span>
                            @if (in_array($role->name, ['admin', 'cashier', 'kitchen']))
                                <span class="bg-primary-soft/40 text-accent font-bold px-2.5 py-0.5 rounded-full text-[11px] uppercase tracking-wider">
                                    Sistem
                                </span>
                            @else
                                <span class="bg-surface border border-border text-text-muted font-bold px-2.5 py-0.5 rounded-full text-[11px] uppercase tracking-wider">
                                    Kustom
                                </span>
                            @endif
                        </div>

                        <p class="text-text-muted t-size2">
                            Memiliki {{ $role->permissions->count() }} hak akses aktif.
                        </p>

                        <!-- Permissions Badges -->
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            @forelse($role->permissions->take(6) as $permission)
                                <span class="bg-surface-alt border border-border text-text-muted text-[11px] px-2 py-0.5 rounded-md font-semibold">
                                    {{ $permission->label }}
                                </span>
                            @empty
                                <span class="text-text-muted italic text-[11px] font-semibold">
                                    Tidak ada hak akses yang ditetapkan.
                                </span>
                            @endforelse
                            @if ($role->permissions->count() > 6)
                                <span class="bg-surface-alt border border-border text-primary text-[11px] px-2 py-0.5 rounded-md font-extrabold">
                                    +{{ $role->permissions->count() - 6 }} lainnya
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-border/60">
                        <a href="{{ route('admin.roles.edit', $role->id) }}"
                            class="bg-surface hover:bg-border text-text border border-border font-bold px-4 py-2 rounded-xl transition t-size3 flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            Edit
                        </a>

                        @if (!in_array($role->name, ['admin', 'cashier', 'kitchen']))
                            <button type="button" @click="confirmDelete('{{ route('admin.roles.destroy', $role->id) }}')"
                                class="bg-danger/10 hover:bg-danger/20 text-danger border border-danger/20 font-bold px-4 py-2 rounded-xl transition t-size3 flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Hapus
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
            <div class="bg-card border border-border w-full max-w-md rounded-3xl p-6 space-y-6 shadow-xl"
                @click.away="showDeleteModal = false">
                <div class="space-y-2">
                    <h3 class="font-bold t-size5 font-heading text-text">
                        Konfirmasi Hapus Peran
                    </h3>
                    <p class="text-text-muted t-size3">
                        Apakah Anda yakin ingin menghapus peran kustom ini? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="bg-surface hover:bg-border text-text border border-border font-bold px-5 py-2.5 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <form :action="deleteRoute" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-danger hover:bg-danger-strong text-white font-bold px-5 py-2.5 rounded-xl transition shadow-xs cursor-pointer">
                            Hapus Peran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
