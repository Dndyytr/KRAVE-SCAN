@props(['status'])

@php
    $config = match($status) {
        'pending' => [
            'label' => 'Menunggu',
            'bg' => 'bg-warning-soft',
            'text' => 'text-yellow-800',
            'dot' => 'bg-warning',
            'animate' => true,
        ],
        'confirmed' => [
            'label' => 'Dikonfirmasi',
            'bg' => 'bg-info-soft',
            'text' => 'text-blue-800',
            'dot' => 'bg-info',
            'animate' => false,
        ],
        'in_process', 'processing' => [
            'label' => 'Diproses',
            'bg' => 'bg-primary-soft/40',
            'text' => 'text-accent',
            'dot' => 'bg-primary-strong',
            'animate' => true,
        ],
        'completed' => [
            'label' => 'Selesai',
            'bg' => 'bg-success-soft',
            'text' => 'text-green-800',
            'dot' => 'bg-success',
            'animate' => false,
        ],
        'cancelled' => [
            'label' => 'Dibatalkan',
            'bg' => 'bg-danger-soft',
            'text' => 'text-red-800',
            'dot' => 'bg-danger',
            'animate' => false,
        ],
        'paid', 'success' => [
            'label' => $status === 'success' ? 'Sukses' : 'Lunas',
            'bg' => 'bg-success-soft',
            'text' => 'text-green-800',
            'dot' => 'bg-success',
            'animate' => false,
        ],
        'failed' => [
            'label' => 'Gagal',
            'bg' => 'bg-danger-soft',
            'text' => 'text-red-800',
            'dot' => 'bg-danger',
            'animate' => false,
        ],
        'unpaid' => [
            'label' => 'Belum Bayar',
            'bg' => 'bg-danger-soft',
            'text' => 'text-red-800',
            'dot' => 'bg-danger',
            'animate' => false,
        ],
        'low_stock' => [
            'label' => 'Stok Rendah',
            'bg' => 'bg-warning-soft',
            'text' => 'text-yellow-800',
            'dot' => 'bg-warning',
            'animate' => true,
        ],
        'available' => [
            'label' => 'Tersedia',
            'bg' => 'bg-success-soft',
            'text' => 'text-green-800',
            'dot' => 'bg-success',
            'animate' => false,
        ],
        'unavailable' => [
            'label' => 'Tidak Tersedia',
            'bg' => 'bg-danger-soft',
            'text' => 'text-red-800',
            'dot' => 'bg-danger',
            'animate' => false,
        ],
        default => [
            'label' => ucfirst(str_replace('_', ' ', $status)),
            'bg' => 'bg-surface-alt',
            'text' => 'text-text-muted',
            'dot' => 'bg-text-muted',
            'animate' => false,
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => "{$config['bg']} {$config['text']} inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full t-size2 font-semibold whitespace-nowrap"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }} {{ $config['animate'] ? 'animate-pulse' : '' }}"></span>
    {{ $config['label'] }}
</span>
