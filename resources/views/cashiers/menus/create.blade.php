<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Tambah Menu Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6 anim-fade">
        <!-- Back Link -->
        <div class="flex justify-between items-center">
            <a href="{{ route('cashier.menus.index') }}" class="text-text-muted hover:text-text font-bold t-size3 transition flex items-center gap-1">
                &larr; Kembali ke Daftar Menu
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
            <form action="{{ route('cashier.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Basic Information Section -->
                <div class="space-y-4">
                    <h3 class="font-bold t-size5 text-text font-heading border-b border-border pb-2">
                        Informasi Menu Makanan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Menu Name -->
                        <div class="space-y-1.5">
                            <label for="name" class="font-bold t-size3 text-text-muted">Nama Menu <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Cappuccino Latte" required
                                class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('name') border-danger @enderror">
                            @error('name')
                                <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="space-y-1.5">
                            <label for="category_id" class="font-bold t-size3 text-text-muted">Kategori Menu <span class="text-danger">*</span></label>
                            <select id="category_id" name="category_id" required
                                class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer @error('category_id') border-danger @enderror">
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Price -->
                        <div class="space-y-1.5">
                            <label for="price" class="font-bold t-size3 text-text-muted">Harga Menu (Rupiah) <span class="text-danger">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-text-muted font-bold t-size4">Rp</span>
                                <input type="number" id="price" name="price" value="{{ old('price') }}" placeholder="Contoh: 28000" min="0"
                                    required
                                    class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl pl-11 pr-4 py-2.5 t-size4 outline-hidden transition @error('price') border-danger @enderror">
                            </div>
                            @error('price')
                                <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status Aktif -->
                        <div class="space-y-1.5">
                            <label class="font-bold t-size3 text-text-muted block">Status Ketersediaan</label>
                            <div class="flex items-center h-11">
                                <label class="inline-flex items-center cursor-pointer gap-3">
                                    <div class="relative">
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-border peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                        </div>
                                    </div>
                                    <span class="font-semibold text-text t-size4">Aktif (Tersedia untuk dipesan pelanggan)</span>
                                </label>
                            </div>
                            @error('is_active')
                                <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Hubungkan ke Stok Barang (Opsional) -->
                    <div class="space-y-1.5">
                        <label for="stock_item_id" class="font-bold t-size3 text-text-muted font-heading">Hubungkan ke Stok Barang (Opsional)</label>
                        <select id="stock_item_id" name="stock_item_id"
                            class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer @error('stock_item_id') border-danger @enderror">
                            <option value="">-- Tidak Terhubung ke Stok --</option>
                            @foreach ($stocks as $stock)
                                <option value="{{ $stock->id }}" {{ old('stock_item_id') == $stock->id ? 'selected' : '' }}>
                                    {{ $stock->name }} (Tersedia: {{ $stock->quantity }} {{ $stock->unit }})
                                </option>
                            @endforeach
                        </select>
                        @error('stock_item_id')
                            <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="space-y-1.5">
                        <label for="description" class="font-bold t-size3 text-text-muted">Deskripsi Menu</label>
                        <textarea id="description" name="description" rows="3" placeholder="Masukkan deskripsi detail mengenai hidangan menu ini..."
                            class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Image Upload -->
                    <div class="space-y-2">
                        <label class="font-bold t-size3 text-text-muted block">Foto Hidangan</label>
                        <div x-data="{
                            previewUrl: null,
                            fileChosen(event) {
                                const file = event.target.files[0];
                                if (file) {
                                    this.previewUrl = URL.createObjectURL(file);
                                }
                            }
                        }" class="flex flex-col sm:flex-row items-start gap-4">
                            <!-- Image preview -->
                            <div class="w-32 h-32 bg-surface border border-border rounded-2xl flex items-center justify-center overflow-hidden shrink-0">
                                <template x-if="previewUrl">
                                    <img :src="previewUrl" alt="Preview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!previewUrl">
                                    <svg class="w-8 h-8 text-text-muted/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </template>
                            </div>
                            <!-- Input -->
                            <div class="space-y-1.5">
                                <input type="file" name="image" id="image" accept="image/*" @change="fileChosen"
                                    class="text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-soft/40 file:text-accent hover:file:bg-primary-soft/60 file:cursor-pointer cursor-pointer">
                                <p class="text-text-muted text-[11px] font-semibold">Format gambar: JPEG, PNG, JPG, WEBP. Ukuran maks 2MB.</p>
                                @error('image')
                                    <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-3 pt-6 border-t border-border">
                    <a href="{{ route('cashier.menus.index') }}"
                        class="bg-surface border border-border text-text-muted hover:text-text px-6 py-2.5 rounded-xl transition cursor-pointer font-semibold t-size4">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-primary hover:bg-primary-strong text-white font-bold px-8 py-2.5 rounded-xl transition shadow-xs cursor-pointer">
                        Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
