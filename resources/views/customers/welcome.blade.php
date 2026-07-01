<x-customer-welcome-layout>
    <style>
        .welcome-page {
            --pink: #f75c83;
            --pink-soft: #fbd8d5;
            --ink: #251817;
            --muted: #77706e;
            min-height: 100svh;
            padding: clamp(10px, 2.4vw, 26px);
            background: radial-gradient(circle at 50% 0, #f7dede 0, #fff8f6 42%, #fffdf9 100%);
        }

        .welcome-card {
            position: relative;
            min-height: calc(100svh - clamp(20px, 4.8vw, 52px));
            overflow: hidden;
            border-radius: clamp(20px, 2.2vw, 28px);
            background: linear-gradient(112deg, #fffdf8 0%, #fffaf7 58%, #fff7f3 100%);
            box-shadow: 0 10px 34px rgba(145, 82, 83, .14);
            isolation: isolate;
        }

        .welcome-dots {
            position: absolute;
            top: 32px;
            left: 36px;
            display: grid;
            grid-template-columns: repeat(4, 5px);
            gap: 14px;
            z-index: 3;
        }

        .welcome-dots span {
            width: 5px;
            height: 5px;
            border-radius: 999px;
            background: #f27d9a;
        }

        .welcome-top-orb {
            position: absolute;
            top: -82px;
            left: 46%;
            width: 220px;
            height: 160px;
            border-radius: 0 0 50% 50%;
            background: linear-gradient(180deg, #fbe2df, #fbd1ce);
            opacity: .72;
        }

        .welcome-language {
            position: absolute;
            top: 34px;
            right: 34px;
            z-index: 20;
        }

        .welcome-language button {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 210px;
            height: 48px;
            padding: 0 20px;
            border: 1px solid #d8dcdd;
            border-radius: 999px;
            background: rgba(255, 255, 255, .86);
            color: #2d2726;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 2px 5px rgba(67, 46, 45, .04);
        }

        .welcome-language-menu {
            position: absolute;
            top: 56px;
            right: 0;
            width: 100%;
            overflow: hidden;
            border: 1px solid #eadbd8;
            border-radius: 14px;
            background: white;
            box-shadow: 0 12px 28px rgba(90, 56, 55, .13);
        }

        .welcome-language-menu a {
            display: block;
            padding: 11px 18px;
            font-size: 13px;
            font-weight: 600;
        }

        .welcome-language-menu a:hover {
            background: #fff4f4;
            color: var(--pink);
        }

        .welcome-grid {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 49% 51%;
            min-height: inherit;
        }

        .welcome-copy {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 108px 30px 54px 54px;
            text-align: center;
        }

        .welcome-copy-inner {
            width: min(100%, 440px);
            animation: welcome-in-left .7s ease-out both;
        }

        .welcome-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 27px;
        }

        .welcome-logo img {
            width: 91px;
            height: 91px;
            object-fit: contain;
        }

        .welcome-logo h2 {
            margin-top: -12px;
            color: #eb557b;
            font-family: var(--font-brand);
            font-size: clamp(29px, 3vw, 41px);
            font-weight: 800;
            font-style: italic;
            line-height: 1;
            letter-spacing: -.055em;
        }

        .welcome-logo-sub {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 10px;
            color: #af8a6c;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .52em;
        }

        .welcome-logo-sub::before,
        .welcome-logo-sub::after {
            content: '';
            width: 42px;
            height: 1px;
            background: #c6a98c;
        }

        .welcome-greeting {
            color: var(--pink);
            font-size: clamp(15px, 1.5vw, 18px);
            font-weight: 700;
        }

        .welcome-title {
            max-width: 410px;
            margin: 13px auto 0;
            color: var(--ink);
            font-family: var(--font-heading);
            font-size: clamp(25px, 2.65vw, 36px);
            font-weight: 800;
            line-height: 1.22;
            letter-spacing: -.035em;
        }

        .welcome-title-heart {
            display: inline-block;
            margin-left: 8px;
            color: var(--pink);
            font-size: 1.18em;
            font-weight: 500;
            transform: rotate(-8deg);
        }

        .welcome-description {
            margin: 17px auto 0;
            color: var(--muted);
            font-size: clamp(13px, 1.18vw, 15px);
            line-height: 1.6;
        }

        .welcome-table {
            display: grid;
            grid-template-columns: 62px 1fr;
            width: min(100%, 270px);
            min-height: 78px;
            margin: 20px auto 0;
            padding: 9px 22px 9px 10px;
            border: 1px solid #efcfd0;
            border-radius: 17px;
            background: rgba(255, 255, 255, .76);
            box-shadow: 0 2px 8px rgba(102, 63, 62, .03);
            text-align: left;
        }

        .welcome-table-icon {
            display: grid;
            place-items: center;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: #fff0ef;
            color: var(--pink);
        }

        .welcome-table-text {
            align-self: center;
            padding-left: 14px;
        }

        .welcome-table-label {
            display: block;
            color: #716967;
            font-size: 12px;
            font-weight: 600;
        }

        .welcome-table-number {
            display: block;
            color: var(--pink);
            font-family: var(--font-heading);
            font-size: 28px;
            font-weight: 700;
            line-height: 1.05;
        }

        .welcome-cta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: min(100%, 382px);
            min-height: 56px;
            margin: 17px auto 0;
            border-radius: 11px;
            background: linear-gradient(100deg, #f63769, #f24e76);
            color: white;
            font-size: 19px;
            font-weight: 700;
            box-shadow: 0 10px 22px rgba(241, 74, 112, .16);
        }

        .welcome-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 13px 26px rgba(241, 74, 112, .25);
        }

        .welcome-cta:active {
            transform: scale(.985);
        }

        .welcome-security {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            gap: 11px;
            margin: 24px auto 0;
            color: #746d6b;
            font-size: 12px;
            line-height: 1.5;
        }

        .welcome-security svg {
            flex: none;
            margin-top: 2px;
            color: var(--pink);
        }

        .welcome-visual {
            position: relative;
            min-width: 0;
            animation: welcome-in-right .8s .14s ease-out both;
        }

        .welcome-pink-field {
            position: absolute;
            right: -13%;
            bottom: -13%;
            width: 105%;
            height: 53%;
            border-radius: 50% 0 0 0;
            background: radial-gradient(circle at 46% 18%, #ffb7bd 0, #fa8da1 50%, #fb718f 100%);
            transform: rotate(5deg);
        }

        .welcome-food {
            position: absolute;
            z-index: 3;
            right: -2%;
            top: 31%;
            width: 98%;
            max-width: 660px;
            filter: drop-shadow(0 22px 18px rgba(104, 49, 47, .20));
        }

        .welcome-heart-outline {
            position: absolute;
            top: 21%;
            right: -2%;
            width: 160px;
            height: 190px;
            color: #f8cece;
            opacity: .75;
        }

        .welcome-splash {
            position: absolute;
            z-index: 4;
            color: #f77795;
        }

        .welcome-splash-left {
            top: 31%;
            left: 4%;
            width: 65px;
        }

        .welcome-splash-right {
            top: 38%;
            right: 5%;
            width: 58px;
        }

        @keyframes welcome-in-left {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes welcome-in-right {
            from {
                opacity: 0;
                transform: translateX(35px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @media (max-width: 1023px) {
            .welcome-page {
                padding: 0;
            }

            .welcome-card {
                min-height: 100svh;
                border-radius: 0;
                box-shadow: none;
            }

            .welcome-grid {
                grid-template-columns: 58% 42%;
            }

            .welcome-copy {
                padding: 105px 24px 44px 42px;
            }

            .welcome-food {
                top: 39%;
                right: -18%;
                width: 128%;
            }

            .welcome-pink-field {
                width: 140%;
                height: 54%;
                right: -48%;
            }

            .welcome-heart-outline {
                display: none;
            }
        }

        @media (max-width: 767px) {
            .welcome-card {
                min-height: max(100svh, 960px);
            }

            .welcome-dots {
                display: none;
            }

            .welcome-top-orb {
                display: none;
            }

            .welcome-language {
                top: 16px;
                right: 15px;
            }

            .welcome-language button {
                min-width: 0;
                width: auto;
                height: 31px;
                gap: 5px;
                padding: 0 9px;
                font-size: 8px;
            }

            .welcome-language button svg {
                width: 11px;
                height: 11px;
            }

            .welcome-language-menu {
                top: 37px;
                min-width: 120px;
            }

            .welcome-language-menu a {
                padding: 8px 10px;
                font-size: 10px;
            }

            .welcome-grid {
                display: block;
            }

            .welcome-copy {
                display: block;
                padding: 66px 20px 0;
            }

            .welcome-copy-inner {
                width: 100%;
            }

            .welcome-logo {
                margin-bottom: 23px;
            }

            .welcome-logo img {
                width: 78px;
                height: 78px;
            }

            .welcome-logo h2 {
                font-size: 28px;
            }

            .welcome-logo-sub {
                margin-top: 8px;
                font-size: 8px;
                gap: 7px;
            }

            .welcome-logo-sub::before,
            .welcome-logo-sub::after {
                width: 30px;
            }

            .welcome-greeting {
                font-size: 12px;
            }

            .welcome-title {
                max-width: 290px;
                margin-top: 11px;
                font-size: 21px;
                line-height: 1.22;
            }

            .welcome-description {
                max-width: 290px;
                margin-top: 14px;
                font-size: 10px;
                line-height: 1.65;
            }

            .welcome-table {
                grid-template-columns: 48px 1fr;
                width: min(100%, 245px);
                min-height: 65px;
                margin-top: 16px;
                padding: 8px 16px 8px 8px;
                border-radius: 14px;
            }

            .welcome-table-icon {
                width: 48px;
                height: 48px;
            }

            .welcome-table-text {
                padding-left: 12px;
            }

            .welcome-table-label {
                font-size: 9px;
            }

            .welcome-table-number {
                font-size: 23px;
            }

            .welcome-cta {
                width: min(100%, 280px);
                min-height: 45px;
                margin-top: 15px;
                border-radius: 7px;
                font-size: 13px;
            }

            .welcome-cta svg {
                width: 17px;
                height: 17px;
            }

            .welcome-security {
                max-width: 250px;
                margin-top: 19px;
                font-size: 9px;
                text-align: left;
            }

            .welcome-visual {
                position: absolute;
                inset: auto 0 0;
                height: 310px;
            }

            .welcome-pink-field {
                right: -24%;
                bottom: -30%;
                width: 125%;
                height: 115%;
                border-radius: 55% 0 0;
                transform: rotate(-4deg);
            }

            .welcome-food {
                top: 35px;
                right: -7%;
                width: 112%;
                max-width: 420px;
            }

            .welcome-splash-left {
                display: none;
            }

            .welcome-splash-right {
                top: 25px;
                right: 10%;
                width: 44px;
            }
        }

        @media (min-width: 360px) and (max-width: 767px) {
            .welcome-card {
                min-height: max(100svh, 1000px);
            }

            .welcome-copy {
                padding-inline: 26px;
            }

            .welcome-logo img {
                width: 86px;
                height: 86px;
            }

            .welcome-logo h2 {
                font-size: 31px;
            }

            .welcome-title {
                font-size: 23px;
                max-width: 320px;
            }

            .welcome-description {
                font-size: 11px;
                max-width: 320px;
            }

            .welcome-table {
                width: 260px;
            }

            .welcome-cta {
                width: 300px;
                min-height: 48px;
                font-size: 14px;
            }

            .welcome-security {
                max-width: 275px;
                font-size: 10px;
            }

            .welcome-visual {
                height: 340px;
            }
        }

        @media (min-width: 400px) and (max-width: 767px) {
            .welcome-card {
                min-height: max(100svh, 1040px);
            }

            .welcome-copy {
                padding-top: 72px;
            }

            .welcome-logo {
                margin-bottom: 27px;
            }

            .welcome-title {
                font-size: 25px;
            }

            .welcome-description {
                font-size: 12px;
            }

            .welcome-table {
                width: 275px;
                min-height: 70px;
            }

            .welcome-cta {
                width: 320px;
                min-height: 51px;
                font-size: 15px;
            }

            .welcome-security {
                max-width: 295px;
                font-size: 10px;
            }

            .welcome-visual {
                height: 365px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .welcome-copy-inner,
            .welcome-visual {
                animation: none;
            }
        }
    </style>

    <main class="welcome-page">
        <section class="welcome-card">
            <div class="welcome-dots" aria-hidden="true">
                @for ($dot = 0; $dot < 16; $dot++)
                    <span></span>
                @endfor
            </div>
            <div class="welcome-top-orb" aria-hidden="true"></div>

            <div class="welcome-language" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" @click="open = !open" :aria-expanded="open" aria-haspopup="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f7557b" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M3.6 9h16.8M3.6 15h16.8M12 3c2.4 2.5 3.6 5.5 3.6 9S14.4 18.5 12 21c-2.4-2.5-3.6-5.5-3.6-9S9.6 5.5 12 3Z" />
                    </svg>
                    <span>{{ app()->getLocale() === 'en' ? 'English' : 'Bahasa Indonesia' }}</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                        :style="open && 'transform:rotate(180deg)'" aria-hidden="true">
                        <path d="m6 9 6 6 6-6" />
                    </svg>
                </button>
                <div class="welcome-language-menu" x-cloak x-show="open" x-transition>
                    <a href="{{ route('locale.switch', 'id') }}">Bahasa Indonesia</a>
                    <a href="{{ route('locale.switch', 'en') }}">English</a>
                </div>
            </div>

            <div class="welcome-grid">
                <div class="welcome-copy">
                    <div class="welcome-copy-inner">
                        <div class="welcome-logo">
                            <img src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt="">
                            <h2>Bakso Cinta</h2>
                            <div class="welcome-logo-sub">CIAMIS</div>
                        </div>

                        <p class="welcome-greeting">❧ {{ __('Selamat Datang!') }} ❧</p>
                        <h1 class="welcome-title">{{ __('Terima kasih telah memilih') }}<br>{{ $branch }} <span class="welcome-title-heart">♡</span></h1>
                        <p class="welcome-description">
                            {{ __('Scan QR ini telah terhubung ke meja Anda.') }}<br>{{ __('Silakan mulai memesan makanan dan minuman favorit Anda.') }}</p>

                        <div class="welcome-table">
                            <span class="welcome-table-icon" aria-hidden="true">
                                <svg width="31" height="31" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="16" cy="7" r="3" />
                                    <path d="M10 14v11M22 14v11M8 17h16v6H8zM5 18v9M27 18v9M9 27h4M19 27h4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <span class="welcome-table-text">
                                <span class="welcome-table-label">{{ __('Meja Anda') }}</span>
                                <strong
                                    class="welcome-table-number">{{ preg_match('/^[A-Za-z]/', (string) $table) ? $table : 'A' . str_pad($table, 2, '0', STR_PAD_LEFT) }}</strong>
                            </span>
                        </div>

                        <a class="welcome-cta" href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table]) }}">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M4 17h16M6 17a6 6 0 0 1 12 0M12 8v3M3 20h18" stroke-linecap="round" />
                            </svg>
                            {{ __('Mulai Pesan') }}
                        </a>

                        <p class="welcome-security">
                            <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M12 3 5 6v5c0 4.8 2.8 8.4 7 10 4.2-1.6 7-5.2 7-10V6l-7-3Z" />
                                <path d="m9.5 12 1.6 1.6 3.6-4" />
                            </svg>
                            <span>{{ __('Data Anda aman. Pesanan Anda akan') }}<br>{{ __('dikirim langsung ke kasir kami.') }}</span>
                        </p>
                    </div>
                </div>

                <div class="welcome-visual" aria-hidden="true">
                    <div class="welcome-pink-field"></div>
                    <svg class="welcome-heart-outline" viewBox="0 0 180 210" fill="none" stroke="currentColor" stroke-width="3">
                        <path d="M92 192 23 119C-25 67 39 1 92 53 145 1 209 67 161 119Z" />
                    </svg>
                    <svg class="welcome-splash welcome-splash-left" viewBox="0 0 70 70" fill="currentColor">
                        <ellipse cx="19" cy="40" rx="11" ry="22" transform="rotate(-28 19 40)" />
                        <ellipse cx="48" cy="18" rx="8" ry="17" transform="rotate(8 48 18)" />
                    </svg>
                    <svg class="welcome-splash welcome-splash-right" viewBox="0 0 70 70" fill="none" stroke="currentColor" stroke-width="6"
                        stroke-linecap="round">
                        <path d="m12 31 9-22M31 39l18-18M45 53l20-8" />
                    </svg>
                    <img class="welcome-food" src="{{ asset('img/bakso.png') }}" alt="">
                </div>
            </div>
        </section>
    </main>
</x-customer-welcome-layout>
