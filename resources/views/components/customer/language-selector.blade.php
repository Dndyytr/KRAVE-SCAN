<div {{ $attributes->class(['relative shrink-0']) }} x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <button type="button" @click="open = !open" :aria-expanded="open" aria-haspopup="true"
        class="grid h-[46px] w-[210px] grid-cols-[18px_minmax(0,1fr)_14px] items-center gap-[11px] rounded-full border border-[#dedbd9] bg-white px-[17px] text-left text-xs font-bold text-[#302827] shadow-[0_3px_10px_rgba(87,53,52,.04)] transition duration-200 hover:border-[#f4a2b4] hover:bg-[#fffafa] hover:shadow-[0_6px_16px_rgba(214,126,146,.10)] focus-visible:border-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/20">
        <svg class="size-[18px] text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="9" />
            <path d="M3.6 9h16.8M3.6 15h16.8M12 3c2.4 2.5 3.6 5.5 3.6 9S14.4 18.5 12 21c-2.4-2.5-3.6-5.5-3.6-9S9.6 5.5 12 3Z" />
        </svg>
        <span class="truncate">{{ app()->getLocale() === 'en' ? 'English' : 'Bahasa Indonesia' }}</span>
        <svg class="size-[14px] justify-self-end transition-transform duration-300" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="m6 9 6 6 6-6" />
        </svg>
    </button>
    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        class="absolute right-0 top-[53px] z-30 w-[210px] origin-top-right overflow-hidden rounded-[14px] border border-[#ebddda] bg-white py-1 shadow-[0_14px_32px_rgba(87,53,52,.14)]">
        @foreach (['id' => 'Bahasa Indonesia', 'en' => 'English'] as $locale => $label)
            <a href="{{ route('locale.switch', $locale) }}"
                class="flex min-h-[42px] items-center justify-between px-[14px] py-2 t-size1 font-semibold transition duration-200 {{ app()->getLocale() === $locale ? 'bg-[#fff4f3] text-primary' : 'text-[#4d4240] hover:bg-[#fff4f3] hover:text-primary' }}">
                <span>{{ $label }}</span>
                @if (app()->getLocale() === $locale)
                    <span class="font-extrabold" aria-hidden="true">✓</span>
                @endif
            </a>
        @endforeach
    </div>
</div>
