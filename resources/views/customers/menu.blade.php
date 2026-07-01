<x-customer-layout :branch="$branch" :table="$table">
    @php
        $cartItems = session('cart', []);
        $cartTotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        $tableLabel = preg_match('/^[A-Za-z]/', (string) $table) ? $table : 'A' . str_pad($table, 2, '0', STR_PAD_LEFT);
    @endphp
    <style>
        body {
            padding-bottom: 0 !important;
            background: #fff6f4 !important
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

        .menu-page {
            --pink: #fa4d77;
            --ink: #281c1b;
            --muted: #786f6d;
            min-height: 100svh;
            padding: 18px;
            background: radial-gradient(circle at 50% 0, #f7dfdd, #fff9f7 48%, #fffdf9)
        }

        .menu-shell {
            display: grid;
            grid-template-columns: 238px minmax(0, 1fr);
            position: relative;
            width: min(100%, 1400px);
            height: calc(100svh - 36px);
            min-height: 0;
            margin: auto;
            overflow: hidden;
            border-radius: 25px;
            background: #fff;
            box-shadow: 0 10px 35px #82494924
        }

        .menu-side {
            display: flex;
            flex-direction: column;
            position: relative;
            height: 100%;
            overflow-y: auto;
            padding: 30px 22px 28px;
            border-right: 1px solid #f4e5e0;
            background: linear-gradient(160deg, #fffaf7, #fff5f0)
        }

        .dots {
            display: grid;
            grid-template-columns: repeat(4, 5px);
            gap: 12px;
            position: absolute;
            top: 26px;
            left: 26px
        }

        .dots i {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #f17996
        }

        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 68px 0 35px
        }

        .brand img {
            width: 78px;
            height: 78px
        }

        .brand strong,
        .mobile-brand strong {
            margin-top: -11px;
            color: #e95578;
            font: italic 800 28px/1 var(--font-brand);
            letter-spacing: -.055em
        }

        .brand small {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 9px;
            color: #ad8a6d;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .48em
        }

        .brand small:before,
        .brand small:after {
            content: '';
            width: 30px;
            height: 1px;
            background: #c8ab90
        }

        .table-card {
            display: grid;
            grid-template-columns: 48px 1fr;
            align-items: center;
            padding: 10px 14px;
            border-radius: 15px;
            background: #ffffffcc;
            box-shadow: 0 5px 20px #ae6f7012
        }

        .table-icon {
            display: grid;
            place-items: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #fff0ef;
            color: var(--pink)
        }

        .table-card small {
            display: block;
            color: #706865;
            font-size: 10px
        }

        .table-card strong {
            color: var(--pink);
            font: 700 23px/1.1 var(--font-heading)
        }

        .side-nav {
            display: grid;
            gap: 7px;
            margin-top: 22px
        }

        .side-nav a {
            display: flex;
            align-items: center;
            gap: 13px;
            min-height: 46px;
            padding: 0 15px;
            border-radius: 9px;
            color: #4c403e;
            font-size: 12px;
            font-weight: 600
        }

        .side-nav a:hover {
            color: var(--pink);
            background: #fff0f0
        }

        .side-nav .active {
            color: #fff;
            background: linear-gradient(100deg, #f54772, #fc708d);
            box-shadow: 0 8px 18px #f24c722e
        }

        .badge {
            display: grid;
            place-items: center;
            min-width: 19px;
            height: 19px;
            margin-left: auto;
            padding: 0 5px;
            border-radius: 99px;
            color: #fff;
            background: var(--pink);
            font-size: 9px
        }

        .active .badge {
            color: var(--pink);
            background: #fff
        }

        .help {
            margin-top: auto;
            padding: 18px 15px;
            border-radius: 15px;
            background: linear-gradient(120deg, #fff0ee, #fde2e0);
            color: #706765;
            font-size: 10px;
            line-height: 1.55;
            text-align: center
        }

        .help svg {
            margin: 0 auto 7px;
            color: var(--pink)
        }

        .menu-main {
            position: relative;
            min-width: 0;
            height: 100%;
            overflow-y: auto;
            padding: 39px 31px 94px
        }

        .mobile-head {
            display: none
        }

        .intro {
            display: flex;
            justify-content: space-between;
            gap: 24px
        }

        .kicker {
            color: var(--pink);
            font-size: 13px
        }

        .title {
            margin-top: 6px;
            color: var(--ink);
            font: 800 clamp(25px, 2.3vw, 32px)/1.15 var(--font-heading);
            letter-spacing: -.03em
        }

        .subtitle {
            margin-top: 7px;
            color: var(--muted);
            font-size: 13px
        }

        .menu-page button,
        .menu-page a,
        .category,
        .card,
        .add,
        .image-search,
        .cart-link {
            transition-property: color, background-color, border-color, box-shadow, transform, opacity;
            transition-duration: .25s;
            transition-timing-function: ease
        }

        .search-row {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) auto;
            gap: 16px;
            margin-top: 20px
        }

        .search {
            position: relative
        }

        .search svg {
            position: absolute;
            top: 50%;
            left: 17px;
            color: #a9a5a3;
            transform: translateY(-50%)
        }

        .search input {
            width: 100%;
            height: 49px;
            padding: 0 18px 0 48px;
            border: 1px solid #e4dfdc;
            border-radius: 16px;
            background: #fff;
            font-size: 13px
        }

        .image-search {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-width: 183px;
            height: 49px;
            padding: 0 17px;
            border-radius: 11px;
            color: #fff;
            background: linear-gradient(100deg, #f74771, #fa567b);
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 8px 18px #f24d7324
        }

        .categories {
            display: flex;
            gap: 12px;
            margin-top: 19px;
            padding-bottom: 4px;
            overflow-x: auto;
            scrollbar-width: none
        }

        .categories::-webkit-scrollbar {
            display: none
        }

        .category {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-width: 116px;
            height: 48px;
            padding: 0 17px;
            flex: 1 0 auto;
            border: 1px solid #eee6e3;
            border-radius: 99px;
            background: #fff;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap
        }

        .category span {
            font-size: 21px
        }

        .category.active {
            color: var(--pink);
            border-color: #ff7898;
            background: #fff7f7
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 22px 0 13px
        }

        .section-head h2 {
            font: 700 15px var(--font-heading)
        }

        .section-head button {
            color: var(--pink);
            font-size: 10px;
            font-weight: 600
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px
        }

        .card {
            position: relative;
            min-width: 0;
            overflow: hidden;
            border: 1px solid #f0e8e5;
            border-radius: 13px;
            background: #fff;
            box-shadow: 0 5px 16px #78524f14;
            cursor: pointer
        }

        .card:hover {
            border-color: #f6a9b9;
            transform: translateY(-2px);
            box-shadow: 0 9px 21px #78524f1f
        }

        .card-image {
            position: relative;
            height: 130px;
            overflow: hidden;
            background: linear-gradient(135deg, #fff3ef, #f6e7e1)
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .placeholder {
            display: grid;
            place-items: center;
            width: 100%;
            height: 100%;
            color: #df8ba0;
            font: 800 34px var(--font-heading)
        }

        .card-tag {
            position: absolute;
            top: 8px;
            left: 8px;
            max-width: calc(100% - 16px);
            overflow: hidden;
            padding: 4px 8px;
            border-radius: 99px;
            color: #fff;
            background: #f74e77e6;
            font-size: 8px;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .card-body {
            display: flex;
            flex-direction: column;
            min-height: 116px;
            padding: 10px
        }

        .card h3 {
            overflow: hidden;
            font: 700 12px var(--font-heading);
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .card p {
            display: -webkit-box;
            min-height: 31px;
            margin-top: 5px;
            overflow: hidden;
            color: var(--muted);
            font-size: 9px;
            line-height: 1.55;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2
        }

        .card-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 5px;
            margin-top: auto;
            padding-top: 8px
        }

        .price {
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap
        }

        .add {
            display: grid;
            place-items: center;
            width: 27px;
            height: 27px;
            flex: none;
            border: 1.5px solid var(--pink);
            border-radius: 7px;
            color: var(--pink);
            background: #fff;
            font-size: 19px;
            line-height: 0;
            padding: 0 0 2px
        }

        .add:hover {
            color: #fff;
            background: var(--pink)
        }

        .empty {
            grid-column: 1/-1;
            padding: 60px 20px;
            color: var(--muted);
            text-align: center
        }

        .cartbar {
            position: absolute;
            right: 31px;
            bottom: 16px;
            left: 31px;
            z-index: 15;
            display: grid;
            grid-template-columns: 1fr auto auto;
            align-items: center;
            gap: 25px;
            min-height: 68px;
            padding: 9px 17px;
            border: 1px solid #f2d8d8;
            border-radius: 20px;
            background: #fffffff5;
            box-shadow: 0 9px 26px #71404026;
            backdrop-filter: blur(12px)
        }

        .cart-info {
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 0
        }

        .cart-icon {
            position: relative;
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            flex: none;
            border-radius: 50%;
            color: #fff;
            background: var(--pink)
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -3px;
            display: grid;
            place-items: center;
            min-width: 17px;
            height: 17px;
            padding: 0 4px;
            border: 2px solid #fff;
            border-radius: 99px;
            background: #ee3e69;
            font-size: 8px
        }

        .cart-copy strong {
            display: block;
            font-size: 13px
        }

        .cart-copy small {
            display: block;
            max-width: 360px;
            overflow: hidden;
            color: var(--muted);
            font-size: 9px;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .cart-total {
            color: var(--pink);
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap
        }

        .cart-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-width: 164px;
            height: 44px;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(100deg, #f64974, #fa5d7f);
            font-size: 12px;
            font-weight: 700
        }

        .toast {
            position: fixed;
            bottom: 94px;
            left: 50%;
            z-index: 70;
            display: flex;
            align-items: center;
            gap: 9px;
            max-width: calc(100% - 30px);
            padding: 11px 17px;
            border-radius: 13px;
            color: #fff;
            background: #68433f;
            box-shadow: 0 10px 25px #37201e33;
            font-size: 12px;
            font-weight: 600;
            transform: translateX(-50%)
        }

        .loading,
        .modal-wrap {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: grid;
            place-items: center;
            padding: 20px;
            background: #2d1c1b61;
            backdrop-filter: blur(4px)
        }

        .loading-card {
            width: min(100%, 320px);
            padding: 25px;
            border-radius: 20px;
            background: #fff;
            text-align: center
        }

        .spinner {
            width: 45px;
            height: 45px;
            margin: 0 auto 13px;
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

        .modal-wrap {
            z-index: 90;
            padding: 20px
        }

        .modal {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 48%) minmax(0, 52%);
            width: min(100%, 1050px);
            max-height: 92svh;
            overflow-y: auto;
            overflow-x: hidden;
            border: 1px solid #f0e2de;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 24px 70px #2a1b1a40
        }

        .detail-media {
            position: relative;
            min-height: 610px;
            padding: 18px;
            background: linear-gradient(145deg, #fff4f1, #fde2df)
        }

        .detail-image {
            width: 100%;
            height: 100%;
            min-height: 574px;
            overflow: hidden;
            border-radius: 18px;
            background: #f8d9d5
        }

        .detail-image img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .detail-image .placeholder {
            font-size: 72px
        }

        .detail-category {
            position: absolute;
            top: 31px;
            left: 31px;
            padding: 6px 12px;
            border-radius: 99px;
            color: #fff;
            background: var(--pink);
            font-size: 10px;
            font-weight: 700;
            box-shadow: 0 5px 14px #e8547630
        }

        .modal-close {
            position: absolute;
            top: 18px;
            right: 18px;
            z-index: 5;
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border: 1px solid #eadfdb;
            border-radius: 50%;
            color: #5b4d4a;
            background: #fff;
            box-shadow: 0 5px 14px #5c403b12;
            transition: .25s ease
        }

        .modal-close:hover {
            color: #fff;
            border-color: var(--pink);
            background: var(--pink);
            transform: rotate(90deg)
        }

        .detail-pane {
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow: visible;
            padding: 58px 42px 30px
        }

        .detail-pane h2 {
            padding-right: 38px;
            color: var(--ink);
            font: 800 clamp(25px, 2.5vw, 34px)/1.18 var(--font-heading);
            letter-spacing: -.035em
        }

        .detail-description {
            margin-top: 13px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7
        }

        .detail-price {
            display: block;
            margin-top: 16px;
            color: var(--pink);
            font: 800 25px var(--font-heading)
        }

        .detail-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 21px;
            padding: 13px 15px;
            border-radius: 14px;
            background: linear-gradient(100deg, #fff3f2, #fff8f6)
        }

        .detail-meta-icon {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            flex: none;
            border-radius: 50%;
            color: var(--pink);
            background: #fff
        }

        .detail-meta small {
            display: block;
            color: #8a807e;
            font-size: 9px
        }

        .detail-meta strong {
            display: block;
            margin-top: 2px;
            font-size: 12px
        }

        .detail-field {
            margin-top: 22px
        }

        .detail-field label,
        .detail-quantity-label {
            display: block;
            color: #3b302f;
            font-size: 12px;
            font-weight: 700
        }

        .detail-field label span {
            color: #968d8b;
            font-weight: 500
        }

        .detail-field textarea {
            width: 100%;
            height: 92px;
            margin-top: 9px;
            padding: 13px 15px;
            border: 1px solid #e5d9d6;
            border-radius: 13px;
            background: #fff;
            font-size: 11px;
            line-height: 1.55;
            resize: none;
            transition: border-color .2s ease, box-shadow .2s ease
        }

        .detail-field textarea:focus {
            border-color: #f28da5;
            box-shadow: 0 0 0 3px #f9c7d233
        }

        .detail-quantity {
            margin-top: 20px
        }

        .qty {
            display: grid;
            grid-template-columns: 42px 50px 42px;
            width: max-content;
            margin-top: 9px;
            overflow: hidden;
            border: 1px solid #eadbd7;
            border-radius: 11px;
            background: #fff
        }

        .qty button,
        .qty span {
            display: grid;
            place-items: center;
            width: auto;
            height: 42px;
            font-weight: 700
        }

        .qty button {
            color: var(--pink);
            font-size: 19px
        }

        .qty span {
            border-inline: 1px solid #eee2df;
            font-size: 12px
        }

        .detail-actions {
            display: flex;
            margin-top: auto;
            padding-top: 28px
        }

        .modal-add {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            min-height: 52px;
            border: 1.5px solid var(--pink);
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(100deg, #f64270, #fa5b7e);
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 9px 20px #f24c7226;
            transition: .25s ease
        }

        .modal-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 13px 25px #f24c7238
        }

        @media(max-width:899px) {
            .modal {
                grid-template-columns: minmax(0, 46%) minmax(0, 54%);
                max-height: 94svh
            }

            .detail-media {
                min-height: 520px;
                padding: 13px
            }

            .detail-image {
                min-height: 494px
            }

            .detail-pane {
                padding: 46px 26px 24px
            }

            .detail-pane h2 {
                font-size: 24px
            }

            .detail-description {
                font-size: 10px
            }

            .detail-price {
                font-size: 21px
            }

            .detail-meta {
                margin-top: 15px
            }

            .detail-field {
                margin-top: 16px
            }

            .detail-field textarea {
                height: 76px
            }

            .detail-actions {
                padding-top: 20px
            }
        }

        @media(max-width:767px) {
            .modal-wrap {
                align-items: end;
                padding: 0
            }

            .modal {
                display: block;
                width: 100%;
                max-height: 96svh;
                overflow-y: auto;
                border-width: 1px 0 0;
                border-radius: 24px 24px 0 0
            }

            .detail-media {
                min-height: 310px;
                padding: 10px
            }

            .detail-image {
                height: 290px;
                min-height: 290px
            }

            .detail-category {
                top: 20px;
                left: 20px
            }

            .modal-close {
                top: 14px;
                right: 14px
            }

            .detail-pane {
                overflow: visible;
                padding: 22px 17px 24px
            }

            .detail-pane h2 {
                padding-right: 46px;
                font-size: 24px
            }

            .detail-description {
                font-size: 11px
            }

            .detail-price {
                font-size: 22px
            }

            .detail-meta {
                margin-top: 17px
            }

            .detail-field textarea {
                height: 78px
            }

            .detail-actions {
                position: sticky;
                bottom: 0;
                margin-inline: -17px;
                margin-bottom: -24px;
                padding: 12px 17px 14px;
                background: #fffffff2;
                backdrop-filter: blur(10px)
            }
        }

        @media(max-width:399px) {
            .detail-media {
                min-height: 270px
            }

            .detail-image {
                height: 250px;
                min-height: 250px
            }

            .detail-pane {
                padding-inline: 14px
            }

            .detail-pane h2 {
                font-size: 21px
            }

            .detail-actions {
                margin-inline: -14px;
                padding-inline: 14px
            }
        }

        @media(max-width:1099px) and (min-width:900px) {
            .menu-shell {
                grid-template-columns: 210px minmax(0, 1fr)
            }

            .menu-side {
                padding-inline: 17px
            }

            .menu-main {
                padding-inline: 24px
            }

            .menu-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr))
            }

            .cartbar {
                right: 24px;
                left: 24px
            }
        }

        @media(max-width:899px) {
            .menu-page {
                padding: 0
            }

            .menu-shell {
                display: block;
                height: auto;
                min-height: 100svh;
                overflow: hidden;
                border-radius: 0;
                box-shadow: none
            }

            .menu-side,
            .intro {
                display: none
            }

            .menu-main {
                height: auto;
                min-height: 100svh;
                overflow: visible;
                padding: 14px 16px 96px
            }

            .mobile-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 17px
            }

            .mobile-brand {
                display: flex;
                align-items: center
            }

            .mobile-brand img {
                width: 42px;
                height: 42px
            }

            .mobile-brand div {
                margin-left: -4px
            }

            .mobile-brand strong {
                display: block;
                margin: 0;
                font-size: 19px
            }

            .mobile-brand small {
                display: block;
                margin-top: 4px;
                color: #a68165;
                font-size: 6px;
                font-weight: 700;
                letter-spacing: .42em;
                text-align: center
            }

            .mobile-table {
                padding: 8px 11px;
                border: 1px solid #f0dfdc;
                border-radius: 10px;
                text-align: center
            }

            .mobile-table small {
                display: block;
                color: #6e6563;
                font-size: 7px
            }

            .mobile-table strong {
                color: var(--pink);
                font: 700 14px var(--font-heading)
            }

            .search-row {
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 8px;
                margin-top: 0
            }

            .search input {
                height: 42px;
                padding-left: 36px;
                border-radius: 10px;
                font-size: 10px
            }

            .search svg {
                left: 12px;
                width: 15px
            }

            .image-search {
                min-width: 87px;
                height: 42px;
                padding: 0 10px;
                border-radius: 9px;
                font-size: 9px
            }

            .image-search svg {
                width: 15px
            }

            .categories {
                gap: 7px;
                margin: 15px -16px 0;
                padding-inline: 16px
            }

            .category {
                min-width: auto;
                height: 34px;
                gap: 6px;
                padding: 0 11px;
                font-size: 8px
            }

            .category span {
                font-size: 14px
            }

            .section-head {
                margin: 17px 0 11px
            }

            .section-head h2 {
                font-size: 12px
            }

            .section-head button {
                font-size: 8px
            }

            .menu-grid {
                grid-template-columns: 1fr;
                gap: 10px
            }

            .card {
                display: grid;
                grid-template-columns: 42% minmax(0, 1fr);
                min-height: 121px
            }

            .card-image {
                height: 100%;
                min-height: 121px
            }

            .card-body {
                min-height: 121px
            }

            .card h3 {
                font-size: 11px
            }

            .card p {
                min-height: 29px;
                font-size: 8px
            }

            .price {
                font-size: 10px
            }

            .cartbar {
                position: fixed;
                right: 10px;
                bottom: 9px;
                left: 10px;
                grid-template-columns: 1fr auto;
                min-height: 61px;
                gap: 9px;
                padding: 8px 11px;
                border-radius: 16px
            }

            .cart-icon {
                width: 38px;
                height: 38px
            }

            .cart-copy strong {
                font-size: 11px
            }

            .cart-copy small {
                display: none
            }

            .cart-total {
                position: absolute;
                left: 62px;
                bottom: 9px;
                font-size: 10px
            }

            .cart-link {
                min-width: 92px;
                height: 40px;
                padding: 0 13px;
                font-size: 10px
            }

            .cart-link span {
                display: none
            }
        }

        @media(min-width:360px) and (max-width:899px) {
            .menu-main {
                padding-inline: 13px
            }

            .categories {
                margin-inline: -13px;
                padding-inline: 13px
            }

            .menu-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px
            }

            .card {
                display: block;
                min-height: 0
            }

            .card-image {
                height: 112px;
                min-height: 0
            }

            .card-body {
                min-height: 111px;
                padding: 9px
            }

            .card h3 {
                font-size: 10px
            }

            .card p {
                font-size: 7px
            }

            .price {
                font-size: 9px
            }
        }

        @media(min-width:390px) and (max-width:899px) {
            .menu-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 8px
            }

            .card-image {
                height: 105px
            }

            .card-body {
                min-height: 105px;
                padding: 7px
            }

            .card h3 {
                font-size: 8px
            }

            .card p {
                min-height: 24px;
                margin-top: 3px;
                font-size: 6px
            }

            .price {
                font-size: 8px
            }

            .add {
                width: 21px;
                height: 21px;
                font-size: 15px
            }

            .card-tag {
                top: 5px;
                left: 5px;
                padding: 3px 5px;
                font-size: 6px
            }
        }

        @media(min-width:768px) and (max-width:899px) {
            .menu-main {
                padding-inline: 28px
            }

            .menu-grid {
                gap: 14px
            }

            .card-image {
                height: 150px
            }

            .card-body {
                min-height: 125px;
                padding: 11px
            }

            .card h3 {
                font-size: 12px
            }

            .card p {
                min-height: 33px;
                font-size: 9px
            }

            .price {
                font-size: 11px
            }
        }

        @media(max-width:767px) {
            body>nav {
                display: flex !important
            }

            .menu-main {
                padding-bottom: 165px
            }

            .cartbar {
                bottom: 72px
            }

            .toast {
                bottom: 150px
            }
        }

        @media(max-width:359px) {
            .menu-main {
                padding-inline: 11px
            }

            .mobile-brand strong {
                font-size: 17px
            }

            .mobile-brand img {
                width: 38px;
                height: 38px
            }

            .image-search {
                min-width: 80px;
                padding-inline: 7px
            }
        }
    </style>
    <div class="menu-page" x-data="{
        activeCategory: 'all',
        search: '',
        showToast: false,
        toastMessage: '',
        isUploading: false,
        selectedMenu: null,
        showMenuModal: false,
        menuNote: '',
        menuQuantity: 1,
        cartCount: {{ $cartCount }},
        cartTotal: {{ $cartTotal }},
        openMenuModal(menu) {
            this.selectedMenu = menu;
            this.menuNote = '';
            this.menuQuantity = 1;
            this.showMenuModal = true
        },
        triggerToast(message) {
            this.toastMessage = message;
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3500)
        },
        addToCart(menuId, quantity = 1, note = '') {
            fetch('{{ route('customer.cart.add', ['branch_code' => $branch_code]) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ menu_id: menuId, quantity, note }) }).then(async r => {
                let d = await r.json();
                if (!r.ok || !d.success) throw new Error(d.message || 'Gagal menambahkan item.');
                this.cartCount = d.cart_count;
                this.cartTotal += Number(this.selectedMenu?.price || 0) * quantity;
                this.showMenuModal = false;
                this.triggerToast(d.message);
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: d.cart_count } }))
            }).catch(e => this.triggerToast(e.message || 'Terjadi kesalahan koneksi.'))
        },
        uploadImage(event) {
            let file = event.target.files[0];
            if (!file) return;
            this.isUploading = true;
            let form = new FormData();
            form.append('image', file);
            fetch('{{ route('customer.menu.identify', ['branch_code' => $branch_code]) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: form }).then(async r => {
                let d = await r.json();
                if (!r.ok || !d.success) throw new Error(d.message || 'Menu tidak dikenali.');
                this.search = d.menu_name;
                this.triggerToast(`Menu terdeteksi: ${d.menu_name}`)
            }).catch(e => this.triggerToast(e.message || 'Layanan AI sedang gangguan.')).finally(() => {
                this.isUploading = false;
                event.target.value = ''
            })
        }
    }">
        <div class="menu-shell">
            <aside class="menu-side">
                <div class="dots" aria-hidden="true">
                    @for ($i = 0; $i < 16; $i++)
                        <i></i>
                    @endfor
                </div>
                <div class="brand"><img src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt=""><strong>Bakso Cinta</strong><small>CIAMIS</small></div>
                <div class="table-card"><span class="table-icon"><svg width="26" height="26" viewBox="0 0 32 32" fill="none" stroke="currentColor"
                            stroke-width="1.8">
                            <circle cx="16" cy="7" r="3" />
                            <path d="M10 14v11M22 14v11M8 17h16v6H8zM5 18v9M27 18v9M9 27h4M19 27h4" />
                        </svg></span><span><small>Meja Anda</small><strong>{{ $tableLabel }}</strong></span></div>
                <nav class="side-nav" aria-label="Navigasi pelanggan"><a class="active"
                        href="{{ route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $table]) }}"><svg width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 17h16M6 17a6 6 0 0 1 12 0M12 8v3M3 20h18" />
                        </svg>Menu</a><a href="{{ route('customer.ai-scan', ['branch_code' => $branch_code]) }}"><svg width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h4l1.5-2h5L16 7h4v12H4z" />
                            <circle cx="12" cy="13" r="3" />
                        </svg>AI Scan</a><a href="{{ route('customer.cart', ['branch_code' => $branch_code]) }}"><svg width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 4h2l2.2 10.5h10.9L21 7H6M9 20h.01M18 20h.01" />
                        </svg>Keranjang <span class="badge" x-show="cartCount" x-text="cartCount"></span></a><a
                        href="{{ route('customer.payment', ['branch_code' => $branch_code]) }}"><svg width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3z" />
                        </svg>Pembayaran</a><a href="{{ route('customer.order.status', ['branch_code' => $branch_code]) }}"><svg width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M7 3h10v18H7zM10 8h4M10 12h4M10 16h3" />
                        </svg>Pesanan</a></nav>
                <div class="help"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 13v-2a8 8 0 0 1 16 0v2M4 13h3v6H5a1 1 0 0 1-1-1v-5ZM20 13h-3v6h2a1 1 0 0 0 1-1v-5Z" />
                    </svg><strong>Butuh bantuan?</strong><br>Hubungi staf kami jika ada kendala.</div>
            </aside>
            <section class="menu-main">
                <header class="mobile-head">
                    <div class="mobile-brand"><img src="{{ asset('svg/bakso_cinta_icon.svg') }}" alt="">
                        <div><strong>Bakso Cinta</strong><small>CIAMIS</small></div>
                    </div>
                    <div class="mobile-table"><small>Meja</small><strong>{{ $tableLabel }}</strong></div>
                </header>
                <div class="intro">
                    <div>
                        <p class="kicker">Selamat menikmati! ❧</p>
                        <h1 class="title">Pilih menu favoritmu</h1>
                        <p class="subtitle">Menu lezat, dibuat dengan cinta untuk Anda.</p>
                    </div><x-customer.language-selector />
                </div>
                <div class="search-row"><label class="search"><span class="sr-only">Cari menu</span><svg width="19" height="19" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m20 20-4-4" />
                        </svg><input type="search" x-model="search" placeholder="Cari menu favoritmu..."></label><button class="image-search" type="button"
                        @click="$refs.cameraInput.click()"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8">
                            <path d="M4 7h4l1.5-2h5L16 7h4v12H4z" />
                            <circle cx="12" cy="13" r="3" />
                        </svg><span>AI Scan</span></button><input x-ref="cameraInput" type="file" accept="image/*" capture="environment" class="hidden"
                        @change="uploadImage($event)"></div>
                <div class="categories"><button class="category" :class="activeCategory === 'all' && 'active'"
                        @click="activeCategory='all'"><span>♨</span>Semua</button>
                    @foreach ($categories as $category)
                        @php $icon=str_contains(strtolower($category->name),'coffee')?'☕':(str_contains(strtolower($category->name),'drink')?'🥤':(str_contains(strtolower($category->name),'dessert')?'🥐':'🍜')); @endphp
                        <button class="category" :class="activeCategory === '{{ $category->id }}' && 'active'"
                            @click="activeCategory='{{ $category->id }}'"><span>{{ $icon }}</span>{{ $category->name }}</button>
                    @endforeach
                </div>
                <div class="section-head">
                    <h2>Menu Pilihan</h2><button type="button" @click="activeCategory='all';search=''">Lihat semua ›</button>
                </div>
                <div class="menu-grid">
                    @forelse($menus as $menu)
                        @php
                            $menuImage = $menu->image_path ? (str_starts_with($menu->image_path, 'http') ? $menu->image_path : asset($menu->image_path)) : null;
                            $menuData = [
                                'id' => $menu->id,
                                'name' => $menu->name,
                                'description' => $menu->description,
                                'price' => (float) $menu->price,
                                'image' => $menuImage,
                                'category' => $menu->category->name,
                            ];
                            $searchText = mb_strtolower($menu->name . ' ' . $menu->description);
                        @endphp
                        <article class="card"
                            x-show="(activeCategory==='all'||activeCategory==='{{ $menu->category_id }}')&&(!search||{{ Js::from($searchText) }}.includes(search.toLowerCase()))"
                            @click="openMenuModal({{ Js::from($menuData) }})">
                            <div class="card-image">
                                @if ($menuImage)
                                <img src="{{ $menuImage }}" alt="{{ $menu->name }}" loading="lazy">@else<div class="placeholder">
                                        {{ mb_substr($menu->name, 0, 1) }}</div>
                                @endif
                                <span class="card-tag">{{ $menu->category->name }}</span>
                            </div>
                            <div class="card-body">
                                <h3>{{ $menu->name }}</h3>
                                <p>{{ $menu->description }}</p>
                                <div class="card-foot"><strong class="price">Rp {{ number_format($menu->price, 0, ',', '.') }}</strong><button
                                        class="add" type="button" aria-label="Tambah {{ $menu->name }}"
                                        @click.stop="openMenuModal({{ Js::from($menuData) }})">+</button></div>
                            </div>
                        </article>
                    @empty<div class="empty">Tidak ada menu yang tersedia saat ini.</div>
                    @endforelse
                </div>
                <div class="cartbar" x-show="cartCount>0" x-transition>
                    <div class="cart-info"><span class="cart-icon"><svg width="21" height="21" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8">
                                <path d="M3 4h2l2.2 10.5h10.9L21 7H6M9 20h.01M18 20h.01" />
                            </svg><b class="cart-count" x-text="cartCount"></b></span><span class="cart-copy"><strong><span x-text="cartCount"></span> Item
                                di Keranjang</strong><small>Pesanan Anda siap untuk diperiksa</small></span></div><strong class="cart-total"
                        x-text="'Rp '+new Intl.NumberFormat('id-ID').format(cartTotal)"></strong><a class="cart-link"
                        href="{{ route('customer.cart', ['branch_code' => $branch_code]) }}"><span>Lihat Keranjang</span><b>›</b></a>
                </div>
            </section>
        </div>
        <div class="toast" x-cloak x-show="showToast" x-transition><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="9" />
                <path d="m8 12 3 3 5-6" />
            </svg><span x-text="toastMessage"></span></div>
        <div class="loading" x-cloak x-show="isUploading" x-transition>
            <div class="loading-card">
                <div class="spinner"></div><strong>Menganalisis Foto...</strong>
                <p class="mt-1 text-xs text-text-muted">Mencocokkan dengan menu kami</p>
            </div>
        </div>
        <div class="modal-wrap" x-cloak x-show="showMenuModal" role="dialog" aria-modal="true" aria-label="Detail menu"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @keydown.escape.window="showMenuModal=false">
            <div class="modal" @click.outside="showMenuModal=false" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-5 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"><button
                    class="modal-close" type="button" @click="showMenuModal=false" aria-label="Tutup detail menu"><svg width="18" height="18"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 6l12 12M18 6 6 18" />
                    </svg></button>
                <div class="detail-media">
                    <div class="detail-image"><template x-if="selectedMenu?.image"><img :src="selectedMenu.image"
                                :alt="selectedMenu.name"></template><template x-if="selectedMenu&&!selectedMenu.image">
                            <div class="placeholder" x-text="selectedMenu.name.substring(0,1)"></div>
                        </template></div><span class="detail-category" x-text="selectedMenu?.category"></span>
                </div>
                <div class="detail-pane">
                    <h2 x-text="selectedMenu?.name"></h2>
                    <p class="detail-description" x-text="selectedMenu?.description"></p><strong class="detail-price"
                        x-text="selectedMenu?'Rp '+new Intl.NumberFormat('id-ID').format(selectedMenu.price):''"></strong>
                    <div class="detail-meta"><span class="detail-meta-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8">
                                <path d="M4 17h16M6 17a6 6 0 0 1 12 0M12 8v3M3 20h18" stroke-linecap="round" />
                            </svg></span><span><small>Kategori</small><strong x-text="selectedMenu?.category"></strong></span></div>
                    <div class="detail-field"><label for="menu-note">Catatan <span>(Opsional)</span></label>
                        <textarea id="menu-note" x-model="menuNote" maxlength="255" rows="3" placeholder="Contoh: tidak pedas, tanpa es batu..."></textarea>
                    </div>
                    <div class="detail-quantity"><span class="detail-quantity-label">Jumlah</span>
                        <div class="qty"><button type="button" @click="menuQuantity=Math.max(1,menuQuantity-1)"
                                aria-label="Kurangi jumlah">−</button><span x-text="menuQuantity"></span><button type="button" @click="menuQuantity++"
                                aria-label="Tambah jumlah">+</button></div>
                    </div>
                    <div class="detail-actions"><button class="modal-add" type="button" @click="addToCart(selectedMenu.id,menuQuantity,menuNote)"><svg
                                width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 4h2l2.2 10.5h10.9L21 7H6M9 20h.01M18 20h.01" stroke-linecap="round" />
                            </svg>Tambah ke Keranjang</button></div>
                </div>
            </div>
        </div>
    </div>
</x-customer-layout>
