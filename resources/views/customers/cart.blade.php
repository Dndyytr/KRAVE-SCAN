<x-customer-layout :branch="$branch" :table="$table" :immersive="true">
    @php
        $tableLabel = preg_match('/^[A-Za-z]/', (string) $table) ? $table : 'A' . str_pad($table, 2, '0', STR_PAD_LEFT);
    @endphp

    <div x-data="{
        cart: {{ Js::from($cart) }},
        cartTotal: {{ (float) $cartTotal }},
        cartCount: {{ $cartCount }},
        busy: false,
        message: '',
        isEmpty() { return Object.keys(this.cart).length === 0 },
        async updateQty(cartKey, quantity) {
            if (this.busy) return;
            this.busy = true;
            try {
                const response = await fetch('{{ route('customer.cart.update', ['branch_code' => $branch_code]) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ cart_key: cartKey, quantity })
                });
                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.message || 'Keranjang gagal diperbarui.');
                if (quantity <= 0) delete this.cart[cartKey];
                else this.cart[cartKey].quantity = quantity;
                this.cartTotal = Number(data.cart_total);
                this.cartCount = Number(data.cart_count);
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: this.cartCount } }));
            } catch (error) {
                this.message = error.message;
                setTimeout(() => this.message = '', 3500);
            } finally { this.busy = false }
        }
    }" class="min-h-screen bg-[radial-gradient(circle_at_50%_0,#f7dfdd,#fff9f7_48%,#fffdf9)] lg:p-[18px]">
        <div
            class="mx-auto min-h-screen w-full max-w-350 overflow-hidden bg-white lg:grid lg:h-[calc(100vh-36px)] lg:min-h-0 lg:overflow-hidden lg:grid-cols-[238px_minmax(0,1fr)] lg:rounded-[25px] lg:shadow-[0_10px_35px_rgba(130,73,73,.14)]">
            <aside
                class="relative hidden overflow-hidden border-r border-[#f4e5e0] bg-[linear-gradient(160deg,#fffaf7,#fff5f0)] px-[22px] pb-7 pt-[30px] lg:flex lg:h-full lg:flex-col lg:overflow-y-auto lg:rounded-l-[25px]">
                <div class="absolute left-6.5 top-6.5 grid grid-cols-4 gap-3" aria-hidden="true">
                    @for ($i = 0; $i < 16; $i++)
                        <i class="size-1.25 rounded-full bg-[#f17996]"></i>
                    @endfor
                </div>
                <div class="mb-8.75 mt-17 flex flex-col items-center">
                    <img src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt="" class="size-19.5">
                    <strong class="-mt-2.75 font-brand text-[28px] font-extrabold italic leading-none tracking-[-.055em] text-[#e95578]">Bakso Cinta</strong>
                    <span class="mt-2 text-[8px] font-bold tracking-[.48em] text-[#ad8a6d]">— CIAMIS —</span>
                </div>
                <div class="grid grid-cols-[48px_1fr] items-center rounded-[15px] bg-white/80 px-3.5 py-[10px] shadow-[0_5px_20px_rgba(174,111,112,.07)]">
                    <span class="grid size-11 place-items-center rounded-full bg-[#fff0ef] text-primary">
                        <svg width="26" height="26" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="16" cy="7" r="3" />
                            <path d="M10 14v11M22 14v11M8 17h16v6H8zM5 18v9M27 18v9M9 27h4M19 27h4" />
                        </svg>
                    </span>
                    <span><small class="block text-[10px] text-[#706865]">Meja Anda</small><strong
                            class="font-heading text-[23px] font-bold leading-tight text-primary">{{ $tableLabel }}</strong></span>
                </div>
                <nav class="mt-[22px] grid gap-[7px]" aria-label="Navigasi pelanggan">
                    <a href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table]) }}"
                        class="flex min-h-11 items-center gap-3 rounded-[9px] px-[14px] text-[11px] font-semibold text-[#4c403e] transition duration-200 hover:bg-[#fff0f0] hover:text-primary"><svg
                            class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 17h16M6 17a6 6 0 0 1 12 0M12 8v3M3 20h18" />
                        </svg>Menu</a>
                    <a href="{{ route('customer.ai-scan', ['branch_code' => $branch_code]) }}"
                        class="flex min-h-11 items-center gap-3 rounded-[9px] px-[14px] text-[11px] font-semibold text-[#4c403e] transition duration-200 hover:bg-[#fff0f0] hover:text-primary"><svg
                            class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h4l1.5-2h5L16 7h4v12H4z" />
                            <circle cx="12" cy="13" r="3" />
                        </svg>AI Scan</a>
                    <a href="{{ route('customer.cart', ['branch_code' => $branch_code]) }}"
                        class="flex min-h-11 items-center gap-3 rounded-[9px] bg-[linear-gradient(100deg,#f54772,#fc708d)] px-[14px] text-[11px] font-semibold text-white shadow-[0_8px_18px_rgba(242,76,114,.18)]"><svg
                            class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 4h2l2.2 10.5h10.9L21 7H6M9 20h.01M18 20h.01" />
                        </svg>Keranjang <span x-show="cartCount" x-text="cartCount"
                            class="ml-auto grid min-w-[19px] place-items-center rounded-full bg-white px-1 text-[9px] text-primary"></span></a>
                    <a href="{{ route('customer.payment', ['branch_code' => $branch_code]) }}"
                        class="flex min-h-11 items-center gap-3 rounded-[9px] px-[14px] text-[11px] font-semibold text-[#4c403e] transition duration-200 hover:bg-[#fff0f0] hover:text-primary"><svg
                            class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3z" />
                        </svg>Pembayaran</a>
                    <a href="{{ route('customer.order.status', ['branch_code' => $branch_code]) }}"
                        class="flex min-h-11 items-center gap-3 rounded-[9px] px-[14px] text-[11px] font-semibold text-[#4c403e] transition duration-200 hover:bg-[#fff0f0] hover:text-primary"><svg
                            class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M7 3h10v18H7zM10 8h4M10 12h4" />
                        </svg>Pesanan</a>
                </nav>
                <div
                    class="mt-auto rounded-[15px] bg-[linear-gradient(120deg,#fff0ee,#fde2e0)] px-[15px] py-[18px] text-center text-[10px] leading-relaxed text-[#706765]">
                    <svg class="mx-auto mb-2 size-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 13v-2a8 8 0 0 1 16 0v2M4 13h3v6H5a1 1 0 0 1-1-1v-5ZM20 13h-3v6h2a1 1 0 0 0 1-1v-5Z" />
                    </svg><strong>Butuh bantuan?</strong><br>Hubungi staf kami jika ada kendala.
                </div>
            </aside>

            <main class="min-w-0 px-3 pb-28 pt-3 bp360:px-4 md:px-7 md:pb-8 md:pt-6 lg:px-8 lg:pb-8 lg:pt-8 lg:h-full lg:overflow-y-auto">
                <header class="mb-5 flex items-center justify-between lg:hidden">
                    <a href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table]) }}"
                        class="grid size-9 place-items-center rounded-full border border-border bg-white" aria-label="Kembali ke menu">←</a>
                    <div class="flex items-center"><img src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt="" class="size-11"><span
                            class="-ml-1 font-brand text-lg font-extrabold italic text-primary">Bakso Cinta</span></div>
                    <span class="rounded-[10px] border border-border bg-white px-3 py-2 text-center text-[9px] text-text-muted">Meja<br><b
                            class="text-sm text-primary">{{ $tableLabel }}</b></span>
                </header>

                <div class="flex items-start justify-between gap-5">
                    <div>
                        <p class="text-xs font-semibold text-primary">Keranjang Anda ❧</p>
                        <h1 class="mt-1 font-heading text-2xl font-extrabold tracking-tight text-text md:text-3xl">Pesanan Anda</h1>
                        <p class="mt-2 text-[11px] text-text-muted md:text-xs">Periksa kembali pesanan Anda sebelum checkout.</p>
                    </div>
                    <x-customer.language-selector class="hidden lg:block" />
                </div>

                @if (session('error'))
                    <div class="mt-4 rounded-xl border border-danger/30 bg-danger/10 p-3 text-xs font-semibold text-danger">{{ session('error') }}</div>
                @endif
                @if (session('success'))
                    <div class="mt-4 rounded-xl border border-success/30 bg-success/10 p-3 text-xs font-semibold text-success">{{ session('success') }}</div>
                @endif
                <div x-cloak x-show="message" x-text="message"
                    class="mt-4 rounded-xl border border-danger/30 bg-danger/10 p-3 text-xs font-semibold text-danger"></div>

                <section x-show="isEmpty()" x-cloak class="mt-7 rounded-3xl border border-border bg-white p-10 text-center shadow-sm">
                    <span class="mx-auto grid size-16 place-items-center rounded-full bg-surface text-primary"><svg class="size-8" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg></span>
                    <h2 class="mt-4 font-heading text-lg font-bold">Keranjang Anda Kosong</h2>
                    <p class="mt-1 text-xs text-text-muted">Silakan pilih menu terlebih dahulu.</p>
                    <a href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table]) }}"
                        class="mt-5 inline-flex rounded-full bg-primary px-6 py-3 text-xs font-bold text-white">Lihat Menu</a>
                </section>

                <div x-show="!isEmpty()" x-cloak>
                    <section class="mt-6 overflow-hidden rounded-2xl border border-[#f0e5e2] bg-white shadow-[0_6px_22px_rgba(113,74,69,.08)]">
                        <div
                            class="hidden grid-cols-[minmax(0,1.8fr)_110px_120px_110px_42px] gap-3 border-b border-border px-5 py-4 text-[10px] font-bold text-text-muted lg:grid">
                            <span>Menu</span><span>Harga</span><span class="text-center">Jumlah</span><span class="text-right">Subtotal</span><span></span>
                        </div>
                        <div class="divide-y divide-border">
                            <template x-for="item in Object.values(cart)" :key="item.cart_key">
                                <article
                                    class="grid grid-cols-[76px_minmax(0,1fr)] gap-3 p-3 bp360:grid-cols-[84px_minmax(0,1fr)] md:grid-cols-[100px_minmax(0,1fr)] md:p-4 lg:grid-cols-[minmax(0,1.8fr)_110px_120px_110px_42px] lg:items-center lg:px-5 lg:py-4">
                                    <div class="col-span-2 flex min-w-0 gap-3 lg:col-span-1 lg:items-center">
                                        <div
                                            class="size-[76px] shrink-0 overflow-hidden rounded-xl bg-surface bp360:size-[84px] md:size-[100px] lg:size-[82px]">
                                            <template x-if="item.image_path"><img
                                                    :src="item.image_path.startsWith('http') ? item.image_path : '/' + item.image_path" :alt="item.name"
                                                    class="size-full object-cover"></template>
                                            <template x-if="!item.image_path"><span
                                                    class="grid size-full place-items-center font-heading text-2xl font-extrabold text-primary"
                                                    x-text="item.name.substring(0,1)"></span></template>
                                        </div>
                                        <div class="min-w-0 py-1">
                                            <h3 class="truncate font-heading text-xs font-bold text-text md:text-sm" x-text="item.name"></h3>
                                            <p class="mt-1 text-[9px] text-text-muted lg:hidden" x-text="'Rp ' + Number(item.price).toLocaleString('id-ID')">
                                            </p><template x-if="item.note">
                                                <p class="mt-2 line-clamp-2 text-[8px] font-semibold text-primary"><span>Catatan: </span><span
                                                        x-text="item.note"></span></p>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-span-2 flex items-center justify-between gap-2 lg:contents">
                                        <span class="hidden text-[11px] font-bold lg:block"
                                            x-text="'Rp ' + Number(item.price).toLocaleString('id-ID')"></span>
                                        <div class="grid grid-cols-[32px_38px_32px] overflow-hidden rounded-[10px] border border-[#f3ccd4] bg-white"><button
                                                type="button" :disabled="busy" @click="updateQty(item.cart_key,item.quantity-1)"
                                                class="grid h-8 place-items-center text-base text-primary disabled:opacity-40"
                                                aria-label="Kurangi jumlah">−</button><span
                                                class="grid h-8 place-items-center border-x border-[#f5e1e5] text-[11px] font-bold"
                                                x-text="item.quantity"></span><button type="button" :disabled="busy"
                                                @click="updateQty(item.cart_key,item.quantity+1)"
                                                class="grid h-8 place-items-center text-base text-primary disabled:opacity-40"
                                                aria-label="Tambah jumlah">+</button></div>
                                        <strong class="text-[11px] font-extrabold text-primary lg:text-right"
                                            x-text="'Rp ' + Number(item.price*item.quantity).toLocaleString('id-ID')"></strong>
                                        <button type="button" :disabled="busy" @click="updateQty(item.cart_key,0)"
                                            class="grid size-8 place-items-center rounded-[9px] border border-[#f3ccd4] text-primary transition hover:bg-primary hover:text-white disabled:opacity-40"
                                            :aria-label="'Hapus ' + item.name"><svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.8">
                                                <path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5" />
                                            </svg></button>
                                    </div>
                                </article>
                            </template>
                        </div>
                    </section>
                    <div class="mt-5 grid gap-5 lg:grid-cols-[minmax(0,1fr)_330px]">
                        <div class="space-y-5">
                            <section class="rounded-2xl border border-[#f0e5e2] bg-white p-4 shadow-[0_5px_18px_rgba(113,74,69,.06)] md:p-5">
                                <div class="flex items-center gap-3 border-b border-border pb-3"><span
                                        class="grid size-10 place-items-center rounded-full bg-[#fff0ef] text-primary"><svg class="size-5"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M12 3 5 6v5c0 4.8 2.8 8.4 7 10 4.2-1.6 7-5.2 7-10V6l-7-3Z" />
                                            <path d="m9.5 12 1.6 1.6 3.6-4" />
                                        </svg></span><span>
                                        <h2 class="font-heading text-sm font-bold">Informasi Pelanggan</h2>
                                        <p class="mt-0.5 text-[9px] text-text-muted">Data Anda digunakan untuk identifikasi pesanan.</p>
                                    </span></div>
                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    <label class="block text-[10px] font-semibold text-text-muted">Nama Lengkap<input type="text" name="customer_name"
                                            form="checkout-form" required value="{{ old('customer_name') }}" placeholder="Contoh: Budi Santoso"
                                            class="mt-2 h-11 w-full rounded-xl border border-border bg-[#fffaf8] px-3 text-xs text-text outline-none transition duration-200 focus:border-primary focus:ring-2 focus:ring-primary/15"></label>
                                    <label class="block text-[10px] font-semibold text-text-muted">No. WhatsApp / Email<input type="text"
                                            name="customer_contact" form="checkout-form" required value="{{ old('customer_contact') }}"
                                            placeholder="081234567890 / email"
                                            class="mt-2 h-11 w-full rounded-xl border border-border bg-[#fffaf8] px-3 text-xs text-text outline-none transition duration-200 focus:border-primary focus:ring-2 focus:ring-primary/15"></label>
                                </div>
                                @error('customer_name')
                                    <p class="mt-2 text-[10px] font-semibold text-danger">{{ $message }}</p>
                                @enderror
                                @error('customer_contact')
                                    <p class="mt-2 text-[10px] font-semibold text-danger">{{ $message }}</p>
                                @enderror
                            </section>
                            <div class="flex items-center gap-3 rounded-2xl bg-[linear-gradient(100deg,#fff5f2,#fde8e4)] p-4 text-[10px] text-text-muted"><span
                                    class="grid size-10 shrink-0 place-items-center rounded-full bg-white text-primary"><svg class="size-5"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M12 3 5 6v5c0 4.8 2.8 8.4 7 10 4.2-1.6 7-5.2 7-10V6l-7-3Z" />
                                        <path d="m9.5 12 1.6 1.6 3.6-4" />
                                    </svg></span><span><strong class="block text-xs text-text">Pesanan Anda aman</strong>Data pesanan dilindungi dan hanya
                                    digunakan untuk proses pemesanan.</span></div>
                            <a href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table]) }}"
                                class="hidden h-11 w-[155px] items-center justify-center gap-2 rounded-[10px] border border-primary text-[10px] font-bold text-primary transition duration-200 hover:bg-primary hover:text-white lg:flex">←
                                Lanjut Belanja</a>
                        </div>

                        <aside class="h-fit rounded-2xl border border-[#f0e5e2] bg-white p-5 shadow-[0_7px_22px_rgba(113,74,69,.08)]">
                            <h2 class="font-heading text-sm font-bold">Ringkasan Pesanan</h2>
                            <div class="mt-5 space-y-3 text-[10px] text-text-muted">
                                <div class="flex justify-between gap-4"><span>Subtotal (<b x-text="cartCount"></b> item)</span><span
                                        class="font-semibold text-text" x-text="'Rp ' + cartTotal.toLocaleString('id-ID')"></span></div>
                                <div class="flex justify-between gap-4"><span>Nomor Meja</span><span
                                        class="font-semibold text-text">{{ $tableLabel }}</span></div>
                                <div class="flex justify-between gap-4"><span>Cabang</span><span
                                        class="max-w-[170px] truncate font-semibold text-text">{{ $branch }}</span></div>
                            </div>
                            <div class="mt-4 flex items-center justify-between border-t border-border pt-4"><strong class="text-sm">Total</strong><strong
                                    class="font-heading text-xl font-extrabold text-primary" x-text="'Rp ' + cartTotal.toLocaleString('id-ID')"></strong>
                            </div>
                            <form id="checkout-form" action="{{ route('customer.checkout', ['branch_code' => $branch_code]) }}" method="POST"
                                class="mt-5">@csrf<button type="submit" :disabled="busy"
                                    class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-[linear-gradient(100deg,#f63f6f,#fa557a)] text-xs font-bold text-white shadow-[0_8px_18px_rgba(242,77,115,.18)] transition duration-200 hover:-translate-y-0.5 hover:shadow-lg disabled:opacity-50">Checkout
                                    <span class="text-lg">›</span></button></form>
                        </aside>
                    </div>
                    <a href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table]) }}"
                        class="mt-4 flex h-11 w-full items-center justify-center gap-2 rounded-[10px] border border-primary text-[10px] font-bold text-primary lg:hidden">←
                        Lanjut Belanja</a>
                </div>
            </main>
        </div>
    </div>
</x-customer-layout>
