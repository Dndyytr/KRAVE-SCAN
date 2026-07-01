<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Tambah Peran Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6 anim-fade">
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-border/60 mb-6">
                <div>
                    <h3 class="font-bold t-size5 text-text font-heading">
                        Buat Peran Baru
                    </h3>
                    <p class="text-text-muted t-size2 mt-0.5">
                        Definisikan nama peran kustom baru dan tetapkan hak akses modul yang diizinkan.
                    </p>
                </div>
                <a href="{{ route('admin.roles.index') }}"
                    class="bg-surface hover:bg-border text-text border border-border font-bold px-4 py-2 rounded-xl transition t-size3 flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>

            <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
                @csrf

                <!-- Name Input -->
                <div class="space-y-2">
                    <label for="name" class="block font-bold text-text t-size3">
                        Nama Peran <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        placeholder="Misal: supervisor, manager, dll."
                        class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-3 t-size4 outline-hidden transition">
                    @error('name')
                        <p class="text-danger t-size2 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Permissions Grid -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <label class="block font-bold text-text t-size3">
                            Tetapkan Hak Akses (Permissions)
                        </label>
                        <div class="flex gap-2">
                            <button type="button" @click="
                                document.querySelectorAll('.permission-checkbox').forEach(el => el.checked = true)
                            " class="text-primary hover:text-primary-strong text-xs font-extrabold cursor-pointer">
                                Pilih Semua
                            </button>
                            <span class="text-border">|</span>
                            <button type="button" @click="
                                document.querySelectorAll('.permission-checkbox').forEach(el => el.checked = false)
                            " class="text-text-muted hover:text-text text-xs font-extrabold cursor-pointer">
                                Batalkan Semua
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($permissions as $permission)
                            <label class="flex items-start gap-3 bg-surface hover:bg-surface-alt border border-border p-4 rounded-xl cursor-pointer transition">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                    class="permission-checkbox mt-1 w-4 h-4 rounded text-primary focus:ring-primary border-border cursor-pointer">
                                <div class="space-y-0.5">
                                    <span class="block font-bold text-text t-size3">{{ $permission->label }}</span>
                                    <span class="block text-text-muted text-[11px] font-semibold font-mono">{{ $permission->name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3 pt-6 border-t border-border/60">
                    <a href="{{ route('admin.roles.index') }}"
                        class="bg-surface hover:bg-border text-text border border-border font-bold px-6 py-3 rounded-xl transition cursor-pointer">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-primary hover:bg-primary-strong text-white font-bold px-8 py-3 rounded-xl transition shadow-xs cursor-pointer">
                        Simpan Peran
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
