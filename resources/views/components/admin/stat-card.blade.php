@props(['label', 'value', 'tone' => 'pink', 'icon' => 'orders', 'note' => null, 'positive' => true])

@php
    $tones = [
        'pink' => ['bg' => 'bg-[#fff0f4]', 'text' => 'text-[#ff3868]'],
        'orange' => ['bg' => 'bg-[#fff3e8]', 'text' => 'text-[#ff922b]'],
        'purple' => ['bg' => 'bg-[#f5efff]', 'text' => 'text-[#8b5cf6]'],
        'green' => ['bg' => 'bg-[#eafaf0]', 'text' => 'text-[#20b66f]'],
        'blue' => ['bg' => 'bg-[#eaf4ff]', 'text' => 'text-[#3987e8]'],
    ];
    $toneClasses = $tones[$tone] ?? $tones['pink'];
@endphp

<article class="rounded-2xl border border-[#f3e2e5] bg-white p-4 shadow-[0_8px_25px_rgba(86,37,47,.04)]">
    <div class="flex items-center gap-3">
        <div class="{{ $toneClasses['bg'] }} {{ $toneClasses['text'] }} flex h-12 w-12 shrink-0 items-center justify-center rounded-full">
            @if ($icon === 'money')
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 2h12l2 5v14H4V7l2-5Z" />
                    <path d="M8 9V6a4 4 0 0 1 8 0v3" />
                </svg>
            @elseif ($icon === 'stock')
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m3 7 9-4 9 4-9 4-9-4Z" />
                    <path d="m3 7 9 4 9-4v10l-9 4-9-4V7Z" />
                </svg>
            @elseif ($icon === 'pending')
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 7v5l3 2" />
                </svg>
            @else
                <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 3h12v18H6zM9 7h6M9 11h6M9 15h4" />
                </svg>
            @endif
        </div>
        <div class="min-w-0">
            <p class="t-size1 font-semibold text-text-muted">{{ $label }}</p>
            <p class="{{ $toneClasses['text'] }} mt-1 truncate font-heading t-size6 font-extrabold">{{ $value }}</p>
            @if ($note)
                <p class="mt-1 t-size1 {{ $positive ? 'text-emerald-600' : 'text-text-muted' }}">{{ $note }}</p>
            @endif
        </div>
    </div>
</article>
