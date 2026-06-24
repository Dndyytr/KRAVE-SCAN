<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Krave Scan Dashboard') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-bg text-text font-sans antialiased min-h-screen flex" x-data="{ sidebarOpen: false }">

        {{-- ── Mobile Sidebar Overlay ── --}}
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-black/30 backdrop-blur-sm md:hidden"
             style="display: none;"></div>

        {{-- ── Sidebar Navigation ── --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-card border-r border-border flex flex-col
                      transform transition-transform duration-300 ease-in-out
                      md:relative md:translate-x-0 md:flex shrink-0 shadow-lg md:shadow-none overflow-y-auto">

            {{-- Brand Header --}}
            <div class="h-16 px-6 border-b border-border flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <span class="text-primary font-brand font-extrabold t-size8 tracking-tight">KRAVE<span class="text-accent">SCAN</span></span>
                    <span class="bg-primary-soft/40 text-accent font-semibold px-2 py-0.5 rounded-full t-size1">Dashboard</span>
                </div>
                {{-- Close button (mobile only) --}}
                <button @click="sidebarOpen = false" class="md:hidden text-text-muted hover:text-text p-1 rounded-lg hover:bg-surface">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="flex-grow py-6 px-4 flex flex-col justify-between">
                <nav class="space-y-1">
                    {{-- General Links --}}
                    <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition-all duration-200 relative {{ request()->routeIs('dashboard') ? 'bg-primary-soft text-accent shadow-sm' : 'text-text-muted hover:bg-surface hover:text-text hover:translate-x-0.5' }}">
                        @if(request()->routeIs('dashboard'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-strong rounded-r-full"></span>
                        @endif
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                        </svg>
                        Ringkasan
                    </a>

                    @if(Auth::user()->role?->name === 'admin')
                        {{-- Admin: Menu & Stok --}}
                        <div class="pt-5 pb-2">
                            <div class="border-t border-border/60 mb-3"></div>
                            <span class="text-[10px] uppercase font-bold text-text-muted/60 tracking-wider px-4">Menu & Stok</span>
                        </div>
                        <a href="{{ route('admin.menus.index') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition-all duration-200 relative {{ request()->routeIs('admin.menus*') ? 'bg-primary-soft text-accent shadow-sm' : 'text-text-muted hover:bg-surface hover:text-text hover:translate-x-0.5' }}">
                            @if(request()->routeIs('admin.menus*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-strong rounded-r-full"></span>
                            @endif
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Daftar Menu
                        </a>
                        <a href="{{ route('admin.stocks.index') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition-all duration-200 relative {{ request()->routeIs('admin.stocks*') ? 'bg-primary-soft text-accent shadow-sm' : 'text-text-muted hover:bg-surface hover:text-text hover:translate-x-0.5' }}">
                            @if(request()->routeIs('admin.stocks*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-strong rounded-r-full"></span>
                            @endif
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Stok Barang
                        </a>
                    @endif

                    {{-- Transaksi --}}
                    <div class="pt-5 pb-2">
                        <div class="border-t border-border/60 mb-3"></div>
                        <span class="text-[10px] uppercase font-bold text-text-muted/60 tracking-wider px-4">Transaksi</span>
                    </div>
                    @if(Auth::user()->role?->name === 'admin')
                        <a href="{{ route('admin.orders.index') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition-all duration-200 relative {{ request()->routeIs('admin.orders*') ? 'bg-primary-soft text-accent shadow-sm' : 'text-text-muted hover:bg-surface hover:text-text hover:translate-x-0.5' }}">
                            @if(request()->routeIs('admin.orders*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-strong rounded-r-full"></span>
                            @endif
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            Daftar Pesanan
                        </a>
                        <a href="{{ route('admin.transactions.index') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition-all duration-200 relative {{ request()->routeIs('admin.transactions*') ? 'bg-primary-soft text-accent shadow-sm' : 'text-text-muted hover:bg-surface hover:text-text hover:translate-x-0.5' }}">
                            @if(request()->routeIs('admin.transactions*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-strong rounded-r-full"></span>
                            @endif
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Riwayat Transaksi
                        </a>
                    @elseif(Auth::user()->role?->name === 'cashier')
                        <a href="{{ route('cashier.orders') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition-all duration-200 relative {{ request()->routeIs('cashier.orders*') ? 'bg-primary-soft text-accent shadow-sm' : 'text-text-muted hover:bg-surface hover:text-text hover:translate-x-0.5' }}">
                            @if(request()->routeIs('cashier.orders*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-strong rounded-r-full"></span>
                            @endif
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            Daftar Pesanan
                        </a>
                    @endif

                    @if(Auth::user()->role?->name === 'admin')
                        {{-- Admin: Manajemen & Laporan --}}
                        <div class="pt-5 pb-2">
                            <div class="border-t border-border/60 mb-3"></div>
                            <span class="text-[10px] uppercase font-bold text-text-muted/60 tracking-wider px-4">Manajemen & Laporan</span>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition-all duration-200 relative {{ request()->routeIs('admin.users*') ? 'bg-primary-soft text-accent shadow-sm' : 'text-text-muted hover:bg-surface hover:text-text hover:translate-x-0.5' }}">
                            @if(request()->routeIs('admin.users*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-strong rounded-r-full"></span>
                            @endif
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Kelola Staf / Cabang
                        </a>
                        <a href="{{ route('admin.reports.sales') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition-all duration-200 relative {{ request()->routeIs('admin.reports.sales') ? 'bg-primary-soft text-accent shadow-sm' : 'text-text-muted hover:bg-surface hover:text-text hover:translate-x-0.5' }}">
                            @if(request()->routeIs('admin.reports.sales'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-strong rounded-r-full"></span>
                            @endif
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Laporan Penjualan
                        </a>
                        <a href="{{ route('admin.reports.menus') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition-all duration-200 relative {{ request()->routeIs('admin.reports.menus') ? 'bg-primary-soft text-accent shadow-sm' : 'text-text-muted hover:bg-surface hover:text-text hover:translate-x-0.5' }}">
                            @if(request()->routeIs('admin.reports.menus'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-strong rounded-r-full"></span>
                            @endif
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Performa Menu
                        </a>
                        <a href="{{ route('admin.reports.payments') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition-all duration-200 relative {{ request()->routeIs('admin.reports.payments') ? 'bg-primary-soft text-accent shadow-sm' : 'text-text-muted hover:bg-surface hover:text-text hover:translate-x-0.5' }}">
                            @if(request()->routeIs('admin.reports.payments'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-strong rounded-r-full"></span>
                            @endif
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Metode Pembayaran
                        </a>

                        {{-- Admin: Otomatisasi --}}
                        <div class="pt-5 pb-2">
                            <div class="border-t border-border/60 mb-3"></div>
                            <span class="text-[10px] uppercase font-bold text-text-muted/60 tracking-wider px-4">Otomatisasi RPA</span>
                        </div>
                        <a href="{{ route('admin.automations.index') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition-all duration-200 relative {{ request()->routeIs('admin.automations*') ? 'bg-primary-soft text-accent shadow-sm' : 'text-text-muted hover:bg-surface hover:text-text hover:translate-x-0.5' }}">
                            @if(request()->routeIs('admin.automations*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-strong rounded-r-full"></span>
                            @endif
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Log Otomatisasi (RPA)
                        </a>
                        <a href="{{ route('admin.activity-logs.index') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition-all duration-200 relative {{ request()->routeIs('admin.activity-logs*') ? 'bg-primary-soft text-accent shadow-sm' : 'text-text-muted hover:bg-surface hover:text-text hover:translate-x-0.5' }}">
                            @if(request()->routeIs('admin.activity-logs*'))
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary-strong rounded-r-full"></span>
                            @endif
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Audit & Log Aktivitas
                        </a>
                    @endif
                </nav>

                <!-- Profile summary & logout -->
                <div class="border-t border-border pt-4 mt-4">
                    <div class="flex items-center gap-3 px-2 mb-3">
                        <div class="w-9 h-9 rounded-full bg-primary-soft text-accent flex items-center justify-center font-bold shrink-0">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-bold t-size4 truncate">{{ Auth::user()->name ?? 'Pengguna' }}</div>
                            <div class="t-size1 text-text-muted capitalize">{{ Auth::user()->role?->name ?? 'Staf' }}</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 rounded-xl font-semibold t-size4 text-danger hover:bg-danger/10 transition-all duration-200 text-left">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Body Wrapper (Header + Content) -->
        <div class="flex-grow flex flex-col min-w-0">
            <!-- Top Header -->
            <header class="h-16 bg-card/80 backdrop-blur-md border-b border-border flex items-center justify-between px-6 sticky top-0 z-30 shadow-xs">
                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="md:hidden text-text-muted hover:text-text p-1.5 rounded-lg hover:bg-surface active:scale-95 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    @isset($header)
                        <div class="font-bold t-size6 md:t-size7">{{ $header }}</div>
                    @endisset
                </div>

                <div class="flex items-center gap-4">
                    <x-notification-bell />

                    @if(Auth::user()->branch_id === null && isset($globalBranches))
                        <form action="{{ route('admin.switch-branch') }}" method="POST" id="branch-switcher-form" class="flex items-center">
                            @csrf
                            <select name="branch_id" onchange="document.getElementById('branch-switcher-form').submit()" class="bg-surface-alt border border-border text-text px-3 py-1.5 rounded-full t-size2 font-semibold cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="">Semua Cabang (Global)</option>
                                @foreach($globalBranches as $branch)
                                    <option value="{{ $branch->id }}" {{ session('active_branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <!-- Branch Badge -->
                        <span class="bg-surface-alt border border-border text-text-muted px-3 py-1 rounded-full t-size2 font-semibold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ Auth::user()->branch->name ?? (app(\App\Services\BranchContext::class)->getBranch()->name ?? 'Semua Cabang') }}
                        </span>
                    @endif
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-grow p-6 overflow-y-auto">
                {{-- Session Flash Alert --}}
                <x-alert />
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
