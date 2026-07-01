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
                max-width: 80mm !important;
                /* Standard thermal printer width */
                margin: 0 auto !important;
                padding: 10px !important;
            }
        }
    </style>
</head>

<body class="bg-surface-alt font-mono min-h-screen py-10 px-4 flex flex-col items-center justify-start text-text antialiased">

    @php
        $order = $receipt->payment->order;
        $contact = $order->customer_contact;
        $whatsappNumber = '';
        $emailAddress = '';
        
        if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
            $emailAddress = $contact;
        } else {
            // Clean phone number format for WhatsApp link
            $cleaned = preg_replace('/[^0-9]/', '', $contact);
            if (str_starts_with($cleaned, '0')) {
                $whatsappNumber = '62' . substr($cleaned, 1);
            } else {
                $whatsappNumber = $cleaned;
            }
        }

        // Compose detailed receipt text for WhatsApp/Email
        $lineItems = '';
        foreach ($order->orderItems as $item) {
            $lineItems .= "- {$item->menu->name} ({$item->quantity}x): Rp " . number_format($item->subtotal, 0, ',', '.') . "\n";
        }

        $receiptText = "=== STRUK KRAVE SCAN ===\n"
                     . "No Struk: {$receipt->receipt_number}\n"
                     . "ID Pelanggan: CUST-{$order->id}\n"
                     . "ID Pesanan: #{$order->id}\n"
                     . "Nama Kasir: " . (Auth::user()->name ?? 'Kasir') . "\n"
                     . "Nama Pelanggan: {$order->customer_name}\n"
                     . "No Meja: Meja {$order->table_number}\n"
                     . "Tanggal Transaksi: " . ($receipt->payment->created_at ? $receipt->payment->created_at->format('d/m/Y H:i:s') : '-') . "\n"
                     . "-------------------------\n"
                     . "Pesanan:\n" . $lineItems
                     . "-------------------------\n"
                     . "Total Harga: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n"
                     . "Metode Pembayaran: " . strtoupper($receipt->payment->method) . "\n"
                     . "Total Bayar: Rp " . number_format($cashReceived, 0, ',', '.') . "\n"
                     . "Kembalian: Rp " . number_format($change, 0, ',', '.') . "\n"
                     . "No WA/Email: {$contact}\n"
                     . "=========================\n"
                     . "Terima kasih atas kunjungan Anda!";
        
        $encodedText = rawurlencode($receiptText);
    @endphp

    <!-- Top Action & Sharing Buttons (Hidden on Print) -->
    <div class="no-print w-full max-w-sm flex flex-col gap-4 mb-6">
        <div class="flex items-center justify-between">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                {{ __('Cetak Struk') }}
            </button>
        </div>

        <div class="bg-card border border-border p-4 rounded-2xl shadow-xs space-y-3">
            <span class="text-text-muted t-size2 font-bold uppercase tracking-wider block">{{ __('Kirim Struk Digital') }}</span>
            <div class="grid grid-cols-2 gap-2">
                <a href="https://api.whatsapp.com/send?phone={{ $whatsappNumber ?: $contact }}&text={{ $encodedText }}" target="_blank"
                    class="flex items-center justify-center gap-1.5 bg-success hover:bg-success-strong text-white font-bold py-2 px-3 rounded-xl t-size2 transition">
                    📱 WhatsApp
                </a>
                <a href="mailto:{{ $emailAddress ?: $contact }}?subject=Struk%20KraveScan%20{{ $receipt->receipt_number }}&body={{ $encodedText }}"
                    class="flex items-center justify-center gap-1.5 bg-info hover:bg-info-strong text-white font-bold py-2 px-3 rounded-xl t-size2 transition">
                    ✉️ Email
                </a>
            </div>
        </div>
    </div>

    <!-- Paper Receipt Container -->
    <div class="receipt-paper w-full max-w-sm bg-card border border-border p-6 shadow-md rounded-2xl flex flex-col space-y-4">

        <!-- Header -->
        <div class="text-center space-y-1">
            <h1 class="font-extrabold t-size6 font-heading tracking-wider uppercase text-text">KRAVE SCAN</h1>
            <h2 class="font-bold t-size3 text-text">{{ $order->branch->name ?? 'Cabang Krave Scan' }}</h2>
            <p class="t-size1 text-text-muted max-w-xs mx-auto">
                {{ $order->branch->address ?? '-' }}
                <br>
                {{ __('Telp') }}: {{ $order->branch->phone ?? '-' }}
            </p>
        </div>

        <hr class="border-dashed border-border">

        <!-- Receipt Details (Class Diagram Attributes Mapping) -->
        <div class="space-y-1 t-size2">
            <div class="flex justify-between">
                <span>{{ __('idStruk') }}:</span>
                <span class="font-bold text-text">{{ $receipt->receipt_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>{{ __('idPelanggan') }}:</span>
                <span class="font-bold text-text">CUST-{{ $order->id }}</span>
            </div>
            <div class="flex justify-between">
                <span>{{ __('idPesanan') }}:</span>
                <span class="font-bold text-text">#{{ $order->id }}</span>
            </div>
            <div class="flex justify-between">
                <span>{{ __('namaKasir') }}:</span>
                <span class="font-bold text-text">{{ Auth::user()->name ?? 'Kasir' }}</span>
            </div>
            <div class="flex justify-between">
                <span>{{ __('namaPelanggan') }}:</span>
                <span class="font-bold text-text">{{ $order->customer_name ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span>{{ __('noMeja') }}:</span>
                <span class="font-bold text-text">{{ $order->table_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>{{ __('tanggalTransaksi') }}:</span>
                <span>{{ $receipt->payment->created_at ? $receipt->payment->created_at->format('d/m/Y H:i:s') : '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span>{{ __('noWa/email') }}:</span>
                <span class="font-bold text-text truncate max-w-[180px]">{{ $order->customer_contact ?? '-' }}</span>
            </div>
        </div>

        <hr class="border-dashed border-border">

        <!-- Items Table (pesanan) -->
        <div class="space-y-2">
            <span class="text-text-muted t-size1 uppercase font-bold tracking-wider">{{ __('pesanan') }}:</span>
            @foreach ($order->orderItems as $item)
                <div class="space-y-0.5 t-size2 text-text">
                    <div class="flex justify-between font-bold">
                        <span>{{ $item->menu->name }}</span>
                        <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-text-muted t-size1 pl-2 flex justify-between">
                        <span>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                        @if ($item->note)
                            <span class="italic text-accent">"{{ $item->note }}"</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <hr class="border-dashed border-border">

        <!-- Totals & Payment Status (totalBayar, totalHarga, kembalian, metodePembayaran) -->
        <div class="space-y-1.5 t-size2 text-text">
            <div class="flex justify-between font-extrabold t-size3 text-accent border-b border-dashed border-border pb-1">
                <span>{{ __('totalHarga') }}</span>
                <span>Rp {{ number_format($receipt->payment->amount, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between">
                <span>{{ __('metodePembayaran') }}:</span>
                <span class="font-bold uppercase">{{ $receipt->payment->method }}</span>
            </div>

            @if ($receipt->payment->method === 'cash')
                <div class="flex justify-between">
                    <span>{{ __('totalBayar') }} (Cash Received):</span>
                    <span>Rp {{ number_format($cashReceived, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-success">
                    <span>{{ __('kembalian') }}:</span>
                    <span>Rp {{ number_format($change, 0, ',', '.') }}</span>
                </div>
            @else
                <div class="flex justify-between">
                    <span>{{ __('totalBayar') }} (QRIS):</span>
                    <span>Rp {{ number_format($receipt->payment->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-success">
                    <span>{{ __('kembalian') }}:</span>
                    <span>Rp 0</span>
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
