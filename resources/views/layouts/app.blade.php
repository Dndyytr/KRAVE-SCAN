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
    <body class="bg-bg text-text font-sans antialiased min-h-screen flex">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-card border-r border-border flex-col hidden md:flex shrink-0">
            <div class="h-16 px-6 border-b border-border flex items-center gap-2">
                <span class="text-primary font-brand font-extrabold t-size8 tracking-tight">KRAVE<span class="text-accent">SCAN</span></span>
                <span class="bg-primary-soft/40 text-accent font-semibold px-2 py-0.5 rounded-full t-size1">Dashboard</span>
            </div>
            
            <div class="flex-grow py-6 px-4 flex flex-col justify-between">
                <nav class="space-y-1">
                    <!-- General Links -->
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition {{ request()->routeIs('dashboard') ? 'bg-primary-soft text-accent' : 'text-text-muted hover:bg-surface hover:text-text' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                        </svg>
                        Ringkasan
                    </a>

                    <!-- Admin specific links (placeholder check) -->
                    <div class="pt-4 pb-2 text-[10px] uppercase font-bold text-text-muted/60 tracking-wider px-4">Menu & Stok</div>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 text-text-muted hover:bg-surface hover:text-text transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Daftar Menu
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 text-text-muted hover:bg-surface hover:text-text transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Stok Barang
                    </a>

                    <div class="pt-4 pb-2 text-[10px] uppercase font-bold text-text-muted/60 tracking-wider px-4">Transaksi</div>
                    <a href="{{ route('cashier.orders') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 transition {{ request()->routeIs('cashier.orders*') ? 'bg-primary-soft text-accent' : 'text-text-muted hover:bg-surface hover:text-text' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Daftar Pesanan
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 text-text-muted hover:bg-surface hover:text-text transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Riwayat Transaksi
                    </a>

                    <div class="pt-4 pb-2 text-[10px] uppercase font-bold text-text-muted/60 tracking-wider px-4">Manajemen & Laporan</div>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 text-text-muted hover:bg-surface hover:text-text transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Kelola Staf / Cabang
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-semibold t-size4 text-text-muted hover:bg-surface hover:text-text transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Laporan Penjualan
                    </a>
                </nav>

                <!-- Profile summary & logout -->
                <div class="border-t border-border pt-4">
                    <div class="flex items-center gap-3 px-2 mb-3">
                        <div class="w-9 h-9 rounded-full bg-primary-soft text-accent flex items-center justify-center font-bold">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <div class="font-bold t-size4 truncate">{{ Auth::user()->name ?? 'Pengguna' }}</div>
                            <div class="t-size1 text-text-muted capitalize">{{ Auth::user()->role?->name ?? 'Staf' }}</div>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 rounded-xl font-semibold t-size4 text-danger hover:bg-danger/10 transition text-left">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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
            <header class="h-16 bg-card border-b border-border flex items-center justify-between px-6 sticky top-0 z-30 shadow-xs">
                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-4">
                    <button class="md:hidden text-text-muted hover:text-text">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    @isset($header)
                        <div class="font-bold t-size6 md:t-size7">{{ $header }}</div>
                    @endisset
                </div>

                <div class="flex items-center gap-4">
                    <!-- Branch Badge -->
                    <span class="bg-surface-alt border border-border text-text-muted px-3 py-1 rounded-full t-size2 font-semibold flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ Auth::user()->branch->name ?? 'Semua Cabang' }}
                    </span>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-grow p-6 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
