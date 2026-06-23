<x-customer-layout :branch="$branch" :table="$table">
    <div x-data="{
        activeCategory: 'all',
        search: '',
        showToast: false,
        toastMessage: '',
        isUploading: false,
        triggerToast(message) {
            this.toastMessage = message;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3500);
        },
        addToCart(menuId) {
            fetch('{{ route('customer.cart.add', ['branch_code' => $branch_code]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ menu_id: menuId, quantity: 1 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.triggerToast(data.message);
                    // Dispatch event to update the cart badge in bottom nav
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                } else {
                    this.triggerToast('Gagal menambahkan item.');
                }
            })
            .catch(err => {
                console.error(err);
                this.triggerToast('Terjadi kesalahan koneksi.');
            });
        },
        uploadImage(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.isUploading = true;
            const formData = new FormData();
            formData.append('image', file);

            fetch('{{ route('customer.menu.identify', ['branch_code' => $branch_code]) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw new Error(err.message || 'Layanan AI sedang gangguan.'); });
                }
                return res.json();
            })
            .then(data => {
                this.isUploading = false;
                if (data.success) {
                    this.search = data.menu_name;
                    this.triggerToast(`Menu terdeteksi: ${data.menu_name} (${Math.round(data.confidence * 100)}% akurasi)`);
                } else {
                    this.triggerToast(data.message || 'Menu tidak berhasil dikenali.');
                }
            })
            .catch(err => {
                this.isUploading = false;
                console.error(err);
                this.triggerToast(err.message || 'Terjadi kesalahan saat memproses gambar.');
            })
            .finally(() => {
                event.target.value = '';
            });
        }
    }" class="space-y-6">

        <!-- Hero Search Section -->
        <div class="bg-surface rounded-3xl p-6 border border-border relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-24 h-24 rounded-full bg-primary-soft/30"></div>
            
            <h2 class="t-size7 font-extrabold font-heading text-accent mb-2">Mau makan apa hari ini?</h2>
            <p class="t-size3 text-text-muted mb-4 font-semibold">Temukan menu favorit Anda atau cari dengan foto makanan!</p>
            
            <div class="flex gap-2">
                <input type="text" x-model="search" placeholder="Cari kopi, donat, nasi..." class="flex-grow bg-card border border-border focus:border-primary focus:ring-1 focus:ring-primary rounded-2xl px-4 py-2.5 t-size4 outline-hidden transition">
                <button @click="$refs.cameraInput.click()" type="button" class="bg-primary hover:bg-primary-strong text-white font-bold p-2.5 rounded-2xl flex items-center justify-center transition shadow-xs cursor-pointer" title="Cari dengan Foto Makanan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
                <input type="file" x-ref="cameraInput" accept="image/*" class="hidden" @change="uploadImage($event)">
            </div>
        </div>

        <!-- Categories horizontal scroll -->
        <div>
            <h3 class="t-size5 font-bold mb-3 font-heading">Kategori</h3>
            <div class="flex gap-2 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-none">
                <button @click="activeCategory = 'all'" 
                        :class="activeCategory === 'all' ? 'bg-primary text-white font-bold' : 'bg-card border border-border text-text-muted hover:text-text font-semibold'"
                        class="px-4 py-2 rounded-full t-size4 transition shrink-0 cursor-pointer">
                    Semua
                </button>
                @foreach($categories as $category)
                    <button @click="activeCategory = '{{ $category->id }}'" 
                            :class="activeCategory === '{{ $category->id }}' ? 'bg-primary text-white font-bold' : 'bg-card border border-border text-text-muted hover:text-text font-semibold'"
                            class="px-4 py-2 rounded-full t-size4 transition shrink-0 cursor-pointer">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Menu Grid -->
        <div>
            <h3 class="t-size5 font-bold mb-4 font-heading">Pilihan Menu</h3>
            <div class="grid grid-cols-2 gap-4">
                @forelse($menus as $menu)
                    <div x-show="(activeCategory === 'all' || activeCategory === '{{ $menu->category_id }}') && (search === '' || '{{ strtolower($menu->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($menu->description) }}'.includes(search.toLowerCase()))"
                         class="bg-card border border-border rounded-3xl p-3 flex flex-col justify-between shadow-xs hover:border-primary-soft transition">
                        <div class="aspect-square bg-surface rounded-2xl mb-3 flex items-center justify-center text-primary-soft font-bold relative overflow-hidden">
                            @if($menu->image_path)
                                <img src="{{ asset($menu->image_path) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="t-size2 uppercase text-center px-2">{{ $menu->name }}</span>
                            @endif
                        </div>
                        <div class="flex-grow flex flex-col justify-between">
                            <div>
                                <span class="bg-primary-soft/50 text-accent font-semibold px-2 py-0.5 rounded-full text-[10px]">{{ $menu->category->name }}</span>
                                <h4 class="font-bold t-size4 mt-1.5 line-clamp-1">{{ $menu->name }}</h4>
                                <p class="text-text-muted t-size2 mt-0.5 line-clamp-2">{{ $menu->description }}</p>
                            </div>
                            <div class="flex items-center justify-between mt-3">
                                <span class="font-extrabold text-accent t-size4">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                <button @click="addToCart({{ $menu->id }})" class="bg-primary hover:bg-primary-strong text-white font-bold w-8 h-8 rounded-full flex items-center justify-center transition shadow-xs cursor-pointer">+</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-12 text-text-muted">
                        <p class="font-semibold">Tidak ada menu yang tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Toast Notification -->
        <div x-show="showToast" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed bottom-20 left-1/2 transform -translate-x-1/2 z-50 bg-accent text-white px-4 py-2.5 rounded-2xl shadow-lg t-size3 font-semibold flex items-center gap-2 border border-primary-strong"
             style="display: none;">
            <svg class="w-5 h-5 text-primary-soft" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span x-text="toastMessage"></span>
        </div>

        <!-- Loading Overlay -->
        <div x-show="isUploading" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center" 
             style="display: none;">
            <div class="bg-card rounded-3xl p-6 max-w-xs w-full text-center border border-border shadow-lg space-y-4">
                <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto"></div>
                <div>
                    <p class="font-bold text-accent t-size4">Menganalisis Foto...</p>
                    <p class="text-text-muted t-size2 mt-1">Mencocokkan dengan menu lezat kami</p>
                </div>
            </div>
        </div>
        
    </div>
</x-customer-layout>
