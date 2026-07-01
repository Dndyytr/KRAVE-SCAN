<x-customer-layout :branch="$branch" :table="$table">
    @php $tableLabel=preg_match('/^[A-Za-z]/',(string)$table)?$table:'A'.str_pad($table,2,'0',STR_PAD_LEFT); @endphp
    <style>
        body {
            padding: 0 !important;
            background: #fff7f5 !important
        }

        body>header,
        body>nav {
            display: none !important
        }

        body>main {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important
        }

        .scan-page {
            --pink: #fa4d77;
            --ink: #2b1d1c;
            --muted: #786f6d;
            min-height: 100svh;
            padding: 18px;
            background: radial-gradient(circle at 50% 0, #f6dfdc, #fff9f7 48%, #fffdf9)
        }

        .scan-shell {
            display: grid;
            grid-template-columns: 238px minmax(0, 1fr);
            width: min(100%, 1400px);
            height: calc(100svh - 36px);
            min-height: 0;
            margin: auto;
            overflow: hidden;
            border-radius: 25px;
            background: #fff;
            box-shadow: 0 10px 35px #82494924
        }

        .scan-side {
            display: flex;
            flex-direction: column;
            position: relative;
            height: 100%;
            overflow-y: auto;
            padding: 30px 22px 28px;
            border-right: 1px solid #f4e5e0;
            background: linear-gradient(160deg, #fffaf7, #fff5f0)
        }

        .scan-dots {
            display: grid;
            grid-template-columns: repeat(4, 5px);
            gap: 12px;
            position: absolute;
            top: 26px;
            left: 26px
        }

        .scan-dots i {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #f17996
        }

        .scan-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 68px 0 35px
        }

        .scan-brand img {
            width: 78px;
            height: 78px
        }

        .scan-brand strong,
        .scan-mobile-brand strong {
            margin-top: -11px;
            color: #e95578;
            font: italic 800 28px/1 var(--font-brand);
            letter-spacing: -.055em
        }

        .scan-brand small {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 9px;
            color: #ad8a6d;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .48em
        }

        .scan-brand small:before,
        .scan-brand small:after {
            content: '';
            width: 30px;
            height: 1px;
            background: #c8ab90
        }

        .scan-table {
            display: grid;
            grid-template-columns: 48px 1fr;
            align-items: center;
            padding: 10px 14px;
            border-radius: 15px;
            background: #ffffffcc;
            box-shadow: 0 5px 20px #ae6f7012
        }

        .scan-table-icon {
            display: grid;
            place-items: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #fff0ef;
            color: var(--pink)
        }

        .scan-table small {
            display: block;
            color: #706865;
            font-size: 10px
        }

        .scan-table strong {
            color: var(--pink);
            font: 700 23px/1.1 var(--font-heading)
        }

        .scan-nav {
            display: grid;
            gap: 7px;
            margin-top: 22px
        }

        .scan-nav a {
            display: flex;
            align-items: center;
            gap: 13px;
            min-height: 44px;
            padding: 0 14px;
            border-radius: 9px;
            color: #4c403e;
            font-size: 11px;
            font-weight: 600;
            transition: .25s ease
        }

        .scan-nav a:hover {
            color: var(--pink);
            background: #fff0f0
        }

        .scan-nav .active {
            color: #fff;
            background: linear-gradient(100deg, #f54772, #fc708d);
            box-shadow: 0 8px 18px #f24c722e
        }

        .scan-help {
            margin-top: auto;
            padding: 18px 15px;
            border-radius: 15px;
            background: linear-gradient(120deg, #fff0ee, #fde2e0);
            color: #706765;
            font-size: 10px;
            line-height: 1.55;
            text-align: center
        }

        .scan-help svg {
            margin: 0 auto 7px;
            color: var(--pink)
        }

        .scan-main {
            min-width: 0;
            height: 100%;
            overflow-y: auto;
            padding: 36px 30px 25px
        }

        .scan-mobile-head {
            display: none
        }

        .scan-head {
            display: flex;
            justify-content: space-between;
            gap: 20px
        }

        .scan-kicker {
            color: var(--pink);
            font-size: 12px
        }

        .scan-title {
            margin-top: 6px;
            color: var(--ink);
            font: 800 clamp(25px, 2.3vw, 31px)/1.15 var(--font-heading);
            letter-spacing: -.03em
        }

        .scan-title em {
            display: inline-grid;
            place-items: center;
            width: 25px;
            height: 25px;
            margin-left: 7px;
            border: 1.5px solid var(--pink);
            border-radius: 8px;
            color: var(--pink);
            font: 700 9px var(--font-sans);
            vertical-align: middle
        }

        .scan-subtitle {
            max-width: 470px;
            margin-top: 8px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.65
        }

        .scanner {
            margin-top: 20px;
            padding: 14px;
            border: 1px solid #f1e5e2;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 6px 22px #7b4c4612
        }

        .scanner-top {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            height: 38px;
            border-bottom: 1px solid #f0dfdc;
            color: var(--pink);
            font-size: 11px;
            font-weight: 700
        }

        .camera-stage {
            position: relative;
            display: grid;
            place-items: center;
            min-height: 260px;
            margin-top: 14px;
            overflow: hidden;
            border: 1.5px dashed #f4a5b6;
            border-radius: 14px;
            background: radial-gradient(circle at 75% 45%, #fde7e5, #fff8f6 55%, #fff)
        }

        .camera-empty {
            text-align: center
        }

        .camera-empty svg {
            margin: auto;
            color: var(--pink)
        }

        .camera-empty h2 {
            margin-top: 12px;
            font: 700 14px var(--font-heading)
        }

        .camera-empty p {
            margin-top: 5px;
            color: var(--muted);
            font-size: 10px
        }

        .camera-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 150px;
            height: 42px;
            margin-top: 15px;
            border-radius: 10px;
            color: #fff;
            background: linear-gradient(100deg, #f74771, #fa5b7e);
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 8px 18px #f24d7324;
            transition: .25s ease
        }

        .camera-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 11px 22px #f24d7338
        }

        .camera-video {
            width: 100%;
            height: 330px;
            object-fit: cover;
            background: #231918
        }

        .camera-actions {
            position: absolute;
            right: 0;
            bottom: 14px;
            left: 0;
            display: flex;
            justify-content: center;
            gap: 10px
        }

        .camera-actions button {
            height: 42px;
            padding: 0 18px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700
        }

        .capture {
            color: #fff;
            background: var(--pink)
        }

        .cancel {
            color: #564846;
            background: #fff
        }

        .scan-error {
            margin-top: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            color: #9a344b;
            background: #fff0f2;
            font-size: 11px;
            text-align: center
        }

        .result-section {
            margin-top: 21px
        }

        .result-heading {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font: 700 15px var(--font-heading)
        }

        .result-heading span {
            color: var(--pink)
        }

        .result-card {
            display: grid;
            grid-template-columns: minmax(250px, 43%) 1fr;
            gap: 25px;
            padding: 8px;
            border: 1px solid #f0e5e2;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 7px 22px #714a4514
        }

        .result-image {
            position: relative;
            min-height: 210px;
            overflow: hidden;
            border-radius: 12px;
            background: #fff0ec
        }

        .result-image img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .best {
            position: absolute;
            top: 9px;
            left: 9px;
            padding: 5px 11px;
            border-radius: 99px;
            color: #fff;
            background: var(--pink);
            font-size: 9px;
            font-weight: 700
        }

        .result-copy {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 13px 15px 13px 0
        }

        .result-copy h2 {
            font: 800 21px var(--font-heading)
        }

        .result-copy p {
            max-width: 400px;
            margin-top: 8px;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.65
        }

        .confidence {
            margin-top: 15px;
            color: var(--pink);
            font: 800 26px var(--font-heading)
        }

        .confidence-label {
            font-size: 9px;
            color: #a19896
        }

        .confidence-bar {
            display: inline-block;
            width: 125px;
            height: 5px;
            margin-left: 12px;
            overflow: hidden;
            border-radius: 99px;
            background: #eee
        }

        .confidence-bar i {
            display: block;
            height: 100%;
            background: var(--pink)
        }

        .result-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 13px
        }

        .result-price {
            color: var(--pink);
            font-size: 16px;
            font-weight: 800
        }

        .result-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 112px;
            height: 38px;
            border-radius: 9px;
            color: #fff;
            background: var(--pink);
            font-size: 10px;
            font-weight: 700
        }

        .similar {
            margin-top: 19px
        }

        .similar h2 {
            margin-bottom: 10px;
            font: 700 13px var(--font-heading)
        }

        .similar-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px
        }

        .similar-card {
            overflow: hidden;
            border: 1px solid #f0e5e2;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 5px 15px #714a4510
        }

        .similar-image {
            height: 95px;
            background: linear-gradient(135deg, #fff0ec, #f8dfda)
        }

        .similar-image img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .similar-placeholder {
            display: grid;
            place-items: center;
            width: 100%;
            height: 100%;
            color: #dc8297;
            font: 800 28px var(--font-heading)
        }

        .similar-body {
            padding: 9px
        }

        .similar-body h3 {
            overflow: hidden;
            font: 700 10px var(--font-heading);
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .similar-body strong {
            display: block;
            margin-top: 6px;
            font-size: 9px
        }

        .scan-note {
            margin-top: 20px;
            color: #aaa19f;
            font-size: 8px;
            text-align: center
        }

        .scan-loading {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: grid;
            place-items: center;
            background: #2d1c1b61;
            backdrop-filter: blur(4px)
        }

        .scan-loading-card {
            width: min(90%, 300px);
            padding: 25px;
            border-radius: 18px;
            background: #fff;
            text-align: center
        }

        .scan-spinner {
            width: 43px;
            height: 43px;
            margin: 0 auto 12px;
            border: 4px solid #f8c3cf;
            border-top-color: var(--pink);
            border-radius: 50%;
            animation: spin .8s linear infinite
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        @media(max-width:899px) {
            .scan-page {
                padding: 0
            }

            .scan-shell {
                display: block;
                height: auto;
                min-height: 100svh;
                overflow: hidden;
                border-radius: 0;
                box-shadow: none
            }

            .scan-side,
            .scan-head {
                display: none
            }

            .scan-main {
                height: auto;
                min-height: 100svh;
                overflow: visible;
                padding: 13px 18px 92px
            }

            .scan-mobile-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 14px
            }

            .scan-back {
                display: grid;
                place-items: center;
                width: 31px;
                height: 31px;
                border: 1px solid #eee3e0;
                border-radius: 50%;
                background: #fff
            }

            .scan-mobile-title {
                display: flex;
                align-items: center;
                gap: 7px;
                font: 800 17px var(--font-heading)
            }

            .scan-mobile-title b {
                display: grid;
                place-items: center;
                width: 20px;
                height: 20px;
                border: 1px solid var(--pink);
                border-radius: 6px;
                color: var(--pink);
                font-size: 7px
            }

            .scan-mobile-table {
                padding: 7px 10px;
                border: 1px solid #eee1de;
                border-radius: 9px;
                color: var(--pink);
                font-size: 11px;
                font-weight: 700
            }

            .scanner {
                margin-top: 0;
                padding: 10px
            }

            .camera-stage {
                min-height: 280px
            }

            .camera-video {
                height: 360px
            }

            .result-card {
                grid-template-columns: 42% 1fr;
                gap: 13px
            }

            .result-image {
                min-height: 190px
            }

            .result-copy {
                padding: 9px 9px 9px 0
            }

            .result-copy h2 {
                font-size: 17px
            }

            .result-copy p {
                font-size: 9px
            }

            .confidence {
                font-size: 23px
            }

            .similar-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 8px
            }

            .similar-image {
                height: 80px
            }

            .similar-body {
                padding: 7px
            }
        }

        @media(max-width:767px) {
            body>nav {
                display: flex !important
            }

            .scan-main {
                padding-bottom: 82px
            }

            .camera-stage {
                min-height: 230px
            }

            .camera-video {
                height: 300px
            }

            .result-heading {
                font-size: 12px
            }

            .result-card {
                grid-template-columns: 1fr;
                gap: 0;
                padding: 6px
            }

            .result-image {
                min-height: 210px
            }

            .result-copy {
                padding: 14px 10px
            }

            .result-copy h2 {
                font-size: 18px
            }

            .result-copy p {
                font-size: 10px
            }

            .result-foot {
                margin-top: 10px
            }

            .similar-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 9px
            }

            .similar-image {
                height: 105px
            }
        }

        @media(max-width:399px) {
            .scan-main {
                padding-inline: 12px
            }

            .scan-mobile-title {
                font-size: 15px
            }

            .camera-stage {
                min-height: 215px
            }

            .camera-video {
                height: 270px
            }

            .camera-actions button {
                padding-inline: 13px
            }

            .result-image {
                min-height: 185px
            }

            .similar-image {
                height: 90px
            }
        }

        @media(max-width:359px) {
            .scan-main {
                padding-inline: 10px
            }

            .scan-mobile-title {
                font-size: 14px
            }

            .scan-mobile-table {
                padding-inline: 7px
            }

            .camera-empty h2 {
                font-size: 12px
            }

            .camera-video {
                height: 245px
            }

            .result-image {
                min-height: 170px
            }
        }

        @media(prefers-reduced-motion:reduce) {

            .camera-button,
            .scan-nav a {
                transition: none
            }
        }
    </style>
    <div class="scan-page" x-data="scanner()">
        <div class="scan-shell">
            <aside class="scan-side">
                <div class="scan-dots" aria-hidden="true">
                    @for ($i = 0; $i < 16; $i++)
                        <i></i>
                    @endfor
                </div>
                <div class="scan-brand"><img src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt=""><strong>Bakso
                        Cinta</strong><small>CIAMIS</small></div>
                <div class="scan-table"><span class="scan-table-icon"><svg width="26" height="26" viewBox="0 0 32 32" fill="none" stroke="currentColor"
                            stroke-width="1.8">
                            <circle cx="16" cy="7" r="3" />
                            <path d="M10 14v11M22 14v11M8 17h16v6H8zM5 18v9M27 18v9M9 27h4M19 27h4" />
                        </svg></span><span><small>Meja Anda</small><strong>{{ $tableLabel }}</strong></span></div>
                <nav class="scan-nav" aria-label="Navigasi pelanggan"><a
                        href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table]) }}"><svg width="19" height="19"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 17h16M6 17a6 6 0 0 1 12 0M12 8v3M3 20h18" />
                        </svg>Menu</a><a class="active" href="{{ route('customer.ai-scan', ['branch_code' => $branch_code]) }}"><svg width="19"
                            height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h4l1.5-2h5L16 7h4v12H4z" />
                            <circle cx="12" cy="13" r="3" />
                        </svg>AI Scan</a><a href="{{ route('customer.cart', ['branch_code' => $branch_code]) }}"><svg width="19" height="19"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 4h2l2.2 10.5h10.9L21 7H6M9 20h.01M18 20h.01" />
                        </svg>Keranjang</a><a href="{{ route('customer.payment', ['branch_code' => $branch_code]) }}"><svg width="19" height="19"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3z" />
                        </svg>Pembayaran</a><a href="{{ route('customer.order.status', ['branch_code' => $branch_code]) }}"><svg width="19" height="19"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M7 3h10v18H7zM10 8h4M10 12h4" />
                        </svg>Pesanan</a></nav>
                <div class="scan-help"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 13v-2a8 8 0 0 1 16 0v2M4 13h3v6H5a1 1 0 0 1-1-1v-5ZM20 13h-3v6h2a1 1 0 0 0 1-1v-5Z" />
                    </svg><strong>Butuh bantuan?</strong><br>Hubungi staf kami jika ada kendala.</div>
            </aside>
            <main class="scan-main">
                <header class="scan-mobile-head"><a class="scan-back"
                        href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table]) }}" aria-label="Kembali">←</a>
                    <h1 class="scan-mobile-title">AI Image Search <b>AI</b></h1><span class="scan-mobile-table">{{ $tableLabel }}</span>
                </header>
                <div class="scan-head">
                    <div>
                        <p class="scan-kicker">Temukan menu favoritmu dengan mudah! ❧</p>
                        <h1 class="scan-title">AI Image Search <em>AI</em></h1>
                        <p class="scan-subtitle">Ambil foto makanan dengan kamera, lalu temukan menu yang paling mirip.
                        </p>
                    </div><x-customer.language-selector />
                </div>
                <section class="scanner">
                    <div class="scanner-top"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h4l1.5-2h5L16 7h4v12H4z" />
                            <circle cx="12" cy="13" r="3" />
                        </svg>Ambil Foto</div>
                    <div class="camera-stage">
                        <div class="camera-empty" x-show="!cameraOpen"><svg width="58" height="58" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5">
                                <path d="M4 7h4l1.5-2h5L16 7h4v12H4z" />
                                <circle cx="12" cy="13" r="3" />
                            </svg>
                            <h2>Arahkan kamera ke makanan</h2>
                            <p>Pastikan makanan terlihat jelas dan cukup cahaya.</p><button class="camera-button" type="button" @click="startCamera()">Buka
                                Kamera</button>
                        </div><video class="camera-video" x-ref="video" x-show="cameraOpen" autoplay playsinline muted></video><canvas x-ref="canvas"
                            hidden></canvas>
                        <div class="camera-actions" x-show="cameraOpen"><button class="cancel" type="button" @click="stopCamera()">Tutup</button><button
                                class="capture" type="button" @click="capture()">Scan Foto</button></div>
                    </div>
                    <p class="scan-error" x-cloak x-show="error" x-text="error"></p>
                </section>
                <section class="result-section" x-cloak x-show="result" x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    <h2 class="result-heading"><span>â§</span> Hasil Pencarian</h2>
                    <div class="result-card">
                        <div class="result-image"><img :src="result?.image_url" alt="Foto hasil scan"><span class="best">Best Match</span></div>
                        <div class="result-copy">
                            <h2 x-text="result?.menu.name"></h2>
                            <p x-text="result?.menu.description"></p><strong class="confidence"
                                x-text="Math.round((result?.confidence||0)*100)+'%'"></strong>
                            <div><span class="confidence-label">Tingkat Kecocokan</span><span class="confidence-bar"><i
                                        :style="`width:${Math.round((result?.confidence||0)*100)}%`"></i></span></div>
                            <div class="result-foot"><strong class="result-price"
                                    x-text="result?'Rp '+new Intl.NumberFormat('id-ID').format(result.menu.price):''"></strong><a class="result-link"
                                    href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table]) }}">Lihat
                                    Menu â€º</a></div>
                        </div>
                    </div>
                </section>
                <section class="similar" x-cloak x-show="result?.similar_menus?.length">
                    <h2>Menu Mirip Lainnya</h2>
                    <div class="similar-grid"><template x-for="item in result?.similar_menus||[]" :key="item.id">
                            <article class="similar-card">
                                <div class="similar-image"><template x-if="item.image"><img :src="item.image" :alt="item.name"></template><template
                                        x-if="!item.image">
                                        <div class="similar-placeholder" x-text="item.name.substring(0,1)"></div>
                                    </template></div>
                                <div class="similar-body">
                                    <h3 x-text="item.name"></h3><strong x-text="'Rp '+new Intl.NumberFormat('id-ID').format(item.price)"></strong>
                                </div>
                            </article>
                        </template></div>
                </section>
                <p class="scan-note">â“˜ Hasil AI mungkin tidak 100% akurat. Sesuaikan dengan menu yang tersedia.</p>
            </main>
        </div>
        <div class="scan-loading" x-cloak x-show="scanning" x-transition>
            <div class="scan-loading-card">
                <div class="scan-spinner"></div><strong>Menganalisis Foto...</strong>
                <p class="mt-1 text-xs text-text-muted">Mencocokkan dengan menu kami</p>
            </div>
        </div>
    </div>
    <script>
        function scanner() {
            return {
                stream: null,
                cameraOpen: false,
                scanning: false,
                result: null,
                error: '',
                async startCamera() {
                    this.error = '';
                    if (!navigator.mediaDevices?.getUserMedia) {
                        this.error =
                            'Kamera tidak didukung. Gunakan perangkat dengan browser modern dan koneksi HTTPS.';
                        return
                    }
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: {
                                    ideal: 'environment'
                                }
                            },
                            audio: false
                        });
                        this.cameraOpen = true;
                        await this.$nextTick();
                        this.$refs.video.srcObject = this.stream;
                        await this.$refs.video.play()
                    } catch (e) {
                        this.error = 'Kamera tidak dapat dibuka. Berikan izin kamera pada browser lalu coba lagi.'
                    }
                },
                stopCamera() {
                    this.stream?.getTracks().forEach(track => track.stop());
                    this.stream = null;
                    this.cameraOpen = false;
                    if (this.$refs.video) this.$refs.video.srcObject = null
                },
                async capture() {
                    let video = this.$refs.video,
                        canvas = this.$refs.canvas;
                    if (!video.videoWidth) {
                        this.error = 'Kamera belum siap. Tunggu sebentar lalu scan kembali.';
                        return
                    }
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    this.scanning = true;
                    this.error = '';
                    try {
                        let blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', .88));
                        let form = new FormData();
                        form.append('image', blob, 'camera-scan.jpg');
                        let response = await fetch(
                            '{{ route('customer.menu.identify', ['branch_code' => $branch_code]) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: form
                            });
                        let data = await response.json();
                        if (!response.ok || !data.success) throw new Error(data.message ||
                            'Menu tidak berhasil dikenali.');
                        this.result = data;
                        this.stopCamera();
                        await this.$nextTick();
                        document.querySelector('.result-section')?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        })
                    } catch (e) {
                        this.error = e.message || 'Layanan AI sedang tidak tersedia.'
                    } finally {
                        this.scanning = false
                    }
                }
            }
        }
    </script>
</x-customer-layout>
