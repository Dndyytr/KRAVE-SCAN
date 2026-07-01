<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold t-size8 font-heading text-text flex items-center gap-2">
                    <span class="w-10 h-10 rounded-xl bg-primary-soft/40 flex items-center justify-center text-accent t-size5">📦</span>
                    {{ __('Tambah Stok') }}
                </h2>
                <p class="text-text-muted t-size3 mt-1">Tambah data stok baru ke dalam sistem inventori.</p>
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
        name: '{{ old('name', '') }}',
        unit: '{{ old('unit', 'pcs') }}',
        quantity: {{ old('quantity', 0) }},
        minQty: {{ old('minimum_quantity', 5) }}
    }">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 t-size2 text-text-muted font-semibold">
            <a href="{{ route('dashboard') }}" class="hover:text-text transition">Home</a>
            <span>›</span>
            <a href="{{ route('admin.stocks.index') }}" class="hover:text-text transition">Manajemen Stok</a>
            <span>›</span>
            <span class="text-text font-bold">Tambah Stok</span>
        </div>

        <form action="{{ route('admin.stocks.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                <!-- Left Side: Form Sections (col-span-2) -->
                <div class="lg:col-span-2 space-y-6">

                    {{-- Section 1: Informasi Stok --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs relative">
                        <div class="flex items-center gap-3 border-b border-border pb-3 mb-5">
                            <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center t-size2 font-bold font-heading">1</span>
                            <h3 class="font-bold t-size5 text-text font-heading">Informasi Stok</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Nama Bahan / Stok -->
                            <div class="space-y-1.5 md:col-span-2">
                                <label for="name" class="font-bold t-size3 text-text-muted">Nama Bahan / Stok <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" x-model="name" placeholder="Contoh: Daging Sapi Segar" required
                                    class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('name') border-danger @enderror">
                                @error('name')
                                    <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Kode Stok (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Kode Stok</label>
                                <input type="text" disabled placeholder="Contoh: DAG-SAPI-001"
                                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed">
                                <span class="t-size1 text-text-muted block">Akan otomatis dibuat jika kosong</span>
                            </div>

                            <!-- Kategori (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Kategori</label>
                                <select disabled
                                    class="w-full bg-surface border border-border rounded-xl px-3 py-2.5 t-size4 text-text-muted cursor-not-allowed">
                                    <option>Pilih kategori</option>
                                </select>
                            </div>

                            <!-- Cabang / Outlet (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Cabang / Outlet</label>
                                <select disabled
                                    class="w-full bg-surface border border-border rounded-xl px-3 py-2.5 t-size4 text-text-muted cursor-not-allowed">
                                    <option>Pilih outlet</option>
                                </select>
                            </div>

                            <!-- Satuan -->
                            <div class="space-y-1.5">
                                <label for="unit" class="font-bold t-size3 text-text-muted">Satuan <span class="text-danger">*</span></label>
                                <input type="text" id="unit" name="unit" x-model="unit" placeholder="Contoh: kg, pcs, pack" required
                                    class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('unit') border-danger @enderror">
                                @error('unit')
                                    <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Supplier (Visual only) -->
                            <div class="space-y-1.5 md:col-span-3">
                                <label class="font-bold t-size3 text-text-muted">Supplier</label>
                                <div class="flex gap-2">
                                    <select disabled
                                        class="w-full bg-surface border border-border rounded-xl px-3 py-2.5 t-size4 text-text-muted cursor-not-allowed">
                                        <option>Pilih supplier</option>
                                    </select>
                                    <button type="button" disabled
                                        class="bg-surface border border-border text-text-muted p-2.5 rounded-xl cursor-not-allowed">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Detail Persediaan --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs relative">
                        <div class="flex items-center gap-3 border-b border-border pb-3 mb-5">
                            <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center t-size2 font-bold font-heading">2</span>
                            <h3 class="font-bold t-size5 text-text font-heading">Detail Persediaan</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Jumlah Awal -->
                            <div class="space-y-1.5">
                                <label for="quantity" class="font-bold t-size3 text-text-muted">Jumlah Awal <span class="text-danger">*</span></label>
                                <input type="number" id="quantity" name="quantity" x-model.number="quantity" min="0" required
                                    class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('quantity') border-danger @enderror">
                                <span class="t-size1 text-text-muted block">Jumlah stok yang masuk</span>
                                @error('quantity')
                                    <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Harga Beli (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Harga Beli</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-text-muted font-bold t-size3">Rp</span>
                                    <input type="text" disabled placeholder="0"
                                        class="w-full bg-surface border border-border rounded-xl pl-10 pr-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed">
                                </div>
                                <span class="t-size1 text-text-muted block">Harga per satuan</span>
                            </div>

                            <!-- Total Nilai (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Total Nilai</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-text-muted font-bold t-size3">Rp</span>
                                    <input type="text" disabled placeholder="0"
                                        class="w-full bg-surface border border-border rounded-xl pl-10 pr-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed">
                                </div>
                                <span class="t-size1 text-text-muted block">Otomatis terhitung</span>
                            </div>

                            <!-- Tanggal Masuk (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="date" disabled value="{{ date('Y-m-d') }}"
                                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed">
                            </div>

                            <!-- Tanggal Kedaluwarsa (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Tanggal Kedaluwarsa</label>
                                <input type="date" disabled
                                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed">
                                <span class="t-size1 text-text-muted block">Kosongkan jika tidak ada batas</span>
                            </div>

                            <!-- Lokasi Penyimpanan (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Lokasi Penyimpanan</label>
                                <input type="text" disabled placeholder="Contoh: Gudang Utama - Rak 1"
                                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed">
                                <span class="t-size1 text-text-muted block">Lokasi penyimpanan stok</span>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Pengaturan Minimum --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs relative">
                        <div class="flex items-center gap-3 border-b border-border pb-3 mb-5">
                            <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center t-size2 font-bold font-heading">3</span>
                            <h3 class="font-bold t-size5 text-text font-heading">Pengaturan Minimum</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Batas Minimum -->
                            <div class="space-y-1.5">
                                <label for="minimum_quantity" class="font-bold t-size3 text-text-muted">Stok Minimum <span
                                        class="text-danger">*</span></label>
                                <input type="number" id="minimum_quantity" name="minimum_quantity" x-model.number="minQty" min="0" required
                                    class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('minimum_quantity') border-danger @enderror">
                                <span class="t-size1 text-text-muted block">Batas minimum sebelum stok menipis</span>
                                @error('minimum_quantity')
                                    <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Stok Maksimum (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Stok Maksimum (Opsional)</label>
                                <input type="number" disabled placeholder="0"
                                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed">
                                <span class="t-size1 text-text-muted block">Batas maksimum penyimpanan</span>
                            </div>

                            <!-- Status (Visual only) -->
                            <div class="space-y-1.5">
                                <label class="font-bold t-size3 text-text-muted">Status <span class="text-danger">*</span></label>
                                <div class="flex items-center gap-2 bg-surface border border-border rounded-xl px-3 py-2.5 text-text-muted">
                                    <span class="w-2.5 h-2.5 bg-success rounded-full"></span>
                                    <span class="font-semibold t-size3 text-text">Aktif</span>
                                </div>
                                <span class="t-size1 text-text-muted block">Non-aktifkan stok jika tidak digunakan</span>
                            </div>

                            {{-- Catatan (Visual only) --}}
                            <div class="space-y-1.5 md:col-span-3">
                                <label class="font-bold t-size3 text-text-muted">Catatan (Opsional)</label>
                                <textarea disabled rows="3" placeholder="Tambahkan catatan terkait stok ini..."
                                    class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden text-text-muted cursor-not-allowed"></textarea>
                                <span class="t-size1 text-text-muted block">Informasi tambahan tentang stok</span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2">
                        <a href="{{ route('admin.stocks.index') }}"
                            class="bg-card border border-border text-text-muted hover:text-text px-8 py-3 rounded-full transition cursor-pointer font-bold t-size4 text-center">
                            Batal
                        </a>
                        <button type="button" disabled
                            class="bg-card border border-border text-text-muted px-8 py-3 rounded-full font-bold t-size4 text-center cursor-not-allowed opacity-50">
                            Simpan Draft
                        </button>
                        <button type="submit"
                            class="bg-primary hover:bg-primary-strong text-white font-extrabold px-10 py-3 rounded-full transition shadow-xs cursor-pointer t-size4 text-center">
                            Simpan Stok
                        </button>
                    </div>

                </div>

                <!-- Right Side: Sidebar (col-span-1) -->
                <div class="space-y-6">

                    {{-- Ringkasan Stok --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs space-y-4">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3">
                            📋 Ringkasan Stok
                        </h3>

                        <div class="bg-surface rounded-2xl p-5 border border-border space-y-4 flex flex-col items-center">
                            <div class="w-32 h-32 bg-primary-soft/30 rounded-2xl flex items-center justify-center text-5xl">
                                🥣
                            </div>
                            <div class="w-full space-y-2.5 t-size3 border-t border-border/80 pt-4">
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Nama Stok</span>
                                    <span class="font-bold text-text truncate max-w-[150px]" x-text="name || '-'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Kode Stok</span>
                                    <span class="font-bold text-text">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Kategori</span>
                                    <span class="font-bold text-text">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Outlet</span>
                                    <span class="font-bold text-text">-</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Satuan</span>
                                    <span class="font-bold text-text" x-text="unit || '-'"></span>
                                </div>
                                <div class="flex justify-between border-t border-border/50 pt-2.5">
                                    <span class="text-text-muted">Jumlah Awal</span>
                                    <span class="font-bold text-accent" x-text="quantity + ' ' + unit"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Harga Beli</span>
                                    <span class="font-bold text-text">Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Total Nilai</span>
                                    <span class="font-bold text-text">Rp 0</span>
                                </div>
                                <div class="flex justify-between border-t border-border/50 pt-2.5">
                                    <span class="text-text-muted">Stok Minimum</span>
                                    <span class="font-bold text-text" x-text="minQty + ' ' + unit"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-text-muted">Status</span>
                                    <span class="bg-success/15 text-success border border-success/30 font-bold px-2 py-0.5 rounded-full t-size1">
                                        Aktif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tips Pengisian --}}
                    <div class="bg-card border border-border rounded-3xl p-6 shadow-xs space-y-4">
                        <h3 class="font-bold t-size4 text-text font-heading border-b border-border pb-3 flex items-center gap-2">
                            <span class="text-info">ℹ️</span> Tips Pengisian
                        </h3>

                        <ul class="space-y-3 t-size3 text-text-muted">
                            <li class="flex items-start gap-2.5">
                                <span class="text-success font-bold text-sm">✓</span>
                                <span>Pastikan nama stok jelas dan mudah diidentifikasi.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-success font-bold text-sm">✓</span>
                                <span>Gunakan kode stok yang unik dan konsisten.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-success font-bold text-sm">✓</span>
                                <span>Isi stok minimum untuk menghindari kehabisan stok.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-success font-bold text-sm">✓</span>
                                <span>Periksa tanggal kedaluwarsa untuk bahan mudah rusak.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-success font-bold text-sm">✓</span>
                                <span>Simpan sebagai draft jika belum yakin.</span>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>

        </form>
    </div>
</x-app-layout>
