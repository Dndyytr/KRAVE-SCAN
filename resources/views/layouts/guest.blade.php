<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Bakso Cinta Ciamis — Sistem manajemen pemesanan dan operasional.">

        <title>{{ $title ?? 'Login — Bakso Cinta Ciamis' }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-bg text-text font-sans antialiased min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 md:p-8">

        {{-- ── Main Login Card ── --}}
        <div class="max-w-5xl w-full bg-card rounded-3xl border border-border shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[560px] md:min-h-[640px]">

            {{-- ═══════════════════════════════════════════════════════════
                 LEFT PANEL — Branding & Illustration
                 ═══════════════════════════════════════════════════════════ --}}
            <div class="w-full md:w-[45%] relative overflow-hidden flex flex-col items-center justify-center px-8 pt-10 pb-0 md:px-10 md:pt-12 md:pb-0 anim-slide-left"
                 style="background: linear-gradient(170deg, #fff5f7 0%, #fce7ec 50%, #f8d7e0 100%);">

                {{-- Dot Pattern (top-left) --}}
                <div class="absolute top-6 left-6 opacity-40">
                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                        @for ($row = 0; $row < 6; $row++)
                            @for ($col = 0; $col < 6; $col++)
                                <circle cx="{{ 6 + $col * 14 }}" cy="{{ 6 + $row * 14 }}" r="3" fill="#e88ca2"/>
                            @endfor
                        @endfor
                    </svg>
                </div>

                {{-- Decorative Blob (top-right, overlaps right panel) --}}
                <div class="absolute -top-12 -right-16 w-56 h-56 rounded-full bg-primary-soft/30 blur-3xl"></div>

                {{-- Small heart shapes --}}
                <div class="absolute top-16 right-10 opacity-50">
                    <svg width="18" height="16" viewBox="0 0 18 16" fill="#e88ca2">
                        <path d="M9 14.5l-1.1-1C3.6 9.7 1 7.3 1 4.5 1 2.4 2.7.8 4.8.8c1.2 0 2.4.6 3.2 1.5C8.8 1.4 10 .8 11.2.8 13.3.8 15 2.4 15 4.5c0 2.8-2.6 5.2-6.9 8.9L9 14.5z"/>
                    </svg>
                </div>
                <div class="absolute top-28 right-20 opacity-30">
                    <svg width="12" height="11" viewBox="0 0 18 16" fill="#d96b87">
                        <path d="M9 14.5l-1.1-1C3.6 9.7 1 7.3 1 4.5 1 2.4 2.7.8 4.8.8c1.2 0 2.4.6 3.2 1.5C8.8 1.4 10 .8 11.2.8 13.3.8 15 2.4 15 4.5c0 2.8-2.6 5.2-6.9 8.9L9 14.5z"/>
                    </svg>
                </div>

                {{-- Logo Icon --}}
                <div class="relative z-10 mb-2 anim-fade" style="animation-delay: 0.15s;">
                    <img src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt="Bakso Cinta Icon" class="w-24 h-24 md:w-28 md:h-28 drop-shadow-lg">
                </div>

                {{-- Brand Name --}}
                <div class="relative z-10 text-center mb-1 anim-fade" style="animation-delay: 0.25s;">
                    <h1 class="font-brand text-primary-strong text-3xl md:text-4xl font-extrabold italic leading-tight tracking-tight">
                        Bakso Cinta
                    </h1>
                    <p class="text-accent text-xs md:text-sm font-semibold tracking-[0.35em] mt-1">— C I A M I S —</p>
                </div>

                {{-- Welcome Text --}}
                <div class="relative z-10 text-center mt-4 anim-fade" style="animation-delay: 0.35s;">
                    <h2 class="t-size7 font-heading font-bold text-accent leading-snug">
                        {{ __('auth_page.welcome_back') }}
                    </h2>
                    <p class="t-size3 text-text-muted mt-2 max-w-[280px] mx-auto leading-relaxed font-medium">
                        {{ __('auth_page.welcome_tagline') }}
                    </p>
                </div>

                {{-- Bakso Image --}}
                <div class="relative z-10 mt-auto anim-slide-up" style="animation-delay: 0.45s;">
                    <img src="{{ asset('img/bakso.png') }}" alt="Bakso Cinta" class="w-64 md:w-72 lg:w-80 drop-shadow-xl object-contain max-h-[220px] md:max-h-[260px]">
                </div>

                {{-- Decorative Lines (bottom-left) --}}
                <div class="absolute bottom-24 left-6 opacity-40">
                    <svg width="30" height="40" viewBox="0 0 30 40" fill="none" stroke="#e88ca2" stroke-width="2.5" stroke-linecap="round">
                        <line x1="4" y1="0" x2="20" y2="16"/>
                        <line x1="4" y1="18" x2="4" y2="35"/>
                        <line x1="12" y1="30" x2="28" y2="30"/>
                    </svg>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════
                 RIGHT PANEL — Login Form
                 ═══════════════════════════════════════════════════════════ --}}
            <div class="w-full md:w-[55%] px-8 py-10 sm:px-12 sm:py-12 flex flex-col justify-center bg-card relative anim-slide-right" style="animation-delay: 0.1s;">

                {{-- Language Selector (top-right) --}}
                <div class="absolute top-5 right-6 z-20" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-border bg-white hover:bg-surface transition-colors text-xs font-semibold text-text-muted shadow-sm cursor-pointer">
                        {{-- Globe Icon --}}
                        <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18zM3.6 9h16.8M3.6 15h16.8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a15 15 0 014 9 15 15 0 01-4 9 15 15 0 01-4-9 15 15 0 014-9z"/>
                        </svg>
                        <span>{{ __('auth_page.language') }}</span>
                        {{-- Chevron --}}
                        <svg class="w-3 h-3 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    {{-- Dropdown --}}
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-border py-1 z-30">
                        <a href="{{ route('locale.switch', 'id') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold hover:bg-surface transition-colors {{ app()->getLocale() === 'id' ? 'text-primary-strong' : 'text-text-muted' }}">
                            🇮🇩 Bahasa Indonesia
                        </a>
                        <a href="{{ route('locale.switch', 'en') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold hover:bg-surface transition-colors {{ app()->getLocale() === 'en' ? 'text-primary-strong' : 'text-text-muted' }}">
                            🇬🇧 English
                        </a>
                    </div>
                </div>

                {{-- Mobile Branding (visible only below md) --}}
                <div class="mb-6 md:hidden text-center anim-fade">
                    <img src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt="Bakso Cinta Icon" class="w-16 h-16 mx-auto mb-2">
                    <span class="font-brand text-primary-strong text-xl font-extrabold italic">Bakso Cinta</span>
                    <p class="text-accent text-[0.6rem] font-semibold tracking-[0.3em] mt-0.5">— C I A M I S —</p>
                </div>

                {{-- Form Content (slot) --}}
                <div>
                    {{ $slot }}
                </div>
            </div>

        </div>

        {{-- Footer Copyright --}}
        <p class="text-text-muted t-size1 mt-6 font-medium anim-fade" style="animation-delay: 0.6s;">
            {{ __('auth_page.copyright', ['year' => date('Y')]) }}
        </p>

    </body>
</html>
