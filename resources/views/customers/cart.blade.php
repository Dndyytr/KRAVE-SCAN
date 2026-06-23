<x-customer-layout :branch="$branch" :table="$table">
    <div x-data="{
        cart: {{ json_encode($cart) }},
        cartTotal: {{ (float) $cartTotal }},
        updateQty(menuId, newQty) {
            fetch('{{ route('customer.cart.update', ['branch_code' => $branch_code]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ menu_id: menuId, quantity: newQty })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (newQty <= 0) {
                        delete this.cart[menuId];
                    } else {
                        if (this.cart[menuId]) {
                            this.cart[menuId].quantity = newQty;
                        }
                    }
                    this.cartTotal = Number(data.cart_total);
                    // Dispatch event to update layout bottom nav badge
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                }
            })
            .catch(err => console.error('Gagal memperbarui keranjang:', err));
        },
        isEmpty() {
            return Object.keys(this.cart).length === 0;
        }
    }" class="space-y-6">

        <div class="flex items-center gap-3">
            <a href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table ?? 1]) }}" class="bg-card border border-border p-2 rounded-full text-text-muted hover:text-text hover:border-primary-soft transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="t-size6 font-extrabold font-heading text-accent">Keranjang Belanja</h2>
        </div>

        @if(session('error'))
            <div class="bg-danger/10 border border-danger/30 text-danger p-4 rounded-2xl t-size3 font-semibold">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-success/10 border border-success/30 text-success p-4 rounded-2xl t-size3 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Empty Cart State -->
        <div x-show="isEmpty()" class="bg-card border border-border rounded-3xl p-8 text-center space-y-4 shadow-xs" style="display: none;">
            <div class="w-16 h-16 bg-surface rounded-full flex items-center justify-center text-primary-soft mx-auto">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="font-bold t-size4">Keranjang Anda Kosong</h3>
                <p class="text-text-muted t-size2">Silakan pilih menu lezat kami terlebih dahulu.</p>
            </div>
            <a href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table ?? 1]) }}" 
               class="inline-block bg-primary hover:bg-primary-strong text-white font-bold px-6 py-2.5 rounded-full t-size3 transition shadow-xs">
                Lihat Menu
            </a>
        </div>

        <!-- Cart Content -->
        <div x-show="!isEmpty()" class="space-y-6" style="display: none;">
            <div class="space-y-4">
                <template x-for="item in Object.values(cart)" :key="item.id">
                    <div class="bg-card border border-border rounded-3xl p-4 flex gap-4 shadow-xs hover:border-primary-soft transition">
                        <div class="w-20 h-20 bg-surface rounded-2xl flex items-center justify-center font-bold text-primary-soft overflow-hidden shrink-0">
                            <template x-if="item.image_path">
                                <img :src="'/' + item.image_path" :alt="item.name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!item.image_path">
                                <span class="t-size1 uppercase text-center px-1" x-text="item.name.substring(0, 3)"></span>
                            </template>
                        </div>
                        <div class="flex-grow flex flex-col justify-between">
                            <div>
                                <h4 class="font-bold t-size4 text-text" x-text="item.name"></h4>
                                <span class="text-text-muted t-size2" x-text="'Rp ' + Number(item.price).toLocaleString('id-ID')"></span>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center gap-2 bg-surface border border-border rounded-full p-1">
                                    <button @click="updateQty(item.id, item.quantity - 1)" class="w-6 h-6 rounded-full bg-card flex items-center justify-center font-bold hover:bg-primary-soft hover:text-accent transition shadow-xs cursor-pointer">-</button>
                                    <span class="w-6 text-center font-bold t-size3 text-text" x-text="item.quantity"></span>
                                    <button @click="updateQty(item.id, item.quantity + 1)" class="w-6 h-6 rounded-full bg-card flex items-center justify-center font-bold hover:bg-primary-soft hover:text-accent transition shadow-xs cursor-pointer">+</button>
                                </div>
                                <span class="font-extrabold text-accent t-size4" x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Order Summary Card -->
            <div class="bg-surface border border-border rounded-3xl p-5 space-y-4">
                <h3 class="font-bold t-size4 font-heading text-accent border-b border-border pb-2">Ringkasan Pesanan</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-text-muted t-size3">
                        <span>Nomor Meja</span>
                        <span class="font-bold text-text">Meja {{ $table ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between text-text-muted t-size3">
                        <span>Cabang</span>
                        <span class="font-bold text-text">{{ $branch }}</span>
                    </div>
                    <div class="flex justify-between border-t border-border pt-3">
                        <span class="font-bold text-text t-size4">Total Pembayaran</span>
                        <span class="font-extrabold text-accent t-size5" x-text="'Rp ' + cartTotal.toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>

            <!-- Checkout Form -->
            <form action="{{ route('customer.checkout', ['branch_code' => $branch_code]) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-primary hover:bg-primary-strong text-white font-extrabold py-3.5 rounded-3xl t-size4 transition shadow-md hover:shadow-lg flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pesan Sekarang
                </button>
            </form>
        </div>

    </div>
</x-customer-layout>
