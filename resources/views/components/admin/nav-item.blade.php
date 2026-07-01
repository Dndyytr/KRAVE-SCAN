@props(['href', 'active' => false, 'icon' => 'grid'])

<a href="{{ $href }}"
    {{ $attributes->class([
        'group flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold t-size2 transition-all duration-200',
        'bg-gradient-to-r from-[#ff3868] to-[#ff6585] text-white shadow-[0_10px_24px_rgba(255,56,104,.2)]' => $active,
        'text-text hover:bg-[#fff1f4] hover:text-[#ff3868]' => !$active,
    ]) }}>
    <span class="flex h-5 w-5 shrink-0 items-center justify-center">
        @switch($icon)
            @case('users')
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            @break

            @case('menu')
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 5h16M4 12h16M4 19h16" />
                    <circle cx="7" cy="5" r="1" />
                    <circle cx="12" cy="12" r="1" />
                    <circle cx="17" cy="19" r="1" />
                </svg>
            @break

            @case('stock')
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m3 7 9-4 9 4-9 4-9-4Z" />
                    <path d="m3 7 9 4 9-4v10l-9 4-9-4V7Z" />
                </svg>
            @break

            @case('orders')
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M6 3h12v18H6zM9 7h6M9 11h6M9 15h4" />
                </svg>
            @break

            @case('money')
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M16 8.5c-.8-.8-2-1.3-4-1.3-2.1 0-3.5 1-3.5 2.5 0 3.8 7.5 1.5 7.5 5.3 0 1.4-1.3 2.5-3.6 2.5-1.8 0-3.3-.5-4.4-1.6M12 5v14" />
                </svg>
            @break

            @case('report')
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 20V10M10 20V4M16 20v-7M22 20H2" />
                </svg>
            @break

            @case('settings')
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21h-4v-.1A1.7 1.7 0 0 0 8.6 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3v-4h.1A1.7 1.7 0 0 0 4.6 8.6a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V3h4v.1A1.7 1.7 0 0 0 15.4 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 9c.1.37.3.72.6 1 .3.28.7.42 1.1.4h.1v4h-.1a1.7 1.7 0 0 0-1.7.6Z" />
                </svg>
            @break

            @default
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="7" height="7" rx="1" />
                    <rect x="14" y="3" width="7" height="7" rx="1" />
                    <rect x="3" y="14" width="7" height="7" rx="1" />
                    <rect x="14" y="14" width="7" height="7" rx="1" />
                </svg>
        @endswitch
    </span>
    <span class="min-w-0 flex-1 truncate">{{ $slot }}</span>
</a>
