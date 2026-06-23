<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold t-size7 font-heading text-text">
            {{ __('Edit Stok Barang') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6 anim-fade">
        <!-- Back Link -->
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.stocks.index') }}" class="text-text-muted hover:text-text font-bold t-size3 transition flex items-center gap-1">
                &larr; Kembali ke Daftar Stok
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-card border border-border rounded-2xl p-6 shadow-xs">
            <form action="{{ route('admin.stocks.update', $stock->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="space-y-4">
                    <h3 class="font-bold t-size5 text-text font-heading border-b border-border pb-2">
                        Informasi Stok Inventaris
                    </h3>

                    <div class="space-y-4">
                        <!-- Item Name -->
                        <div class="space-y-1.5">
                            <label for="name" class="font-bold t-size3 text-text-muted">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $stock->name) }}" placeholder="Contoh: Gelas Plastik, Susu UHT, Kopi Arabika" required
                                class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('name') border-danger @enderror">
                            @error('name')
                                <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Quantity -->
                            <div class="space-y-1.5">
                                <label for="quantity" class="font-bold t-size3 text-text-muted">Jumlah Stok <span class="text-danger">*</span></label>
                                <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $stock->quantity) }}" min="0" required
                                    class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('quantity') border-danger @enderror">
                                @error('quantity')
                                    <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Minimum Quantity -->
                            <div class="space-y-1.5">
                                <label for="minimum_quantity" class="font-bold t-size3 text-text-muted">Batas Minimum <span class="text-danger">*</span></label>
                                <input type="number" id="minimum_quantity" name="minimum_quantity" value="{{ old('minimum_quantity', $stock->minimum_quantity) }}" min="0" required
                                    class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('minimum_quantity') border-danger @enderror">
                                @error('minimum_quantity')
                                    <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Unit -->
                            <div class="space-y-1.5">
                                <label for="unit" class="font-bold t-size3 text-text-muted">Satuan <span class="text-danger">*</span></label>
                                <input type="text" id="unit" name="unit" value="{{ old('unit', $stock->unit) }}" placeholder="Contoh: pcs, pack, kg, liter" required
                                    class="w-full bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-xl px-4 py-2.5 t-size4 outline-hidden transition @error('unit') border-danger @enderror">
                                @error('unit')
                                    <span class="text-danger t-size2 font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Block -->
                <div class="flex justify-end gap-3 pt-4 border-t border-border">
                    <a href="{{ route('admin.stocks.index') }}" class="bg-surface border border-border text-text-muted hover:text-text px-6 py-2.5 rounded-xl transition cursor-pointer font-bold t-size4">
                        Batal
                    </a>
                    <button type="submit" class="bg-primary hover:bg-primary-strong text-white font-bold px-6 py-2.5 rounded-xl transition shadow-xs cursor-pointer t-size4">
                        Perbarui Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
