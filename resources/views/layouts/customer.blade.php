<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Krave Scan') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- AlpineJS (included via Vite app.js usually, but dynamic layout hooks might need it) -->
    </head>
    <body class="bg-bg text-text font-sans antialiased min-h-screen flex flex-col pb-20 md:pb-0">
        <!-- Customer Top Navbar -->
        <header class="bg-card border-b border-border sticky top-0 z-40 shadow-xs">
            <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-primary font-brand font-extrabold t-size7 tracking-tight">KRAVE<span class="text-accent">SCAN</span></span>
                    @isset($branch)
                        <span class="bg-primary-soft/40 text-accent font-semibold px-2 py-0.5 rounded-full t-size2">
                            {{ $branch }}
                        </span>
                    @endisset
                </div>
                
                @isset($table)
                    <div class="bg-surface border border-border px-3 py-1 rounded-full text-text-muted t-size2 font-semibold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary-strong animate-pulse"></span>
                        Meja {{ $table }}
                    </div>
                @endisset
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-grow max-w-md w-full mx-auto px-4 py-6">
            {{ $slot }}
        </main>

        <!-- Mobile Bottom Navigation (Visible only on mobile/small screens) -->
        <nav class="fixed bottom-0 left-0 right-0 bg-card border-t border-border py-2 px-6 flex justify-around items-center z-40 md:hidden shadow-lg">
            @php
                $branchCode = request()->route('branch_code');
                $tableNumber = session('table_number', 1);
                $latestOrderId = session('latest_order_id');
            @endphp
            
            <a href="{{ route('customer.menu', ['branch_code' => $branchCode, 'table_number' => $tableNumber]) }}" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('customer.menu') ? 'text-primary-strong font-bold' : 'text-text-muted hover:text-primary' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="t-size1 font-semibold">Menu</span>
            </a>
            
            <a href="#" class="flex flex-col items-center gap-1 text-text-muted hover:text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="t-size1 font-semibold">AI Scan</span>
            </a>
            
            <a href="{{ route('customer.cart', ['branch_code' => $branchCode]) }}" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('customer.cart') ? 'text-primary-strong font-bold' : 'text-text-muted hover:text-primary' }} relative"
               x-data="{ count: {{ array_sum(array_column(session('cart', []), 'quantity')) }} }"
               @cart-updated.window="count = $event.detail.count">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span x-show="count > 0" 
                      x-text="count" 
                      class="absolute -top-1 right-2 bg-primary text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold"
                      style="display: none;"></span>
                <span class="t-size1 font-semibold">Keranjang</span>
            </a>

            <a href="{{ $latestOrderId ? route('customer.order.status', ['branch_code' => $branchCode, 'order' => $latestOrderId]) : '#' }}" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('customer.order.status') ? 'text-primary-strong font-bold' : 'text-text-muted hover:text-primary' }} {{ !$latestOrderId ? 'opacity-50 cursor-not-allowed' : '' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <span class="t-size1 font-semibold">Pesanan</span>
            </a>
        </nav>
    </body>
</html>
