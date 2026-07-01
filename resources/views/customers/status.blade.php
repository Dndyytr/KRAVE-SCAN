<x-customer-layout :branch="$branch" :table="$order ? $order->table_number : session('table_number', 1)" :immersive="true">
    @php
        $table = $order ? $order->table_number : session('table_number', 1);
        $tableLabel = preg_match('/^[A-Za-z]/', (string) $table) ? $table : 'A' . str_pad($table, 2, '0', STR_PAD_LEFT);
        $steps = [
            'pending' => ['label' => 'Pesanan Diterima', 'description' => 'Menunggu pembayaran'],
            'confirmed' => ['label' => 'Pembayaran Dikonfirmasi', 'description' => 'Pesanan masuk ke dapur'],
            'in_process' => ['label' => 'Sedang Disiapkan', 'description' => 'Menu sedang dimasak'],
            'completed' => ['label' => 'Selesai', 'description' => 'Pesanan telah disajikan'],
        ];
        $stepKeys = array_keys($steps);
        $currentStep = $order ? array_search($order->status, $stepKeys, true) : false;
        $currentStep = $currentStep === false ? -1 : $currentStep;
    @endphp

    <div class="min-h-screen bg-[radial-gradient(circle_at_50%_0,#f7dfdd,#fff9f7_48%,#fffdf9)] lg:p-[18px]">
        <div
            class="mx-auto min-h-screen w-full max-w-[1400px] overflow-hidden bg-white lg:grid lg:h-[calc(100vh-36px)] lg:min-h-0 lg:overflow-hidden lg:grid-cols-[238px_minmax(0,1fr)] lg:rounded-[25px] lg:shadow-[0_10px_35px_rgba(130,73,73,.14)]">
            <aside
                class="relative hidden overflow-hidden border-r border-[#f4e5e0] bg-[linear-gradient(160deg,#fffaf7,#fff5f0)] px-[22px] pb-7 pt-[30px] lg:flex lg:h-full lg:flex-col lg:overflow-y-auto lg:rounded-l-[25px]">
                <div class="absolute left-[26px] top-[26px] grid grid-cols-4 gap-3" aria-hidden="true">
                    @for ($i = 0; $i < 16; $i++)
                        <i class="size-[5px] rounded-full bg-[#f17996]"></i>
                    @endfor
                </div>
                <div class="mb-[35px] mt-[68px] flex flex-col items-center"><img src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt=""
                        class="size-[78px]"><strong
                        class="-mt-[11px] font-brand text-[28px] font-extrabold italic leading-none tracking-[-.055em] text-[#e95578]">Bakso Cinta</strong><span
                        class="mt-2 text-[8px] font-bold tracking-[.48em] text-[#ad8a6d]">— CIAMIS —</span></div>
                <div class="grid grid-cols-[48px_1fr] items-center rounded-[15px] bg-white/80 px-[14px] py-[10px] shadow-[0_5px_20px_rgba(174,111,112,.07)]">
                    <span class="grid size-11 place-items-center rounded-full bg-[#fff0ef] text-primary"><svg width="26" height="26" viewBox="0 0 32 32"
                            fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="16" cy="7" r="3" />
                            <path d="M10 14v11M22 14v11M8 17h16v6H8zM5 18v9M27 18v9M9 27h4M19 27h4" />
                        </svg></span><span><small class="block text-[10px] text-[#706865]">Meja Anda</small><strong
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
                        class="flex min-h-11 items-center gap-3 rounded-[9px] px-[14px] text-[11px] font-semibold text-[#4c403e] transition duration-200 hover:bg-[#fff0f0] hover:text-primary"><svg
                            class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 4h2l2.2 10.5h10.9L21 7H6M9 20h.01M18 20h.01" />
                        </svg>Keranjang</a>
                    <a href="{{ route('customer.payment', ['branch_code' => $branch_code, 'order' => $order?->id]) }}"
                        class="flex min-h-11 items-center gap-3 rounded-[9px] px-[14px] text-[11px] font-semibold text-[#4c403e] transition duration-200 hover:bg-[#fff0f0] hover:text-primary"><svg
                            class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3z" />
                        </svg>Pembayaran</a>
                    <a href="{{ route('customer.order.status', ['branch_code' => $branch_code, 'order' => $order?->id]) }}"
                        class="flex min-h-11 items-center gap-3 rounded-[9px] bg-[linear-gradient(100deg,#f54772,#fc708d)] px-[14px] text-[11px] font-semibold text-white shadow-[0_8px_18px_rgba(242,76,114,.18)]"><svg
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

            <main class="min-w-0 px-3 pb-28 pt-3 bp360:px-4 md:px-7 md:pb-8 md:pt-6 lg:px-8 lg:pb-7 lg:pt-8 lg:h-full lg:overflow-y-auto"
                @if ($order) x-data="{ status: '{{ $order->status }}', init() { if (['pending','confirmed','in_process'].includes(this.status)) setInterval(() => window.location.reload(), 15000) } }" @endif>
                <header class="mb-5 flex items-center justify-between lg:hidden"><a
                        href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table]) }}"
                        class="grid size-9 place-items-center rounded-full border border-border bg-white" aria-label="Kembali">←</a>
                    <h1 class="font-heading text-base font-extrabold bp360:text-lg">Pelacakan Pesanan ❧</h1><span
                        class="rounded-[10px] border border-border bg-white px-3 py-2 text-center text-[9px] text-text-muted">Meja<br><b
                            class="text-sm text-primary">{{ $tableLabel }}</b></span>
                </header>
                <div class="hidden items-start justify-between gap-5 lg:flex">
                    <div>
                        <p class="text-xs font-semibold text-primary">Pesanan Saya ❧</p>
                        <h1 class="mt-2 font-heading text-3xl font-extrabold tracking-tight">Pelacakan Pesanan</h1>
                        <p class="mt-2 text-xs text-text-muted">Pantau status pesanan Anda secara berkala.</p>
                    </div><x-customer.language-selector />
                </div>

                @if (session('error'))
                    <div class="mt-4 rounded-xl border border-danger/30 bg-danger/10 p-3 text-xs font-semibold text-danger">{{ session('error') }}</div>
                @endif
                @if (session('success'))
                    <div class="mt-4 rounded-xl border border-success/30 bg-success/10 p-3 text-xs font-semibold text-success">{{ session('success') }}</div>
                @endif

                @if (!$order)
                    <section class="mx-auto mt-10 max-w-xl rounded-3xl border border-border bg-white p-10 text-center shadow-sm"><span
                            class="mx-auto grid size-16 place-items-center rounded-full bg-primary-soft/30 text-primary"><svg class="size-8" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M7 3h10v18H7zM10 8h4M10 12h4" />
                            </svg></span>
                        <h2 class="mt-4 font-heading text-xl font-extrabold">Belum Ada Pesanan Aktif</h2>
                        <p class="mx-auto mt-2 max-w-sm text-xs leading-relaxed text-text-muted">Meja Anda belum memiliki pesanan yang dapat dilacak.</p><a
                            href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table]) }}"
                            class="mt-6 inline-flex rounded-full bg-primary px-7 py-3 text-xs font-bold text-white">Lihat Menu</a>
                    </section>
                @else
                    <div
                        class="mt-5 flex flex-col gap-3 rounded-2xl border border-[#f0e5e2] bg-white p-4 shadow-[0_6px_22px_rgba(113,74,69,.07)] md:flex-row md:items-center md:justify-between">
                        <div><small class="text-[9px] text-text-muted">No. Pesanan</small><strong
                                class="mt-1 block font-heading text-base text-primary">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</strong></div>
                        <div class="text-left md:text-right"><small class="text-[9px] text-text-muted">Dibuat</small><span
                                class="mt-1 block text-[10px] font-semibold">{{ $order->created_at->translatedFormat('d M Y • H:i') }} WIB</span></div>
                    </div>

                    @if ($activeOrders->count() > 1)
                        <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                            @foreach ($activeOrders as $activeOrder)
                                <a href="{{ route('customer.order.status', ['branch_code' => $branch_code, 'order' => $activeOrder->id]) }}"
                                    class="shrink-0 rounded-xl border px-4 py-2 text-[10px] font-bold {{ $activeOrder->id === $order->id ? 'border-primary bg-primary text-white' : 'border-border bg-white text-text-muted' }}">#{{ str_pad($activeOrder->id, 5, '0', STR_PAD_LEFT) }}</a>
                            @endforeach
                        </div>
                    @endif

                    <section class="mt-4 rounded-2xl border border-[#f0e5e2] bg-white p-4 shadow-[0_7px_22px_rgba(113,74,69,.08)] md:p-5">
                        <h2 class="font-heading text-sm font-bold">Status Pesanan</h2>
                        @if ($order->status === 'cancelled')
                            <div class="mt-5 flex items-start gap-3 rounded-xl border border-danger/30 bg-danger/10 p-4"><span
                                    class="grid size-10 shrink-0 place-items-center rounded-full bg-white text-danger"><svg class="size-5"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M8 8l8 8M16 8l-8 8" />
                                    </svg></span><span><strong class="text-sm text-danger">Pesanan Dibatalkan</strong>
                                    <p class="mt-1 text-[10px] text-text-muted">Hubungi staf jika Anda membutuhkan bantuan.</p>
                                </span></div>
                        @else
                            <ol class="relative mt-5 grid gap-0 md:grid-cols-4 md:gap-3">
                                @foreach ($steps as $key => $step)
                                    @php
                                        $index = array_search($key, $stepKeys, true);
                                        $done = $index <= $currentStep;
                                        $current = $index === $currentStep;
                                        $circleClass = $current
                                            ? 'border-primary bg-primary text-white shadow-[0_5px_14px_rgba(242,77,115,.24)]'
                                            : ($done
                                                ? 'border-primary bg-white text-primary'
                                                : 'border-[#ddd9d7] bg-[#fafafa] text-[#c9c5c3]');
                                    @endphp
                                    <li class="relative flex min-h-[82px] gap-3 pl-1 md:block md:min-h-0 md:text-center">
                                        @if (!$loop->last)
                                            <span
                                                class="absolute left-[20px] top-10 h-[calc(100%-20px)] w-0.5 {{ $index < $currentStep ? 'bg-primary' : 'bg-[#e8e4e2]' }} md:left-[calc(50%+20px)] md:top-5 md:h-0.5 md:w-[calc(100%-28px)]"></span>
                                        @endif
                                        <span
                                            class="relative z-10 grid size-10 shrink-0 place-items-center rounded-full border-2 {{ $circleClass }} md:mx-auto">
                                            @if ($key === 'pending')
                                                <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M7 3h10v18H7zM10 8h4M10 12h4" />
                                                </svg>
                                            @elseif($key === 'confirmed')
                                                <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="m7 12 3 3 7-8" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                            @elseif($key === 'in_process')
                                                <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M4 17h16M6 17a6 6 0 0 1 12 0M12 8v3M3 20h18" />
                                            </svg>@else<svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="1.8">
                                                    <path d="m7 12 3 3 7-8" />
                                                    <circle cx="12" cy="12" r="9" />
                                                </svg>
                                            @endif
                                        </span>
                                        <span class="pt-1 md:mt-3 md:block md:pt-0"><strong
                                                class="block text-[10px] {{ $current ? 'text-primary' : 'text-text' }}">{{ $step['label'] }}</strong><small
                                                class="mt-1 block text-[8px] text-text-muted">{{ $step['description'] }}</small>
                                            @if ($current)
                                                <small class="mt-1 block text-[8px] font-semibold text-primary">{{ $order->updated_at->format('H:i') }}
                                                    WIB</small>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ol>
                            <div class="mt-5 flex items-start gap-3 rounded-xl bg-[linear-gradient(100deg,#fff1f0,#fde7e4)] p-4"><span
                                    class="grid size-10 shrink-0 place-items-center rounded-full bg-white text-primary"><svg class="size-5"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M4 17h16M6 17a6 6 0 0 1 12 0M12 8v3M3 20h18" />
                                    </svg></span><span><strong class="text-xs">{{ $steps[$order->status]['label'] }}</strong>
                                    <p class="mt-1 text-[9px] text-text-muted">{{ $steps[$order->status]['description'] }}. Halaman diperbarui otomatis setiap
                                        15 detik.</p>
                                </span></div>
                        @endif
                    </section>
                    <div class="mt-4 grid gap-4 md:grid-cols-[minmax(0,1fr)_330px]">
                        <section class="rounded-2xl border border-[#f0e5e2] bg-white p-4 shadow-[0_6px_20px_rgba(113,74,69,.06)] md:p-5">
                            <div class="flex items-center gap-3 border-b border-dashed border-border pb-4"><span
                                    class="grid size-10 place-items-center rounded-full bg-[#fff0ef] text-primary"><svg class="size-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M7 3h10v18H7zM10 8h4M10 12h4M10 16h3" />
                                    </svg></span>
                                <h2 class="font-heading text-sm font-bold">Rincian Pesanan</h2>
                            </div>
                            <div class="divide-y divide-border">
                                @foreach ($order->orderItems as $item)
                                    @php $image = $item->menu?->image_path; @endphp
                                    <article class="flex items-center gap-3 py-3 first:pt-4">
                                        <div class="size-14 shrink-0 overflow-hidden rounded-[10px] bg-surface md:size-16">
                                            @if ($image)
                                                <img src="{{ str_starts_with($image, 'http') ? $image : asset($image) }}" alt="{{ $item->menu->name }}"
                                                class="size-full object-cover">@else<span
                                                    class="grid size-full place-items-center font-heading text-xl font-extrabold text-primary">{{ mb_substr($item->menu?->name ?? '?', 0, 1) }}</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="truncate text-[10px] font-bold md:text-xs">{{ $item->menu?->name ?? 'Menu tidak tersedia' }}</h3><span
                                                class="mt-1 block text-[9px] text-text-muted">{{ $item->quantity }} × Rp
                                                {{ number_format($item->price, 0, ',', '.') }}</span>
                                            @if ($item->note)
                                                <p class="mt-1 line-clamp-2 text-[8px] font-semibold text-primary">Catatan: {{ $item->note }}</p>
                                            @endif
                                        </div><strong class="text-[10px] md:text-xs">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                                    </article>
                                @endforeach
                            </div>
                            <div class="mt-2 flex items-center justify-between border-t border-border pt-4"><strong class="text-sm">Total
                                    Pembayaran</strong><strong class="font-heading text-xl font-extrabold text-primary">Rp
                                    {{ number_format($order->total_amount, 0, ',', '.') }}</strong></div>
                        </section>

                        <aside class="space-y-4">
                            <section class="rounded-2xl border border-[#f0e5e2] bg-white p-4 shadow-[0_6px_20px_rgba(113,74,69,.06)]">
                                <h2 class="font-heading text-sm font-bold">Informasi Pesanan</h2>
                                <dl class="mt-4 space-y-3 text-[10px] text-text-muted">
                                    <div class="flex justify-between gap-4">
                                        <dt>Meja</dt>
                                        <dd class="font-bold text-text">{{ $tableLabel }}</dd>
                                    </div>
                                    <div class="flex justify-between gap-4">
                                        <dt>Nama Pelanggan</dt>
                                        <dd class="max-w-[170px] truncate font-bold text-text">{{ $order->customer_name }}</dd>
                                    </div>
                                    <div class="flex justify-between gap-4">
                                        <dt>Kontak</dt>
                                        <dd class="max-w-[170px] truncate font-bold text-text">{{ $order->customer_contact }}</dd>
                                    </div>
                                    <div class="flex justify-between gap-4">
                                        <dt>Cabang</dt>
                                        <dd class="max-w-[170px] truncate font-bold text-text">{{ $branch }}</dd>
                                    </div>
                                </dl>
                            </section>
                            @if ($order->status === 'pending')
                                <a href="{{ route('customer.payment', ['branch_code' => $branch_code, 'order' => $order->id]) }}"
                                    class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-[linear-gradient(100deg,#f63f6f,#fa557a)] text-xs font-bold text-white shadow-[0_8px_18px_rgba(242,77,115,.18)] transition duration-200 hover:-translate-y-0.5">Bayar
                                    Sekarang ›</a>
                            @elseif (in_array($order->status, ['completed', 'cancelled']))
                                <a href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $order->table_number]) }}"
                                    class="flex h-12 w-full items-center justify-center gap-2 rounded-xl border border-primary text-xs font-bold text-primary transition duration-200 hover:bg-primary hover:text-white">Pesan
                                    Menu Lain</a>
                            @endif
                        </aside>
                    </div>
                    <div class="mt-4 flex items-center gap-3 rounded-2xl bg-[linear-gradient(100deg,#fff4f1,#fde7e3)] p-4"><img
                            src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt="" class="size-12 shrink-0"><span><strong class="text-xs">Terima
                                kasih telah memesan di Bakso Cinta!</strong>
                            <p class="mt-1 text-[9px] leading-relaxed text-text-muted">Kami selalu berusaha memberikan pelayanan terbaik untuk Anda.</p>
                        </span></div>
                @endif
            </main>
        </div>
    </div>
</x-customer-layout>
