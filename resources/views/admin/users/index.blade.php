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
                    <p class="text-base font-bold text-primary-strong">Manajemen Pengguna</p>
                    <h1 class="mt-1 font-heading text-2xl font-extrabold tracking-tight text-text md:text-3xl">Daftar
                        Pengguna</h1>
                    <p class="mt-2 max-w-2xl text-base leading-6 text-text-muted md:text-base">
                        Kelola akun staf, peran, penempatan cabang, dan status akses sistem.
                    </p>
                </div>
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-base font-bold text-white shadow-sm hover:bg-primary-strong">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Tambah Pengguna
                </a>
            </header>

            <section class="rounded-2xl border border-border bg-card p-4 shadow-[0_10px_30px_var(--color-shadow)] md:p-5" aria-label="Filter pengguna">
                <form method="GET" action="{{ route('admin.users.index') }}"
                    class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-[minmax(260px,1fr)_repeat(3,minmax(150px,190px))_auto]">
                    <label class="relative block">
                        <span class="sr-only">Cari pengguna</span>
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-text-muted" fill="none"
                            stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m20 20-3.5-3.5" />
                        </svg>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                            class="h-12 w-full rounded-xl border-border bg-card pl-12 pr-4 text-base font-medium text-text placeholder:text-text-muted/70 focus:border-primary focus:ring-primary md:text-base">
                    </label>

                    @if (auth()->user()->branch_id === null)
                        <label>
                            <span class="sr-only">Filter cabang</span>
                            <select name="branch_id"
                                class="h-12 w-full rounded-xl border-border bg-card px-4 text-base font-semibold text-text focus:border-primary focus:ring-primary">
                                <option value="">Semua Cabang</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    @endif

                    <label>
                        <span class="sr-only">Filter peran</span>
                        <select name="role_id"
                            class="h-12 w-full rounded-xl border-border bg-card px-4 text-base font-semibold text-text focus:border-primary focus:ring-primary">
                            <option value="">Semua Peran</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>{{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span class="sr-only">Filter status</span>
                        <select name="status"
                            class="h-12 w-full rounded-xl border-border bg-card px-4 text-base font-semibold text-text focus:border-primary focus:ring-primary">
                            <option value="">Semua Status</option>
                            <option value="active" @selected(request('status') === 'active')>Aktif</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                        </select>
                    </label>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="min-h-12 flex-1 rounded-xl bg-primary px-5 text-base font-bold text-white hover:bg-primary-strong xl:flex-none">Terapkan</button>
                        @if (request()->anyFilled(['search', 'branch_id', 'role_id', 'status']))
                            <a href="{{ route('admin.users.index') }}"
                                class="inline-flex min-h-12 items-center justify-center rounded-xl border border-border bg-card px-4 text-base font-bold text-text-muted hover:bg-surface hover:text-text">Reset</a>
                        @endif
                    </div>
                </form>
            </section>

            <section class="overflow-hidden rounded-2xl border border-border bg-card shadow-[0_10px_32px_var(--color-shadow)]"
                aria-labelledby="users-table-title">
                <div class="flex items-center justify-between border-b border-border px-5 py-4 md:px-6">
                    <div>
                        <h2 id="users-table-title" class="font-heading text-lg font-bold text-text md:text-xl">Data Pengguna
                        </h2>
                        <p class="mt-1 text-base text-text-muted">{{ $users->total() }} pengguna ditemukan</p>
                    </div>
                </div>

                @if ($users->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-surface text-primary-strong">
                            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87" />
                            </svg>
                        </div>
                        <p class="mt-3 text-base font-bold text-text">Pengguna tidak ditemukan</p>
                        <p class="mt-1 text-base text-text-muted">Coba ubah kata pencarian atau filter.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[980px] border-separate border-spacing-0 text-left">
                            <thead class="bg-gradient-to-r from-bg to-surface">
                                <tr>
                                    <th class="border-b border-border px-6 py-4 text-base font-bold text-text">Pengguna</th>
                                    <th class="border-b border-border px-5 py-4 text-base font-bold text-text">Peran</th>
                                    <th class="border-b border-border px-5 py-4 text-base font-bold text-text">Cabang</th>
                                    <th class="border-b border-border px-5 py-4 text-base font-bold text-text">Status</th>
                                    <th class="border-b border-border px-5 py-4 text-base font-bold text-text">Bergabung
                                    </th>
                                    <th class="border-b border-border px-6 py-4 text-right text-base font-bold text-text">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach ($users as $user)
                                    @php
                                        $roleConfig = match ($user->role?->name) {
                                            'admin' => ['Admin', 'bg-primary-soft/30 text-accent border-primary-soft'],
                                            'cashier' => ['Kasir', 'bg-info-soft text-blue-700 border-info/30'],
                                            'kitchen' => ['Dapur', 'bg-warning-soft text-amber-700 border-warning/40'],
                                            default => [ucfirst($user->role?->name ?? 'Staf'), 'bg-surface text-text-muted border-border'],
                                        };
                                    @endphp
                                    <tr class="transition-colors duration-200 hover:bg-surface/60">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary-soft/35 text-base font-extrabold text-primary-strong">
                                                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="truncate text-base font-bold text-text">{{ $user->name }}</span>
                                                        @if ($user->id === auth()->id())
                                                            <span class="rounded-md bg-primary-soft/35 px-2 py-0.5 text-sm font-bold text-accent">Anda</span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-1 truncate text-base text-text-muted">{{ $user->email }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span
                                                class="inline-flex rounded-full border px-3 py-1.5 text-base font-bold {{ $roleConfig[1] }}">{{ $roleConfig[0] }}</span>
                                        </td>
                                        <td class="max-w-[220px] px-5 py-4">
                                            <p class="truncate text-base font-semibold text-text">
                                                {{ $user->branch?->name ?: 'Semua Cabang' }}</p>
                                            <p class="mt-1 text-sm text-text-muted">
                                                {{ $user->branch ? 'Staf cabang' : 'Akses global' }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if ($user->id === auth()->id())
                                                <span
                                                    class="inline-flex items-center gap-2 rounded-full bg-success-soft px-3 py-1.5 text-base font-bold text-green-700"><span
                                                        class="h-2 w-2 rounded-full bg-success"></span>Aktif</span>
                                            @else
                                                <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-base font-bold {{ $user->is_active ? 'bg-success-soft text-green-700 hover:bg-success-soft/70' : 'bg-danger-soft text-red-700 hover:bg-danger-soft/70' }}">
                                                        <span class="h-2 w-2 rounded-full {{ $user->is_active ? 'bg-success' : 'bg-danger' }}"></span>
                                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-base font-semibold text-text">
                                                {{ $user->created_at->translatedFormat('d M Y') }}</p>
                                            <p class="mt-1 text-sm text-text-muted">
                                                {{ $user->created_at->diffForHumans() }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.users.edit', $user) }}" title="Edit pengguna"
                                                    aria-label="Edit {{ $user->name }}"
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-primary-soft bg-card text-primary-strong hover:bg-surface">
                                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Z" />
                                                        <path d="m14 7 3 3" />
                                                    </svg>
                                                </a>
                                                @if ($user->id !== auth()->id())
                                                    <button type="button" @click="confirmDelete('{{ route('admin.users.destroy', $user) }}')"
                                                        title="Hapus pengguna" aria-label="Hapus {{ $user->name }}"
                                                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-danger/40 bg-card text-danger hover:bg-danger-soft">
                                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                            <path d="M4 7h16M9 7V4h6v3M7 7l1 14h8l1-14M10 11v6M14 11v6" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <footer class="flex flex-col gap-3 border-t border-border bg-bg/60 px-5 py-4 sm:flex-row sm:items-center sm:justify-between md:px-6">
                        <p class="text-base font-medium text-text-muted">
                            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }}
                            pengguna
                        </p>
                        {{ $users->links() }}
                    </footer>
                @endif
            </section>
        </div>

        <div x-cloak x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/35 px-4">
            <div class="w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-lg" @click.outside="showDeleteModal = false">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-danger-soft text-danger">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 9v4M12 17h.01" />
                        <path d="M10.3 3.8 2.7 17a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3L13.7 3.8a2 2 0 0 0-3.4 0Z" />
                    </svg>
                </div>
                <h3 class="mt-4 font-heading text-xl font-bold text-text">Hapus pengguna?</h3>
                <p class="mt-2 text-base leading-6 text-text-muted">Akun dan akses login pengguna ini akan dihapus.
                    Tindakan ini tidak dapat dibatalkan.</p>
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
