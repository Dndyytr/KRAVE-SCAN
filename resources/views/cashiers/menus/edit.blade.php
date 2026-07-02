<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 t-size3 text-text-muted">
            <a href="{{ route('cashier.menus.index') }}" class="hover:text-text transition">Menu</a>
            <span>›</span>
            <span class="text-text font-bold">Edit Menu</span>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6 anim-fade">

        <form action="{{ route('cashier.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Card 1: Informasi Menu --}}
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs mb-6">
                <h3 class="font-bold t-size5 text-text font-heading border-b border-border pb-3 mb-5 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-primary-soft/40 flex items-center justify-center text-accent t-size3">📋</span>
                    Informasi Menu
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nama Menu --}}
                    <div class="space-y-1.5">
                        <label for="name" class="font-bold t-size3 text-text-muted">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $menu->name) }}" placeholder="Contoh: Bakso Urat Spesial"
                            required
                            class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('name') border-danger @enderror">
                        @error('name')
                            <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="space-y-1.5">
                        <label for="category_id" class="font-bold t-size3 text-text-muted">Kategori <span class="text-danger">*</span></label>
                        <select id="category_id" name="category_id" required
                            class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer @error('category_id') border-danger @enderror">
                            <option value="" disabled>Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="space-y-1.5 mt-5" x-data="{ charCount: {{ strlen(old('description', $menu->description ?? '')) }}, maxChars: 200 }">
                    <label for="description" class="font-bold t-size3 text-text-muted">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" :maxlength="maxChars"
                        placeholder="Bakso urat sapi dengan tekstur kenyal, disajikan dengan kuah kaldu gurih dan pelengkap spesial." @input="charCount = $el.value.length"
                        class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('description') border-danger @enderror">{{ old('description', $menu->description) }}</textarea>
                    <div class="flex justify-end">
                        <span class="t-size1 text-text-muted" x-text="charCount + '/' + maxChars"></span>
                    </div>
                    @error('description')
                        <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Card 2: Harga & Kategori --}}
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs mb-6">
                <h3 class="font-bold t-size5 text-text font-heading border-b border-border pb-3 mb-5 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-primary-soft/40 flex items-center justify-center text-accent t-size3">💰</span>
                    Harga & Kategori
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Harga --}}
                    <div class="space-y-1.5">
                        <label for="price" class="font-bold t-size3 text-text-muted">Harga <span class="text-danger">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-text-muted font-bold t-size4">Rp</span>
                            <input type="number" id="price" name="price" value="{{ old('price', (int) $menu->price) }}" placeholder="25.000"
                                min="0" required
                                class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl pl-11 pr-4 py-2.5 t-size4 outline-hidden transition @error('price') border-danger @enderror">
                        </div>
                        @error('price')
                            <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Stok Terkait --}}
                    <div class="space-y-1.5">
                        <label for="stock_item_id" class="font-bold t-size3 text-text-muted">Stok Terkait</label>
                        <select id="stock_item_id" name="stock_item_id"
                            class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-3 py-2.5 t-size4 outline-hidden transition cursor-pointer @error('stock_item_id') border-danger @enderror">
                            <option value="">-- Tidak Terhubung ke Stok --</option>
                            @foreach ($stocks as $stock)
                                <option value="{{ $stock->id }}" {{ old('stock_item_id', $menu->stock_item_id) == $stock->id ? 'selected' : '' }}>
                                    {{ $stock->name }} (Sisa: {{ $stock->quantity }} {{ $stock->unit }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-text-muted t-size1">Jika dihubungkan, stok barang ini akan berkurang otomatis
                            ketika ada pelanggan yang membeli menu ini
                            dan status pembayaran dikonfirmasi.</p>
                        @error('stock_item_id')
                            <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    {{-- Estimasi Waktu (UI placeholder) --}}
                    <div class="space-y-1.5">
                        <label class="font-bold t-size3 text-text-muted">Estimasi Waktu (Opsional)</label>
                        <div class="relative">
                            <input type="number" disabled placeholder="15" min="1"
                                class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden transition text-text-muted cursor-not-allowed pr-16">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-text-muted font-semibold t-size3">Menit</span>
                        </div>
                        <p class="t-size1 text-text-muted">Fitur akan tersedia dalam pembaruan mendatang.</p>
                    </div>

                    {{-- Label Promo (UI placeholder) --}}
                    <div class="space-y-1.5">
                        <label class="font-bold t-size3 text-text-muted">Label Promo (Opsional)</label>
                        <input type="text" disabled placeholder="Contoh: Recommended"
                            class="w-full bg-surface border border-border rounded-xl px-4 py-2.5 t-size4 outline-hidden transition text-text-muted cursor-not-allowed">
                        <p class="t-size1 text-text-muted">Fitur akan tersedia dalam pembaruan mendatang.</p>
                    </div>
                </div>
            </div>

            {{-- Card 3: Foto Menu --}}
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs mb-6">
                <h3 class="font-bold t-size5 text-text font-heading border-b border-border pb-3 mb-5 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-primary-soft/40 flex items-center justify-center text-accent t-size3">📷</span>
                    Foto Menu
                </h3>

                <div x-data="{
                    imagePreview: '{{ $menu->image_path ? (Str::startsWith($menu->image_path, ['http://', 'https://']) ? $menu->image_path : (Str::startsWith($menu->image_path, 'storage/') ? asset($menu->image_path) : asset('storage/' . $menu->image_path))) : '' }}',
                    fileName: null,
                    handleFileChange(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.imagePreview = e.target.result;
                            };
                            reader.readAsDataURL(file);
                            this.fileName = file.name;
                        }
                    },
                    removeImage() {
                        this.imagePreview = null;
                        this.fileName = null;
                        document.getElementById('image-input').value = '';
                    }
                }" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Upload Area --}}
                    <div class="space-y-3">
                        <p class="t-size3 text-text-muted">Upload foto menu terbaik dengan background pokok terang.</p>
                        <label
                            class="relative flex flex-col items-center justify-center border-2 border-dashed border-border rounded-2xl p-6 hover:border-primary transition cursor-pointer bg-surface-alt/20 min-h-[140px]">
                            <input type="file" id="image-input" name="image" accept="image/*" @change="handleFileChange($event)"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <svg class="w-8 h-8 text-primary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                </path>
                            </svg>
                            <span class="font-bold t-size3 text-primary">Upload Foto</span>
                            <span x-show="fileName" x-text="fileName" class="t-size2 text-text-muted mt-1 truncate max-w-full"></span>
                        </label>
                        <p class="text-text-muted t-size1 font-semibold">Format: JPG, PNG, WEBP. Ukuran maks 2MB.</p>
                        @error('image')
                            <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Preview Menu Card --}}
                    <div class="flex flex-col items-center">
                        <p class="t-size3 text-text-muted font-semibold mb-3 self-start">Preview Menu</p>
                        <div class="bg-card border border-border rounded-2xl overflow-hidden shadow-xs w-full max-w-[220px]">
                            <div class="w-full h-36 bg-surface flex items-center justify-center overflow-hidden relative">
                                <template x-if="imagePreview">
                                    <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!imagePreview">
                                    <svg class="w-12 h-12 text-text-muted/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </template>
                                {{-- Remove button --}}
                                <button x-show="imagePreview" type="button" @click.prevent="removeImage()"
                                    class="absolute top-2 right-2 bg-danger hover:bg-danger/80 text-white rounded-full p-1 shadow-md transition cursor-pointer z-10">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="p-3 space-y-1">
                                <p class="font-bold t-size3 text-text truncate">{{ $menu->name }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold text-accent t-size4">Rp
                                        {{ number_format($menu->price, 0, ',', '.') }}</span>
                                    <span
                                        class="{{ $menu->is_active ? 'bg-success/15 text-success border-success/30' : 'bg-danger/15 text-danger border-danger/30' }} t-size1 font-bold px-2 py-0.5 rounded-full border">
                                        {{ $menu->is_active ? 'Tersedia' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Status Penjualan --}}
            <div class="bg-card border border-border rounded-2xl p-6 shadow-xs mb-6">
                <h3 class="font-bold t-size5 text-text font-heading border-b border-border pb-3 mb-5 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-primary-soft/40 flex items-center justify-center text-accent t-size3">🏷️</span>
                    Status Penjualan
                </h3>

                <div class="space-y-1.5 max-w-sm">
                    <label class="font-bold t-size3 text-text-muted block">Status</label>
                    <div class="flex items-center gap-3 bg-surface border border-border rounded-xl px-4 py-3">
                        <label class="inline-flex items-center cursor-pointer gap-3 w-full">
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $menu->is_active ? '1' : '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-border peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-success">
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $menu->is_active ? 'bg-success' : 'bg-danger' }}"></span>
                                <span class="font-semibold text-text t-size4">{{ $menu->is_active ? 'Tersedia' : 'Nonaktif' }}</span>
                            </div>
                        </label>
                    </div>
                    @error('is_active')
                        <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-2 pb-6">
                <a href="{{ route('cashier.menus.index') }}"
                    class="bg-card border border-border text-text-muted hover:text-text px-8 py-3 rounded-full transition cursor-pointer font-bold t-size4 text-center">
                    Batal
                </a>
                <button type="submit"
                    class="bg-primary hover:bg-primary-strong text-white font-extrabold px-10 py-3 rounded-full transition shadow-xs cursor-pointer t-size4 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
