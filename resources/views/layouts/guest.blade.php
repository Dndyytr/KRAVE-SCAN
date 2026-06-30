<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Bakso Cinta Ciamis — Sistem manajemen pemesanan dan operasional.">
    <title>{{ $title ?? 'Login — Bakso Cinta Ciamis' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }

        .auth-page {
            --page-pad-x: clamp(10px, 2.8vw, 36px);
            --page-pad-y: clamp(8px, 1.6svh, 18px);
            padding: var(--page-pad-y) var(--page-pad-x) !important;
            background: radial-gradient(circle at 8% 8%, rgba(245, 183, 197, .18), transparent 28%), radial-gradient(circle at 91% 86%, rgba(245, 183, 197, .14), transparent 30%), #fff9f8;
        }

        .auth-shell {
            width: min(1352px, calc(100vw - (var(--page-pad-x) * 2)));
            min-height: min(974px, calc(100svh - (var(--page-pad-y) * 2) - 40px));
            height: auto;
            box-shadow: 0 18px 55px rgba(115, 70, 76, .13);
        }

        .auth-brand {
            padding: clamp(36px, 5svh, 56px) clamp(26px, 3vw, 44px) 0 !important;
            background: radial-gradient(circle at 112% -10%, rgba(251, 207, 204, .72) 0 34%, transparent 34.2%), linear-gradient(145deg, #fffaf4 0%, #fffaf5 70%, #fff7f2 100%);
        }

        .auth-brand::after {
            content: "";
            position: absolute;
            z-index: 0;
            left: -7%;
            right: -18%;
            bottom: -14%;
            height: clamp(36%, 45%, 48%);
            border-radius: 50% 50% 0 0 / 24% 24% 0 0;
            background: linear-gradient(145deg, #f9a7b8, #eb8199);
            transform: rotate(-4deg);
        }

        .auth-dots {
            left: clamp(26px, 3.4vw, 42px) !important;
            top: clamp(28px, 4svh, 44px) !important;
            gap: clamp(12px, 1.2vw, 17px) !important;
        }

        .auth-form-panel {
            border-radius: clamp(20px, 3vw, 34px) 28px 28px clamp(20px, 3vw, 34px);
            box-shadow: -12px 0 30px rgba(128, 89, 89, .035);
            padding: clamp(62px, 7.2svh, 92px) clamp(30px, 4vw, 56px) clamp(30px, 3.8svh, 42px) !important;
            align-items: flex-start !important;
        }

        .auth-language {
            right: clamp(28px, 4vw, 40px) !important;
            top: clamp(28px, 4.1svh, 46px) !important;
        }

        .auth-food {
            filter: drop-shadow(0 24px 22px rgba(130, 77, 79, .22));
        }

        .auth-slot {
            margin-top: clamp(38px, 6.8svh, 72px);
            max-width: clamp(404px, 33.5vw, 484px) !important;
        }

        .auth-brand-logo {
            margin-top: clamp(30px, 5.8svh, 62px) !important;
        }

        .auth-brand-logo img {
            width: clamp(78px, 7.5vw, 108px);
            height: clamp(78px, 7.5vw, 108px);
        }

        .auth-brand-logo h1 {
            font-size: clamp(30px, 2.65vw, 38px);
        }

        .auth-brand-logo>div {
            margin-top: clamp(8px, 1.2svh, 12px) !important;
            font-size: clamp(12px, 1.05vw, 15px);
            gap: clamp(8px, .85vw, 12px);
        }

        .auth-brand-logo>div span:not(:nth-child(2)) {
            width: clamp(32px, 3.4vw, 48px);
        }

        .auth-brand-copy {
            margin-top: clamp(18px, 2.8svh, 28px) !important;
        }

        .auth-brand-copy h2 {
            font-size: clamp(20px, 1.75vw, 25px);
        }

        .auth-brand-copy p {
            margin-top: clamp(8px, 1.2svh, 12px) !important;
            font-size: clamp(14px, 1.1vw, 16px);
            max-width: clamp(300px, 27vw, 390px);
        }

        .auth-food-wrap {
            height: clamp(260px, 43%, 420px) !important;
            bottom: clamp(-18px, -2svh, -8px) !important;
        }

        .auth-slot header {
            margin-bottom: clamp(20px, 3svh, 30px) !important;
        }

        .auth-slot h2 {
            font-size: clamp(24px, 2.1vw, 30px) !important;
        }

        .auth-slot header p {
            margin-top: clamp(8px, 1.2svh, 12px) !important;
            font-size: clamp(14px, 1.1vw, 16px) !important;
        }

        .auth-slot form>.mb-7 {
            margin-bottom: clamp(16px, 2.1svh, 22px) !important;
        }

        .auth-slot form>.mb-8 {
            margin-bottom: clamp(16px, 2.2svh, 24px) !important;
        }

        .auth-slot label {
            margin-bottom: clamp(6px, .9svh, 8px) !important;
        }

        .auth-slot input:not([type="checkbox"]) {
            height: clamp(52px, 5.8svh, 60px) !important;
        }

        .auth-slot form>button {
            height: clamp(52px, 5.8svh, 60px) !important;
        }

        .auth-slot .my-10 {
            margin-block: clamp(18px, 2.8svh, 28px) !important;
        }

        .auth-slot .mt-16 {
            margin-top: clamp(22px, 4svh, 40px) !important;
        }

        body>footer {
            margin-top: clamp(8px, 1.4svh, 16px) !important;
        }

        @keyframes authReveal {
            from {
                opacity: 0;
                transform: translateY(18px) scale(.985);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes authSlideRight {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes authSlideLeft {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes authRise {
            from {
                opacity: 0;
                transform: translateY(34px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .auth-shell {
            animation: authReveal .65s cubic-bezier(.22, 1, .36, 1) both;
        }

        .auth-brand-logo {
            animation: authSlideRight .65s .18s cubic-bezier(.22, 1, .36, 1) both;
        }

        .auth-brand-copy {
            animation: authSlideRight .65s .3s cubic-bezier(.22, 1, .36, 1) both;
        }

        .auth-food-wrap {
            animation: authRise .75s .38s cubic-bezier(.22, 1, .36, 1) both;
        }

        .auth-form-panel {
            animation: authSlideLeft .65s .12s cubic-bezier(.22, 1, .36, 1) both;
        }

        .auth-slot>header {
            animation: authRise .55s .28s cubic-bezier(.22, 1, .36, 1) both;
        }

        .auth-slot>form>div,
        .auth-slot>form>button,
        .auth-slot>form>p {
            animation: authRise .5s cubic-bezier(.22, 1, .36, 1) both;
        }

        .auth-slot>form> :nth-child(2) {
            animation-delay: .34s;
        }

        .auth-slot>form> :nth-child(3) {
            animation-delay: .4s;
        }

        .auth-slot>form> :nth-child(4) {
            animation-delay: .46s;
        }

        .auth-slot>form> :nth-child(5) {
            animation-delay: .52s;
        }

        .auth-slot>form> :nth-child(6) {
            animation-delay: .58s;
        }

        .auth-slot>form> :nth-child(7) {
            animation-delay: .64s;
        }

        .auth-slot>form> :nth-child(8) {
            animation-delay: .7s;
        }

        body>footer {
            animation: authRise .5s .72s cubic-bezier(.22, 1, .36, 1) both;
        }

        @media (prefers-reduced-motion: reduce) {

            .auth-shell,
            .auth-brand-logo,
            .auth-brand-copy,
            .auth-food-wrap,
            .auth-form-panel,
            .auth-slot>header,
            .auth-slot>form>*,
            body>footer {
                animation: none !important;
            }
        }

        @media (max-width: 1279px) {
            .auth-page {
                --page-pad-x: clamp(16px, 2.5vw, 32px);
            }

            .auth-form-panel {
                padding-top: clamp(54px, 6svh, 74px) !important;
                padding-inline: clamp(30px, 4vw, 48px) !important;
            }

            .auth-slot {
                margin-top: clamp(20px, 3.4svh, 38px);
            }

            .auth-brand-logo {
                margin-top: clamp(22px, 4svh, 40px) !important;
            }
        }

        @media (max-width: 1023px) {
            .auth-shell {
                width: min(960px, calc(100vw - (var(--page-pad-x) * 2)));
            }

            .auth-brand {
                width: 45% !important;
                padding-inline: clamp(20px, 2.6vw, 28px) !important;
            }

            .auth-brand-copy {
                margin-top: clamp(20px, 3svh, 28px) !important;
            }

            .auth-brand-copy p {
                max-width: 300px;
            }

            .auth-form-panel {
                padding: clamp(52px, 6svh, 68px) clamp(22px, 3vw, 32px) clamp(24px, 3svh, 34px) !important;
            }

            .auth-slot {
                margin-top: clamp(14px, 2.8svh, 28px);
            }
        }

        @media (max-width: 767px) {
            .auth-page {
                --page-pad-x: clamp(10px, 4vw, 16px);
                --page-pad-y: clamp(10px, 2svh, 16px);
                justify-content: flex-start;
            }

            .auth-shell {
                width: 100%;
                min-height: 0;
                height: auto;
                flex-direction: column;
                border-radius: clamp(20px, 6vw, 24px);
            }

            .auth-brand {
                min-height: clamp(238px, 39svh, 310px);
                width: 100% !important;
                padding: clamp(16px, 4.2svh, 28px) clamp(18px, 6vw, 32px) 0 !important;
            }

            .auth-dots {
                left: clamp(18px, 6vw, 28px) !important;
                top: clamp(18px, 4svh, 28px) !important;
                gap: clamp(9px, 3.5vw, 13px) !important;
            }

            .auth-brand::after {
                bottom: -25%;
                height: 62%;
            }

            .auth-brand-copy {
                display: none;
            }

            .auth-brand-logo {
                margin-top: clamp(6px, 2svh, 14px) !important;
            }

            .auth-brand-logo img {
                width: clamp(66px, 21vw, 88px);
                height: clamp(66px, 21vw, 88px);
            }

            .auth-brand-logo h1 {
                font-size: clamp(26px, 8vw, 33px);
            }

            .auth-food-wrap {
                height: clamp(146px, 54%, 205px) !important;
                bottom: -2px !important;
            }

            .auth-language {
                right: clamp(16px, 5vw, 24px) !important;
                top: clamp(18px, 4svh, 28px) !important;
            }

            .auth-language button {
                height: clamp(44px, 12vw, 50px) !important;
                padding-inline: clamp(16px, 5vw, 22px) !important;
                gap: clamp(8px, 3vw, 12px) !important;
                font-size: 13px !important;
            }

            .auth-form-panel {
                border-radius: clamp(20px, 6vw, 24px) clamp(20px, 6vw, 24px) 0 0;
                margin-top: -16px;
                padding: clamp(54px, 12svh, 68px) clamp(18px, 5vw, 28px) clamp(24px, 4svh, 32px) !important;
            }

            .auth-slot {
                max-width: 100% !important;
                margin-top: 0;
            }

            .auth-slot header {
                margin-bottom: clamp(16px, 4.5vw, 22px) !important;
            }

            .auth-slot h2 {
                font-size: clamp(23px, 7vw, 28px) !important;
            }

            .auth-slot form>.mb-7 {
                margin-bottom: clamp(13px, 3.8vw, 18px) !important;
            }

            .auth-slot form>.mb-8 {
                margin-bottom: clamp(14px, 4vw, 20px) !important;
            }

            .auth-slot input:not([type="checkbox"]),
            .auth-slot form>button {
                height: clamp(50px, 13vw, 56px) !important;
            }

            .auth-slot .my-10 {
                margin-block: clamp(16px, 4.8vw, 24px) !important;
            }

            .auth-slot .mt-16 {
                margin-top: clamp(18px, 5.5vw, 28px) !important;
            }
        }

        @media (max-width: 399px) {
            .auth-page {
                --page-pad-x: 10px;
            }

            .auth-brand {
                min-height: clamp(224px, 36svh, 262px);
            }

            .auth-form-panel {
                padding-top: clamp(48px, 12svh, 60px) !important;
            }

            .auth-slot form>.mb-7 {
                margin-bottom: 16px !important;
            }
        }

        @media (max-width: 359px) {
            .auth-form-panel {
                padding-inline: 14px !important;
            }

            .auth-slot h2 {
                font-size: 22px !important;
            }

            .auth-slot header p,
            .auth-slot label,
            .auth-slot a,
            .auth-slot p {
                font-size: 13px !important;
            }
        }
    </style>
</head>

<body class="auth-page min-h-screen overflow-x-hidden font-sans text-text antialiased flex flex-col items-center justify-center px-8 py-6">
    <main class="auth-shell relative flex overflow-hidden rounded-[28px] bg-[#FAD9D5]">
        <section class="auth-brand relative flex w-[48.3%] shrink-0 flex-col items-center px-8 pt-10 md:pt-14">
            <div class="auth-dots absolute left-10 top-10 grid grid-cols-5 gap-[17px] opacity-80" aria-hidden="true">
                @for ($dot = 0; $dot < 20; $dot++)
                    <span class="block size-[5px] rounded-full bg-[#ef91a6]"></span>
                @endfor
            </div>
            <div class="auth-brand-logo relative z-10 mt-[105px] flex flex-col items-center md:mt-[112px]">
                <img src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt="" class="h-[108px] w-[108px] object-contain">
                <h1 class="-mt-2 font-brand text-[38px] font-extrabold italic leading-none tracking-[-.055em] text-[#e8758d]">
                    Bakso Cinta</h1>
                <div class="mt-3 flex items-center gap-3 text-[15px] font-semibold tracking-[.48em] text-[#b99576]">
                    <span class="h-px w-12 bg-[#c6a98c]"></span><span>CIAMIS</span><span class="h-px w-12 bg-[#c6a98c]"></span>
                </div>
            </div>
            <div class="auth-brand-copy relative z-10 mt-10 text-center">
                <h2 class="font-heading text-[25px] font-bold text-[#74382f]">{{ __('auth_page.welcome_back') }}</h2>
                <p class="mx-auto mt-3 max-w-[390px] text-[16px] font-medium leading-[1.65] text-[#776f6d]">
                    {{ __('auth_page.welcome_tagline') }}</p>
            </div>
            <div class="auth-food-wrap absolute bottom-[-2%] left-[-3%] z-10 flex h-[43%] w-[106%] items-end justify-center">
                <span class="absolute left-[8%] top-[8%] h-9 w-4 rotate-[-28deg] rounded-full bg-[#f49aae]" aria-hidden="true"></span>
                <span class="absolute left-[5%] top-[17%] h-11 w-6 rotate-[-38deg] rounded-full bg-[#f49aae]" aria-hidden="true"></span>
                <span class="absolute right-[13%] top-[6%] h-9 w-[6px] rotate-[18deg] rounded-full bg-[#f28ca3]" aria-hidden="true"></span>
                <span class="absolute right-[9%] top-[11%] h-9 w-[6px] rotate-[43deg] rounded-full bg-[#f28ca3]" aria-hidden="true"></span>
                <span class="absolute right-[6%] top-[18%] h-7 w-[6px] rotate-[70deg] rounded-full bg-[#f28ca3]" aria-hidden="true"></span>
                <img src="{{ asset('img/bakso.png') }}" alt="Semangkuk Bakso Cinta" class="auth-food h-full w-full object-contain object-bottom">
            </div>
        </section>
        <section class="auth-form-panel relative z-20 flex min-w-0 flex-1 items-center justify-center bg-white px-8 py-12 md:px-12 lg:px-16">
            <div class="auth-language absolute right-6 top-7 z-30 md:right-10 md:top-11" x-data="{ open: false }">
                <button type="button" @click="open = !open" :aria-expanded="open"
                    class="flex h-[56px] items-center gap-4 rounded-full border border-[#ddd7d5] bg-white px-7 text-[14px] font-semibold text-[#433b39] hover:border-primary hover:bg-[#fffafa] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                    <svg class="size-5 text-[#ed7f97]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M3.6 9h16.8M3.6 15h16.8M12 3c2.4 2.5 3.6 5.5 3.6 9S14.4 18.5 12 21c-2.4-2.5-3.6-5.5-3.6-9S9.6 5.5 12 3Z">
                        </path>
                    </svg>
                    <span>{{ __('auth_page.language') }}</span>
                    <svg class="size-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>
                <div x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false"
                    class="absolute right-0 mt-2 w-52 overflow-hidden rounded-2xl border border-border bg-white p-1.5 shadow-xl">
                    <a href="{{ route('locale.switch', 'id') }}"
                        class="block rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-surface {{ app()->getLocale() === 'id' ? 'text-primary-strong' : 'text-text-muted' }}">Bahasa
                        Indonesia</a>
                    <a href="{{ route('locale.switch', 'en') }}"
                        class="block rounded-xl px-4 py-2.5 text-sm font-semibold hover:bg-surface {{ app()->getLocale() === 'en' ? 'text-primary-strong' : 'text-text-muted' }}">English</a>
                </div>
            </div>
            <div class="auth-slot w-full max-w-[484px]">{{ $slot }}</div>
        </section>
    </main>
    <footer class="mt-6 text-center text-[13px] font-medium text-[#777270]">
        {{ __('auth_page.copyright', ['year' => date('Y')]) }}</footer>
</body>

</html>
