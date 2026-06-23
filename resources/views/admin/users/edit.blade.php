<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Edit Staf') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6 anim-fade">
        <!-- Back Link -->
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.users.index') }}" class="text-text-muted hover:text-text font-bold t-size3 transition flex items-center gap-1">
                &larr; Kembali ke Daftar Staf
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="space-y-4">
                    <h3 class="font-bold t-size5 text-text font-heading border-b border-border pb-2">
                        Perbarui Informasi Staf
                    </h3>

                    <div class="space-y-4">
                        <!-- Full Name -->
                        <div class="space-y-1.5">
                            <label for="name" class="font-bold t-size3 text-text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('name') border-danger @enderror">
                            @error('name')
                                <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="space-y-1.5">
                            <label for="email" class="font-bold t-size3 text-text-muted">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('email') border-danger @enderror">
                            @error('email')
                                <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password Section (Optional on Edit) -->
                        <div class="bg-surface/50 border border-border rounded-xl p-4 space-y-4">
                            <div>
                                <h4 class="font-bold t-size4 text-text font-heading">Ubah Kata Sandi (Opsional)</h4>
                                <p class="text-text-muted t-size2 mt-0.5">Biarkan kosong jika Anda tidak ingin mengubah kata sandi staf ini.</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Password -->
                                <div class="space-y-1.5">
                                    <label for="password" class="font-bold t-size3 text-text-muted">Kata Sandi Baru</label>
                                    <input type="password" id="password" name="password" placeholder="Minimal 8 karakter"
                                        class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('password') border-danger @enderror">
                                    @error('password')
                                        <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="space-y-1.5">
                                    <label for="password_confirmation" class="font-bold t-size3 text-text-muted">Konfirmasi Kata Sandi Baru</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang kata sandi baru"
                                        class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition">
                                </div>
                            </div>
                        </div>

                        <!-- Role & Branch Scope Safeguard -->
                        @if($user->id === auth()->id())
                            <div class="bg-primary-soft/30 border border-primary-soft/50 text-text-muted rounded-xl p-4 t-size3">
                                <span class="font-bold text-accent">Catatan Keamanan:</span> Anda sedang mengedit akun Anda sendiri. Demi keamanan akses sistem, Anda tidak dapat mengubah peran atau penugasan cabang Anda sendiri di halaman ini.
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Role assignment -->
                                <div class="space-y-1.5">
                                    <label for="role_id" class="font-bold t-size3 text-text-muted">Peran Hak Akses <span class="text-danger">*</span></label>
                                    <select id="role_id" name="role_id" required class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer @error('role_id') border-danger @enderror">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Branch Assignment (Only for Super Admin) -->
                                @if(auth()->user()->branch_id === null)
                                    <div class="space-y-1.5">
                                        <label for="branch_id" class="font-bold t-size3 text-text-muted">Cabang Penugasan</label>
                                        <select id="branch_id" name="branch_id" class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer @error('branch_id') border-danger @enderror">
                                            <option value="">Super Admin (Semua Cabang / Tanpa Cabang)</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-text-muted t-size1 mt-1 block">Biarkan kosong jika ingin menyetel sebagai Super Admin global.</span>
                                        @error('branch_id')
                                            <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Button Block -->
                <div class="flex justify-end gap-3 pt-4 border-t border-border">
                    <a href="{{ route('admin.users.index') }}" class="bg-surface border border-border text-text-muted hover:text-text px-6 py-2.5 rounded-xl transition cursor-pointer font-bold t-size4">
                        Batal
                    </a>
                    <button type="submit" class="bg-primary hover:bg-primary-strong text-white font-bold px-6 py-2.5 rounded-xl transition shadow-xs cursor-pointer t-size4">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
