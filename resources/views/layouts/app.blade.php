<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Bakso Cinta Dashboard') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-dvh overflow-hidden bg-[#fffafa] text-text antialiased" x-data="{ sidebarOpen: false }">
    <div x-cloak x-show="sidebarOpen" x-transition.opacity.duration.200ms @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-[#2d1c20]/35 backdrop-blur-sm lg:hidden"></div>

    <div class="flex h-dvh min-h-0">
        <x-admin.sidebar />

        <section class="flex min-w-0 flex-1 flex-col">
            <header class="relative z-30 flex h-16 shrink-0 items-center justify-between border-b border-[#f3e2e5] bg-white/90 px-4 backdrop-blur lg:hidden">
                <button type="button" @click="sidebarOpen = true" class="rounded-xl border border-[#f3e2e5] bg-white p-2 text-text" aria-label="Buka navigasi">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <img src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt="" class="h-9 w-9">
                    <span class="font-brand font-extrabold text-[#ef426f]">Bakso Cinta</span>
                </div>
                <x-notification-bell />
            </header>

            <main class="min-h-0 flex-1 overflow-y-auto">
                <div class="mx-auto w-full max-w-[1500px] p-4 md:p-6 xl:p-7">
                    <x-alert />
                    @isset($header)
                        <div class="mb-5">{{ $header }}</div>
                    @endisset
                    {{ $slot }}
                </div>
            </main>
        </section>
    </div>

    @stack('scripts')
</body>

</html>
