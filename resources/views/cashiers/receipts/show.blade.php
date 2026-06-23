<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Pembayaran — {{ $receipt->receipt_number }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .receipt-paper {
                box-shadow: none !important;
                border: none !important;
                background-color: #ffffff !important;
                width: 100% !important;
                max-width: 80mm !important; /* Standard thermal printer width */
                margin: 0 auto !important;
                padding: 10px !important;
            }
        }
    </style>
</head>
<body class="bg-surface-alt font-mono min-h-screen py-10 px-4 flex flex-col items-center justify-start text-text antialiased">

    <!-- Top Action Buttons (Hidden on Print) -->
    <div class="no-print w-full max-w-sm flex items-center justify-between mb-6">
        <a href="{{ route('cashier.orders.show', $receipt->payment->order_id) }}" 
           class="inline-flex items-center gap-2 bg-card border border-border hover:bg-surface text-text font-bold px-4 py-2 rounded-xl t-size2 transition cursor-pointer shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            {{ __('Kembali') }}
        </a>
        
        <button onclick="window.print()" 
                class="inline-flex items-center gap-2 bg-primary hover:bg-primary-strong text-white font-extrabold px-6 py-2 rounded-xl t-size2 transition cursor-pointer shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            {{ __('Cetak Struk') }}
        </button>
    </div>

    <!-- Paper Receipt Container -->
    <div class="receipt-paper w-full max-w-sm bg-card border border-border p-6 shadow-md rounded-2xl flex flex-col space-y-4">
        
        <!-- Header -->
        <div class="text-center space-y-1">
            <h1 class="font-extrabold t-size6 font-heading tracking-wider uppercase text-text">KRAVE SCAN</h1>
            <h2 class="font-bold t-size3 text-text">{{ $receipt->payment->order->branch->name ?? 'Cabang Krave Scan' }}</h2>
            <p class="t-size1 text-text-muted max-w-xs mx-auto">
                {{ $receipt->payment->order->branch->address ?? '-' }}
                <br>
                {{ __('Telp') }}: {{ $receipt->payment->order->branch->phone ?? '-' }}
            </p>
        </div>

        <hr class="border-dashed border-border">

        <!-- Receipt Details -->
        <div class="space-y-1 t-size2">
            <div class="flex justify-between">
                <span>{{ __('No. Struk') }}:</span>
                <span class="font-bold text-text">{{ $receipt->receipt_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>{{ __('Waktu') }}:</span>
                <span>{{ $receipt->printed_at ? $receipt->printed_at->format('d/m/Y H:i:s') : '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span>{{ __('Meja') }}:</span>
                <span class="font-bold text-text">{{ $receipt->payment->order->table_number }}</span>
            </div>
        </div>

        <hr class="border-dashed border-border">

        <!-- Items Table -->
        <div class="space-y-2">
            @foreach($receipt->payment->order->orderItems as $item)
                <div class="space-y-0.5 t-size2 text-text">
                    <div class="flex justify-between font-bold">
                        <span>{{ $item->menu->name }}</span>
                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-text-muted t-size1 pl-2">
                        {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>

        <hr class="border-dashed border-border">

        <!-- Totals & Payment Status -->
        <div class="space-y-1.5 t-size2 text-text">
            <div class="flex justify-between font-extrabold t-size3 text-accent">
                <span>{{ __('TOTAL') }}</span>
                <span>Rp {{ number_format($receipt->payment->amount, 0, ',', '.') }}</span>
            </div>
            
            <div class="flex justify-between">
                <span>{{ __('Metode') }}:</span>
                <span class="font-bold uppercase">{{ $receipt->payment->method }}</span>
            </div>

            @if($receipt->payment->method === 'cash')
                <div class="flex justify-between">
                    <span>{{ __('Uang Bayar') }}:</span>
                    <span>Rp {{ number_format($cashReceived, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-success">
                    <span>{{ __('Kembalian') }}:</span>
                    <span>Rp {{ number_format($change, 0, ',', '.') }}</span>
                </div>
            @else
                <div class="flex justify-between font-bold text-success">
                    <span>{{ __('Status QRIS') }}:</span>
                    <span>{{ __('SUKSES (Instan)') }}</span>
                </div>
            @endif
        </div>

        <hr class="border-dashed border-border">

        <!-- Footer Note -->
        <div class="text-center space-y-1">
            <p class="t-size2 font-semibold text-text">{{ __('Terima Kasih!') }}</p>
            <p class="t-size1 text-text-muted">{{ __('Silakan berkunjung kembali.') }}</p>
        </div>

    </div>

    <!-- Auto Print Script -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Auto invoke printer dialog
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
