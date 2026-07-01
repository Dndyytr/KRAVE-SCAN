<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold t-size8 font-heading text-text">
                    {{ __('Edit Pengguna') }}
                </h2>
                <p class="text-text-muted t-size3 mt-1">Perbarui detail akun pengguna dan cabang penugasan.</p>
            </div>
            <div class="hidden sm:block">
                <div class="bg-card border border-border px-4 py-2 rounded-2xl flex items-center gap-2 t-size2 font-semibold text-text">
                    <span class="text-primary">📅</span>
                    {{ now()->translatedFormat('l, d M Y') }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-[1400px] mx-auto space-y-6 anim-fade" x-data="{
        roleId: '{{ old('role_id', $user->role_id) }}',
        get roleSummary() {
            if (!this.roleId) return 'Belum ada role dipilih';
            let select = document.getElementById('role_id');
            if (!select) return '{{ $user->role ? ucfirst($user->role->name) : 'Belum ada role dipilih' }}';
            let option = select.options[select.selectedIndex];
            return option ? option.text : 'Belum ada role dipilih';
        },
        get roleDescription() {
            let summary = this.roleSummary.toLowerCase();
            if (summary.includes('admin')) {
                return 'Super Admin memiliki akses penuh ke seluruh fitur sistem, termasuk pengelolaan pengguna, cabang, menu, transaksi, laporan, dan otomatisasi.';
            } else if (summary.includes('kasir') || summary.includes('cashier')) {
                return 'Akses khusus untuk kasir. Dapat membuka halaman kasir, mengelola transaksi pembayaran pelanggan, dan mencetak struk digital.';
            } else if (summary.includes('dapur') || summary.includes('kitchen')) {
                return 'Akses khusus untuk kru dapur. Dapat memantau pesanan masuk, mengelola status pengerjaan makanan, dan menandai kesiapan hidangan.';
            }
            return 'Akses standar sesuai modul yang ditugaskan oleh administrator.';
        }
    }">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 t-size2 text-text-muted font-semibold">
            <a href="{{ route('dashboard') }}" class="hover:text-text transition">Home</a>
            <span>›</span>
            <a href="{{ route('admin.users.index') }}" class="hover:text-text transition">Manajemen Pengguna</a>
            <span>›</span>
            <span class="text-text font-bold">Edit Pengguna</span>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                <!-- Left Side: Form Fields (col-span-2) -->
                <div class="lg:col-span-2 space-y-6">

                    {{-- Card 1: Informasi Dasar --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <h3 class="font-bold t-size5 text-text font-heading border-b border-border pb-3 mb-5 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-primary-soft/40 flex items-center justify-center text-accent t-size3">👤</span>
                            Informasi Dasar
                        </h3>

                        <div class="flex flex-col sm:flex-row gap-6">
                            <!-- Foto Profil (Visual Only) -->
                            <div class="flex flex-col items-center shrink-0">
                                <label class="block font-bold t-size2 text-text-muted mb-2 text-center sm:text-left w-full">Foto Profil</label>
                                <div
                                    class="relative w-36 h-36 border-2 border-dashed border-border rounded-2xl flex flex-col items-center justify-center bg-surface-alt/10 hover:border-primary transition cursor-pointer p-4 text-center">
                                    <div class="w-10 h-10 rounded-full bg-primary-soft/30 flex items-center justify-center text-accent mb-2">
                                        📷
                                    </div>
                                    <span class="t-size1 font-bold text-primary">Ganti Foto</span>
                                    <span class="text-[9px] text-text-muted mt-1">PNG, JPG maks. 2MB</span>
                                    <input type="file" disabled class="absolute inset-0 w-full h-full opacity-0 cursor-not-allowed">
                                </div>
                            </div>

                            <!-- Inputs Grid -->
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Nama Lengkap --}}
                                <div class="space-y-1.5 md:col-span-2">
                                    <label for="name" class="font-bold t-size3 text-text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                        placeholder="Masukkan nama lengkap" required
                                        class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('name') border-danger @enderror">
                                    @error('name')
                                        <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Username (Visual only / disabled placeholder) --}}
                                <div class="space-y-1.5">
                                    <label class="font-bold t-size3 text-text-muted">Username <span class="text-danger">*</span></label>
                                    <input type="text" disabled value="{{ strstr($user->email, '@', true) }}"
                                        class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed">
                                </div>

                                {{-- Email --}}
                                <div class="space-y-1.5">
                                    <label for="email" class="font-bold t-size3 text-text-muted">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                        placeholder="nama@kravescan.com" required
                                        class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('email') border-danger @enderror">
                                    @error('email')
                                        <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Nomor HP (Visual only / disabled placeholder) --}}
                                <div class="space-y-1.5 md:col-span-2">
                                    <label class="font-bold t-size3 text-text-muted">Nomor HP</label>
                                    <div class="flex gap-2">
                                        <select disabled
                                            class="bg-surface border border-border rounded-xl px-3 py-2.5 t-size4 text-text-muted cursor-not-allowed">
                                            <option>+62</option>
                                        </select>
                                        <input type="text" disabled placeholder="812-3456-7890"
                                            class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Akun & Keamanan (Optional on Edit) --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <div class="border-b border-border pb-3 mb-5">
                            <h3 class="font-bold t-size5 text-text font-heading flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg bg-primary-soft/40 flex items-center justify-center text-accent t-size3">🔒</span>
                                Akun & Keamanan
                            </h3>
                            <p class="text-text-muted t-size2 mt-0.5">Biarkan kosong jika Anda tidak ingin mengubah password pengguna ini.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Password -->
                            <div class="space-y-1.5" x-data="{ showPass: false }">
                                <label for="password" class="font-bold t-size3 text-text-muted">Password Baru</label>
                                <div class="relative">
                                    <input :type="showPass ? 'text' : 'password'" id="password" name="password" placeholder="Masukkan password baru"
                                        class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl pl-4 pr-11 py-2.5 t-size4 outline-hidden transition @error('password') border-danger @enderror">
                                    <button type="button" @click="showPass = !showPass"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-text-muted hover:text-text">
                                        <span x-text="showPass ? '👁️' : '👁️‍🗨️'"></span>
                                    </button>
                                </div>
                                <span class="t-size1 text-text-muted block mt-1">Minimal 8 karakter</span>
                                @error('password')
                                    <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-1.5" x-data="{ showConfirm: false }">
                                <label for="password_confirmation" class="font-bold t-size3 text-text-muted">Konfirmasi Password Baru</label>
                                <div class="relative">
                                    <input :type="showConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation"
                                        placeholder="Konfirmasi password baru"
                                        class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl pl-4 pr-11 py-2.5 t-size4 outline-hidden transition">
                                    <button type="button" @click="showConfirm = !showConfirm"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-text-muted hover:text-text">
                                        <span x-text="showConfirm ? '👁️' : '👁️‍🗨️'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Penempatan & Hak Akses --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <h3 class="font-bold t-size5 text-text font-heading border-b border-border pb-3 mb-5 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-primary-soft/40 flex items-center justify-center text-accent t-size3">🛡️</span>
                            Penempatan & Hak Akses
                        </h3>

                        @if ($user->id === auth()->id())
                            <div class="bg-primary-soft/30 border border-primary-soft/50 text-text-muted rounded-2xl p-4 t-size3 mb-4">
                                <span class="font-bold text-accent">Catatan Keamanan:</span> Anda sedang mengedit akun
                                Anda sendiri. Demi keamanan akses sistem, Anda tidak dapat mengubah peran atau penugasan
                                cabang Anda sendiri di halaman ini.
                            </div>
                            <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                            @if ($user->branch_id !== null)
                                <input type="hidden" name="branch_id" value="{{ $user->branch_id }}">
                            @endif
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Role Peran -->
                                <div class="space-y-1.5">
                                    <label for="role_id" class="font-bold t-size3 text-text-muted">Role / Peran <span class="text-danger">*</span></label>
                                    <select id="role_id" name="role_id" required x-model="roleId"
                                        class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer @error('role_id') border-danger @enderror">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Cabang / Outlet -->
                                @if (auth()->user()->branch_id === null)
                                    <div class="space-y-1.5">
                                        <label for="branch_id" class="font-bold t-size3 text-text-muted">Cabang / Outlet</label>
                                        <select id="branch_id" name="branch_id"
                                            class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer @error('branch_id') border-danger @enderror">
                                            <option value="">Super Admin (Semua Cabang / Tanpa Cabang)</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                    {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @else
                                    <div class="space-y-1.5">
                                        <label class="font-bold t-size3 text-text-muted">Cabang / Outlet</label>
                                        <input type="text" disabled value="{{ auth()->user()->branch->name ?? 'Cabang Aktif' }}"
                                            class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed">
                                    </div>
                                @endif

                                <!-- Status Akun (Visual only / disabled) -->
                                <div class="space-y-1.5">
                                    <label class="font-bold t-size3 text-text-muted">Status Akun</label>
                                    <div class="flex items-center gap-2 bg-surface border border-border rounded-xl px-3 py-2.5 text-text-muted">
                                        <span class="w-2.5 h-2.5 bg-success rounded-full"></span>
                                        <span class="font-semibold t-size3 text-text">Aktif</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Catatan (Visual only / disabled textarea) --}}
                        <div class="space-y-1.5 mt-5">
                            <label class="font-bold t-size3 text-text-muted">Catatan (Opsional)</label>
                            <textarea disabled rows="3" placeholder="Tambahkan catatan terkait penugasan atau informasi lainnya..."
                                class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed"></textarea>
                            <div class="flex justify-end">
                                <span class="t-size1 text-text-muted">0/255</span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
                        <a href="{{ route('admin.users.index') }}"
                            class="bg-card border border-border text-text-muted hover:text-text px-8 py-3 rounded-full transition cursor-pointer font-bold t-size4 text-center">
                            Batal
                        </a>
                        <button type="button" disabled
                            class="bg-card border border-border text-text-muted px-8 py-3 rounded-full font-bold t-size4 text-center cursor-not-allowed opacity-50">
                            Simpan Draft
                        </button>
                        <button type="submit"
                            class="bg-primary hover:bg-primary-strong text-white font-extrabold px-10 py-3 rounded-full transition shadow-xs cursor-pointer t-size4 text-center">
                            Simpan Perubahan
                        </button>
                    </div>

                </div>

                <!-- Right Side: Sidebar Cards (col-span-1) -->
                <div class="space-y-6">

                    {{-- Ringkasan Hak Akses --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs text-center space-y-4">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 text-left">
                            🛡️ Ringkasan Hak Akses
                        </h3>
                        <p class="text-text-muted t-size2 text-left">Preview hak akses berdasarkan role yang dipilih.</p>

                        <div
                            class="bg-surface rounded-2xl p-5 border border-border flex flex-col items-center justify-center min-h-[200px] space-y-4 transition-all">
                            <div class="w-16 h-16 rounded-full bg-primary-soft/30 flex items-center justify-center text-accent t-size7 shadow-xs">
                                🛡️
                            </div>
                            <div>
                                <h4 class="font-extrabold text-accent t-size4 font-heading capitalize" x-text="roleSummary"></h4>
                                <p class="text-text-muted t-size2 mt-2 leading-relaxed text-center" x-text="roleDescription"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Tips & Best Practice --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs space-y-4">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 flex items-center gap-2">
                            <span class="text-info">ℹ️</span> Tips & Best Practice
                        </h3>

                        <ul class="space-y-3 t-size3 text-text-muted">
                            <li class="flex items-start gap-2.5">
                                <span class="text-success font-bold text-sm">✓</span>
                                <span>Gunakan alamat email resmi staf yang valid dan aktif.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-success font-bold text-sm">✓</span>
                                <span>Pastikan password kuat dan tidak mudah ditebak oleh orang lain.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-success font-bold text-sm">✓</span>
                                <span>Pilih role sesuai tanggung jawab pengguna demi keamanan data.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-success font-bold text-sm">✓</span>
                                <span>Batasi akses hanya pada outlet yang relevan dengan penugasan.</span>
                            </li>
                        </ul>

                        <div class="bg-info-soft/30 border border-info-soft/80 text-text-muted rounded-2xl p-4 space-y-2 mt-4">
                            <div class="flex items-center gap-2 text-accent font-extrabold t-size3">
                                🔒 Keamanan data adalah prioritas kami.
                            </div>
                            <p class="t-size1 leading-relaxed">
                                Pastikan hanya memberikan hak akses pengguna yang berwenang memiliki akses ke sistem KraveScan.
                            </p>
                        </div>
                    </div>

                </div>

            </div>

        </form>
    </div>
</x-app-layout>
