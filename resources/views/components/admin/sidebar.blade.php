<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-[49] flex h-dvh w-[238px] shrink-0 flex-col border-r border-[#f3e2e5] bg-white transition-transform duration-300 ease-out lg:static lg:translate-x-0">
    <div class="flex h-[154px] shrink-0 items-center justify-center border-b border-[#faeef0] px-5">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center" aria-label="Bakso Cinta Ciamis">
            <img src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt="" class="h-16 w-16 object-contain">
            <span class="-mt-1 font-brand text-xl font-extrabold text-[#ef426f]">Bakso Cinta</span>
            <span class="mt-1 text-[9px] font-bold tracking-[.55em] text-[#a88762]">CIAMIS</span>
        </a>
        <button type="button" @click="sidebarOpen = false" class="absolute right-4 top-4 rounded-lg p-2 text-text-muted hover:bg-surface lg:hidden"
            aria-label="Tutup navigasi">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                <path d="m6 6 12 12M18 6 6 18" />
            </svg>
        </button>
    </div>

    <div class="flex min-h-0 flex-1 flex-col px-4 py-4">
        <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto pr-1" aria-label="Navigasi admin">
            <x-admin.nav-item :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-admin.nav-item>

            @if (auth()->user()->hasPermission('manage_users'))
                <x-admin.nav-item :href="route('admin.users.index')" :active="request()->routeIs('admin.users*')" icon="users">Manajemen
                    Pengguna</x-admin.nav-item>
            @endif
            @if (auth()->user()->hasPermission('manage_menus'))
                <x-admin.nav-item :href="route('cashier.menus.index')" :active="request()->routeIs('cashier.menus*')" icon="menu">Manajemen Menu</x-admin.nav-item>
            @endif
            @if (auth()->user()->hasPermission('manage_categories'))
                <x-admin.nav-item :href="route('cashier.categories.index')" :active="request()->routeIs('cashier.categories*')" icon="menu">Kategori Menu</x-admin.nav-item>
            @endif
            @if (auth()->user()->hasPermission('manage_stocks'))
                <x-admin.nav-item :href="route('admin.stocks.index')" :active="request()->routeIs('admin.stocks*')" icon="stock">Manajemen Stok</x-admin.nav-item>
            @endif
            @if (auth()->user()->hasPermission('view_all_orders'))
                <x-admin.nav-item :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders*')" icon="orders">Manajemen
                    Pesanan</x-admin.nav-item>
            @elseif (auth()->user()->hasPermission('access_cashier'))
                <x-admin.nav-item :href="route('cashier.orders')" :active="request()->routeIs('cashier.orders*')" icon="orders">Kelola Pesanan</x-admin.nav-item>
            @elseif (auth()->user()->hasPermission('access_kitchen'))
                <x-admin.nav-item :href="route('kitchen.orders')" :active="request()->routeIs('kitchen.orders*')" icon="orders">Antrean Masak</x-admin.nav-item>
            @endif
            @if (auth()->user()->hasPermission('view_all_transactions'))
                <x-admin.nav-item :href="route('admin.transactions.index')" :active="request()->routeIs('admin.transactions*')" icon="money">Manajemen
                    Transaksi</x-admin.nav-item>
            @elseif (auth()->user()->hasPermission('access_cashier'))
                <x-admin.nav-item :href="route('cashier.transactions')" :active="request()->routeIs('cashier.transactions*')" icon="money">Kelola
                    Transaksi</x-admin.nav-item>
            @endif
            @if (auth()->user()->hasPermission('view_reports'))
                <x-admin.nav-item :href="route('admin.reports.sales')" :active="request()->routeIs('admin.reports*')" icon="report">Laporan &
                    Analitik</x-admin.nav-item>
            @endif
            @if (auth()->user()->hasPermission('manage_roles'))
                <x-admin.nav-item :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles*')" icon="settings">Pengaturan
                    Akses</x-admin.nav-item>
            @endif
            @if (auth()->user()->hasPermission('manage_automations'))
                <x-admin.nav-item :href="route('admin.automations.index')" :active="request()->routeIs('admin.automations*')" icon="settings">Otomatisasi</x-admin.nav-item>
            @endif
            @if (auth()->user()->hasPermission('view_activity_logs'))
                <x-admin.nav-item :href="route('admin.activity-logs.index')" :active="request()->routeIs('admin.activity-logs*')" icon="report">Log Aktivitas</x-admin.nav-item>
            @endif
        </nav>

        <div class="mt-4 shrink-0 space-y-3">
            <div class="rounded-2xl bg-gradient-to-br from-[#fff5f7] to-[#fff0ed] p-4">
                <p class="t-size2 font-bold text-text">Butuh bantuan?</p>
                <p class="mt-1 t-size1 leading-relaxed text-text-muted">Hubungi administrator sistem jika ada kendala.
                </p>
            </div>
            <div class="flex items-center gap-3 rounded-xl border border-[#f3e2e5] bg-white p-2.5">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#fff0d8] font-bold text-[#a86b34]">
                    {{ mb_strtoupper(mb_substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate t-size2 font-bold">{{ Auth::user()->name ?? 'Pengguna' }}</p>
                    <p class="truncate t-size1 capitalize text-text-muted">{{ Auth::user()->role?->name ?? 'Staf' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg p-1.5 text-text-muted hover:bg-[#fff0f4] hover:text-[#ff3868]" title="Keluar" aria-label="Keluar">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M10 17l5-5-5-5M15 12H3M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
