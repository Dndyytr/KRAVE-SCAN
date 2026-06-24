<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Kelola Pengguna & Akses') }}
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
                        Daftar Staf Krave Scan
                    </h3>
                    <p class="text-text-muted t-size3 mt-0.5">
                        Kelola data staf (Admin Cabang & Kasir), penugasan cabang, peran, dan status keaktifan akun.
                    </p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="bg-primary hover:bg-primary-strong text-white font-bold px-5 py-2.5 rounded-xl transition shadow-xs flex items-center gap-2 shrink-0 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Staf
                </a>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap md:flex-nowrap gap-3 pt-2">
                <div class="relative flex-grow">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email staf..." class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2 t-size4 outline-hidden transition">
                </div>

                @if(auth()->user()->branch_id === null)
                    <div class="w-full md:w-48">
                        <select name="branch_id" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                            <option value="">Semua Cabang</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="w-full md:w-44">
                    <select name="role_id" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                        <option value="">Semua Peran</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-44">
                    <select name="status" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div class="flex gap-2 w-full md:w-auto shrink-0">
                    <button type="submit" class="flex-grow md:flex-grow-0 bg-primary hover:bg-primary-strong text-white font-bold px-6 py-2.5 rounded-xl transition shadow-xs cursor-pointer">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'branch_id', 'role_id', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="bg-surface border border-border hover:bg-border text-text-muted hover:text-text px-4 py-2.5 rounded-xl transition cursor-pointer text-center flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>


        <!-- Users Table List -->
        <div class="bg-card border border-border rounded-2xl shadow-xs overflow-hidden">
            @if($users->isEmpty())
                <div class="py-12 text-center text-text-muted t-size4 font-semibold">
                    Tidak ada data staf yang ditemukan.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                <th class="py-3 px-6">Nama Staf</th>
                                <th class="py-3 px-6">Email</th>
                                <th class="py-3 px-6">Peran</th>
                                <th class="py-3 px-6">Cabang</th>
                                <th class="py-3 px-6 w-32">Status Keaktifan</th>
                                <th class="py-3 px-6 text-right w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($users as $user)
                                <tr class="hover:bg-surface/30 transition">
                                    <!-- Name -->
                                    <td class="py-4 px-6 font-bold text-text t-size4">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="ml-1 bg-primary-soft/50 text-accent font-bold px-1.5 py-0.5 rounded t-size1 border border-primary-soft/80">Anda</span>
                                        @endif
                                    </td>
                                    <!-- Email -->
                                    <td class="py-4 px-6 text-text-muted t-size3">
                                        {{ $user->email }}
                                    </td>
                                    <!-- Role badge -->
                                    <td class="py-4 px-6">
                                        @if($user->role?->name === 'admin')
                                            <span class="bg-accent/10 text-accent font-bold px-2.5 py-0.5 rounded-full t-size1 border border-accent/25">
                                                Admin
                                            </span>
                                        @else
                                            <span class="bg-primary-soft/40 text-primary-strong font-bold px-2.5 py-0.5 rounded-full t-size1 border border-primary-soft/60">
                                                Kasir
                                            </span>
                                        @endif
                                    </td>
                                    <!-- Branch name -->
                                    <td class="py-4 px-6 text-text t-size3 font-medium">
                                        {{ $user->branch?->name ?: 'Super Admin (Semua Cabang)' }}
                                    </td>
                                    <!-- Active/Suspend toggle status -->
                                    <td class="py-4 px-6">
                                        @if($user->id === auth()->id())
                                            <span class="bg-success/15 text-success border-success/35 px-3 py-1 rounded-full t-size2 font-bold border">
                                                Aktif
                                            </span>
                                        @else
                                            <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="px-3 py-1 rounded-full t-size2 font-bold border transition cursor-pointer {{ $user->is_active ? 'bg-success/15 text-success border-success/35 hover:bg-success/25' : 'bg-danger/15 text-danger border-danger/35 hover:bg-danger/25' }}">
                                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <!-- Actions -->
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-accent hover:text-primary font-bold t-size3 transition">
                                                Edit
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <button @click="confirmDelete('{{ route('admin.users.destroy', $user->id) }}')" class="text-danger hover:text-red-600 font-bold t-size3 transition cursor-pointer">
                                                    Hapus
                                                </button>
                                            @else
                                                <span class="text-text-muted/40 font-bold t-size3 cursor-not-allowed select-none">Hapus</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination block -->
                <div class="px-6 py-4 border-t border-border">
                    {{ $users->links() }}
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
                    Konfirmasi Hapus Staf
                </h3>
                <p class="text-text-muted t-size3">
                    Apakah Anda yakin ingin menghapus staf ini? Semua data login dan histori aksi yang terkait mungkin tidak dapat diakses lagi. Tindakan ini tidak dapat dibatalkan.
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
