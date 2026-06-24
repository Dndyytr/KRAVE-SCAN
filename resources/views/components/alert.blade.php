@props(['type' => null, 'message' => null, 'dismissible' => true])

@php
    // Determine type from session if not explicitly passed
    $sessionType = null;
    $sessionMessage = null;

    if (session('success')) {
        $sessionType = 'success';
        $sessionMessage = session('success');
    } elseif (session('error')) {
        $sessionType = 'error';
        $sessionMessage = session('error');
    } elseif (session('warning')) {
        $sessionType = 'warning';
        $sessionMessage = session('warning');
    } elseif (session('info')) {
        $sessionType = 'info';
        $sessionMessage = session('info');
    }

    $alertType = $type ?? $sessionType;
    $alertMessage = $message ?? $sessionMessage;

    $styles = match($alertType) {
        'success' => [
            'bg' => 'bg-success-soft',
            'border' => 'border-success/40',
            'text' => 'text-green-800',
            'icon_color' => 'text-success',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        ],
        'error' => [
            'bg' => 'bg-danger-soft',
            'border' => 'border-danger/40',
            'text' => 'text-red-800',
            'icon_color' => 'text-danger',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        ],
        'warning' => [
            'bg' => 'bg-warning-soft',
            'border' => 'border-warning/40',
            'text' => 'text-yellow-800',
            'icon_color' => 'text-warning',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>',
        ],
        'info' => [
            'bg' => 'bg-info-soft',
            'border' => 'border-info/40',
            'text' => 'text-blue-800',
            'icon_color' => 'text-info',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        ],
        default => null,
    };
@endphp

@if($alertMessage && $styles)
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="{{ $styles['bg'] }} border {{ $styles['border'] }} {{ $styles['text'] }} rounded-xl px-4 py-3 mb-6 flex items-center gap-3 t-size3 font-medium shadow-xs anim-slide-down"
         role="alert">
        <svg class="w-5 h-5 {{ $styles['icon_color'] }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            {!! $styles['icon'] !!}
        </svg>
        <span class="flex-grow">{{ $alertMessage }}</span>
        @if($dismissible)
            <button @click="show = false" class="shrink-0 opacity-60 hover:opacity-100 transition-opacity p-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        @endif
    </div>
@endif
