<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold t-size8 font-heading text-text">
                    {{ __('Tambah Akses') }}
                </h2>
                <p class="text-text-muted t-size3 mt-1">Buat akses/role baru dan atur izin untuk setiap modul sistem.</p>
            </div>
            <div class="hidden sm:block">
                <div class="bg-card border border-border px-4 py-2 rounded-2xl flex items-center gap-2 t-size2 font-semibold text-text">
                    <span class="text-primary">📅</span>
                    {{ now()->translatedFormat('l, d M Y') }}
                </div>
            </div>
        </div>
    </x-slot>

    @php
        // Group permissions logically to represent modular row matrix
        $groupedPermissions = [
            'Dasbor & Utama' => [
                'access_admin' => 'Akses Halaman Admin',
            ],
            'Kasir & Menu' => [
                'access_cashier' => 'Akses Halaman Kasir',
                'manage_menus' => 'Kelola Menu Hidangan',
                'manage_categories' => 'Kelola Kategori Menu',
            ],
            'Dapur' => [
                'access_kitchen' => 'Akses Halaman Dapur',
            ],
            'Pengguna & Hak Akses' => [
                'manage_users' => 'Kelola Pengguna',
                'manage_roles' => 'Kelola Peran & Akses',
            ],
            'Stok & Inventaris' => [
                'manage_stocks' => 'Kelola Stok Bahan',
            ],
            'Laporan & Transaksi' => [
                'view_all_orders' => 'Lihat Semua Pesanan (Admin)',
                'view_all_transactions' => 'Lihat Semua Transaksi (Admin)',
                'view_reports' => 'Lihat Laporan Penjualan & Performa',
            ],
            'Sistem & Log' => [
                'manage_automations' => 'Kelola Otomatisasi',
                'view_activity_logs' => 'Lihat Log Aktivitas',
            ],
        ];
    @endphp

    <div class="max-w-[1400px] mx-auto space-y-6 anim-fade" x-data="{
        checkedCount: 0,
        totalPermissions: 13,
        init() {
            this.updateCount();
        },
        updateCount() {
            this.checkedCount = document.querySelectorAll('.permission-checkbox:checked').length;
        },
        selectAll() {
            document.querySelectorAll('.permission-checkbox').forEach(el => el.checked = true);
            this.updateCount();
        },
        deselectAll() {
            document.querySelectorAll('.permission-checkbox').forEach(el => el.checked = false);
            this.updateCount();
        },
        get percentage() {
            return ((this.checkedCount / this.totalPermissions) * 100).toFixed(1);
        },
        get strokeDash() {
            // Circumference of circle with radius 40 is 2 * pi * 40 ≈ 251.2
            let circ = 251.2;
            let offset = circ - (this.checkedCount / this.totalPermissions) * circ;
            return offset;
        }
    }">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 t-size2 text-text-muted font-semibold">
            <a href="{{ route('dashboard') }}" class="hover:text-text transition">Home</a>
            <span>›</span>
            <a href="{{ route('admin.roles.index') }}" class="hover:text-text transition">Role & Permission</a>
            <span>›</span>
            <span class="text-text font-bold">Tambah Akses</span>
        </div>

        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                <!-- Left Side: Form Sections (col-span-2) -->
                <div class="lg:col-span-2 space-y-6">

                    {{-- Card 1: Informasi Akses --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <h3 class="font-bold t-size5 text-text font-heading border-b border-border pb-3 mb-5 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-primary-soft/40 flex items-center justify-center text-accent t-size3">📋</span>
                            Informasi Akses
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nama Akses / Role -->
                            <div class="space-y-1.5 md:col-span-2">
                                <label for="name" class="font-bold t-size3 text-text-muted">Nama Akses / Role <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Supervisor Outlet" required
                                    class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('name') border-danger @enderror">
                                @error('name')
                                    <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Deskripsi (Visual only) -->
                            <div class="space-y-1.5 md:col-span-2">
                                <label class="font-bold t-size3 text-text-muted">Deskripsi</label>
                                <input type="text" disabled placeholder="Jelaskan tujuan dan ruang lingkup akses ini"
                                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed">
                            </div>

                            <!-- Level Akses (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Level Akses</label>
                                <select disabled
                                    class="w-full bg-surface border border-border rounded-xl px-3 py-2.5 t-size4 text-text-muted cursor-not-allowed">
                                    <option>Staf</option>
                                </select>
                            </div>

                            <!-- Cabang / Scope (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Cabang / Scope</label>
                                <select disabled
                                    class="w-full bg-surface border border-border rounded-xl px-3 py-2.5 t-size4 text-text-muted cursor-not-allowed">
                                    <option>Semua Cabang</option>
                                </select>
                            </div>

                            <!-- Status (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Status</label>
                                <div class="flex items-center gap-2 bg-surface border border-border rounded-xl px-3 py-2.5 text-text-muted">
                                    <span class="w-2.5 h-2.5 bg-success rounded-full"></span>
                                    <span class="font-semibold t-size3 text-text">Aktif</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Hak Modul --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-border pb-3 mb-5 gap-3">
                            <div>
                                <h3 class="font-bold t-size5 text-text font-heading flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg bg-primary-soft/40 flex items-center justify-center text-accent t-size3">🔑</span>
                                    Hak Modul
                                </h3>
                                <p class="text-text-muted t-size2 mt-0.5">Atur hak akses untuk setiap modul berdasarkan kebutuhan.</p>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="selectAll()" class="text-primary hover:text-primary-strong text-xs font-bold transition">
                                    Pilih Semua
                                </button>
                                <span class="text-border">|</span>
                                <button type="button" @click="deselectAll()" class="text-text-muted hover:text-text text-xs font-bold transition">
                                    Batalkan Semua
                                </button>
                            </div>
                        </div>

                        {{-- Modular Permissions Grid Table --}}
                        <div class="border border-border rounded-2xl overflow-hidden shadow-2xs">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-surface-alt border-b border-border text-text-muted t-size2 font-bold uppercase tracking-wider">
                                            <th class="py-3.5 px-6 w-1/3">Modul</th>
                                            <th class="py-3.5 px-6 w-2/3">Daftar Hak Akses</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border">
                                        @foreach ($groupedPermissions as $groupName => $perms)
                                            <tr class="hover:bg-surface/30 transition">
                                                <td class="py-4 px-6 font-bold text-text t-size3">
                                                    {{ $groupName }}
                                                </td>
                                                <td class="py-4 px-6">
                                                    <div class="flex flex-wrap gap-4">
                                                        @foreach ($perms as $permName => $permLabel)
                                                            @php
                                                                $perm = $permissions->where('name', $permName)->first();
                                                            @endphp
                                                            @if ($perm)
                                                                <label
                                                                    class="inline-flex items-center gap-2 cursor-pointer bg-card border border-border hover:bg-surface px-3 py-1.5 rounded-xl transition">
                                                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                                        @change="updateCount()"
                                                                        class="permission-checkbox w-4 h-4 rounded text-primary focus:ring-primary border-border cursor-pointer">
                                                                    <span class="t-size2 font-semibold text-text">{{ $permLabel }}</span>
                                                                </label>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
                        <a href="{{ route('admin.roles.index') }}"
                            class="bg-card border border-border text-text-muted hover:text-text px-8 py-3 rounded-full transition cursor-pointer font-bold t-size4 text-center">
                            Batal
                        </a>
                        <button type="button" disabled
                            class="bg-card border border-border text-text-muted px-8 py-3 rounded-full font-bold t-size4 text-center cursor-not-allowed opacity-50">
                            Simpan Draft
                        </button>
                        <button type="submit"
                            class="bg-primary hover:bg-primary-strong text-white font-extrabold px-10 py-3 rounded-full transition shadow-xs cursor-pointer t-size4 text-center">
                            Simpan Akses
                        </button>
                    </div>

                </div>

                <!-- Right Side: Sidebar (col-span-1) -->
                <div class="space-y-6">

                    {{-- Ringkasan Izin --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs text-center space-y-4">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 text-left">
                            📊 Ringkasan Izin
                        </h3>

                        <div class="bg-surface rounded-2xl p-5 border border-border flex flex-col items-center justify-center space-y-4 transition-all">
                            <!-- SVG Donut Chart -->
                            <div class="relative w-28 h-28 flex items-center justify-center">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                    <!-- Background circle -->
                                    <circle cx="50" cy="50" r="40" stroke="var(--color-border)" stroke-width="10" fill="transparent"
                                        class="text-border" />
                                    <!-- Progress circle -->
                                    <circle cx="50" cy="50" r="40" stroke="var(--color-primary)" stroke-width="10" fill="transparent"
                                        stroke-linecap="round" stroke-dasharray="251.2" :stroke-dashoffset="strokeDash"
                                        class="transition-all duration-300" />
                                </svg>
                                <div class="absolute flex flex-col items-center justify-center">
                                    <span class="font-extrabold text-accent t-size5" x-text="checkedCount"></span>
                                    <span class="text-text-muted text-[10px] font-bold">Izin Terpilih</span>
                                </div>
                            </div>

                            <div class="w-full space-y-2.5 t-size2.5 text-left border-t border-border pt-4">
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Total Modul</span>
                                    <span class="font-bold text-text">7</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Total Izin Tersedia</span>
                                    <span class="font-bold text-text" x-text="totalPermissions"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Total Izin Dipilih</span>
                                    <span class="font-bold text-accent" x-text="checkedCount"></span>
                                </div>
                                <div class="flex justify-between border-t border-border/50 pt-2">
                                    <span class="text-text-muted">Persentase</span>
                                    <span class="font-extrabold text-text" x-text="percentage + '%'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-info-soft/30 border border-info-soft/60 text-text-muted rounded-2xl p-4 text-left space-y-2 t-size2 leading-relaxed">
                            <div class="flex items-center gap-1.5 text-accent font-extrabold t-size3">
                                ℹ️ Informasi
                            </div>
                            <p>Izin akses di atas mengontrol kemampuan pengguna dalam mengakses halaman admin, kasir, dapur, dan modul-modul lainnya secara
                                menyeluruh.</p>
                        </div>
                    </div>

                    {{-- Tips --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs space-y-4">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 flex items-center gap-2">
                            <span class="text-info">💡</span> Tips
                        </h3>

                        <ul class="space-y-3 t-size3 text-text-muted">
                            <li class="flex items-start gap-2.5">
                                <span class="text-success font-bold text-sm">✓</span>
                                <span>Berikan akses secukupnya sesuai kebutuhan untuk menjaga keamanan data.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-success font-bold text-sm">✓</span>
                                <span>Periksa kembali daftar hak akses modul sebelum menekan tombol simpan.</span>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>

        </form>
    </div>
</x-app-layout>
