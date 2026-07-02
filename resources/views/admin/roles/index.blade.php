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
                    <p class="text-base font-bold text-primary-strong">Pengaturan Akses</p>
                    <h1 class="mt-1 font-heading text-2xl font-extrabold tracking-tight text-text md:text-3xl">Peran & Hak
                        Akses</h1>
                    <p class="mt-2 max-w-2xl text-base leading-7 text-text-muted">Atur peran staf dan tentukan fitur yang
                        dapat digunakan oleh setiap peran.</p>
                </div>
                <a href="{{ route('admin.roles.create') }}"
                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-base font-bold text-white shadow-sm hover:bg-primary-strong">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Tambah Peran
                </a>
            </header>

            <section class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3" aria-label="Daftar peran">
                @forelse ($roles as $role)
                    @php($isSystemRole = in_array($role->name, ['admin', 'cashier', 'kitchen']))
                    <article
                        class="flex min-h-[280px] flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-[0_10px_30px_var(--color-shadow)] transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <div class="flex-1 p-5 md:p-6">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $isSystemRole ? 'bg-primary-soft/35 text-primary-strong' : 'bg-info-soft text-blue-700' }}">
                                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" />
                                            <path d="m9 12 2 2 4-4" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h2 class="truncate font-heading text-xl font-extrabold capitalize text-text">
                                            {{ $role->name }}</h2>
                                        <p class="mt-1 text-base text-text-muted">{{ $role->permissions->count() }} hak
                                            akses aktif</p>
                                    </div>
                                </div>
                                <span
                                    class="shrink-0 rounded-full border px-3 py-1 text-sm font-bold {{ $isSystemRole ? 'border-primary-soft bg-primary-soft/30 text-accent' : 'border-border bg-surface text-text-muted' }}">
                                    {{ $isSystemRole ? 'Sistem' : 'Kustom' }}
                                </span>
                            </div>

                            <div class="mt-5 border-t border-border pt-4">
                                <p class="text-base font-bold text-text">Hak akses utama</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @forelse ($role->permissions->take(6) as $permission)
                                        <span
                                            class="rounded-lg border border-border bg-surface px-3 py-1.5 text-sm font-semibold leading-5 text-text-muted">{{ $permission->label }}</span>
                                    @empty
                                        <p class="text-base italic text-text-muted">Belum ada hak akses yang ditetapkan.</p>
                                    @endforelse
                                    @if ($role->permissions->count() > 6)
                                        <span
                                            class="rounded-lg border border-primary-soft bg-primary-soft/20 px-3 py-1.5 text-sm font-bold text-primary-strong">+{{ $role->permissions->count() - 6 }}
                                            lainnya</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <footer class="flex items-center justify-end gap-2 border-t border-border bg-bg/60 px-5 py-4 md:px-6">
                            <a href="{{ route('admin.roles.edit', $role) }}"
                                class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-primary-soft bg-card px-4 text-base font-bold text-primary-strong hover:bg-surface">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="m4 20 4.5-1 10-10a2.1 2.1 0 0 0-3-3l-10 10L4 20Z" />
                                    <path d="m14 7 3 3" />
                                </svg>
                                Edit Akses
                            </a>
                            @if (!$isSystemRole)
                                <button type="button" @click="confirmDelete('{{ route('admin.roles.destroy', $role) }}')"
                                    class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-danger/40 bg-card px-4 text-base font-bold text-danger hover:bg-danger-soft">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 7h16M9 7V4h6v3M7 7l1 14h8l1-14" />
                                    </svg>
                                    Hapus
                                </button>
                            @endif
                        </footer>
                    </article>
                @empty
                    <div class="rounded-2xl border border-border bg-card px-6 py-16 text-center md:col-span-2 xl:col-span-3">
                        <p class="text-lg font-bold text-text">Belum ada peran</p>
                        <p class="mt-1 text-base text-text-muted">Tambahkan peran untuk mulai mengatur akses staf.</p>
                    </div>
                @endforelse
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
                <h3 class="mt-4 font-heading text-xl font-bold text-text">Hapus peran?</h3>
                <p class="mt-2 text-base leading-7 text-text-muted">Peran kustom ini akan dihapus. Peran bawaan sistem
                    tetap dilindungi.</p>
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
