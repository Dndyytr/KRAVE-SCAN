@if ($paginator->hasPages())
    <nav class="flex items-center justify-center" role="navigation" aria-label="{{ __('Navigasi halaman') }}">
        <div class="inline-flex items-center gap-1.5 rounded-2xl border border-border bg-card p-1.5 shadow-[0_6px_20px_var(--color-shadow)]">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}"
                    class="inline-flex h-10 w-10 cursor-not-allowed items-center justify-center rounded-xl text-text-muted/35">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-text-muted hover:bg-surface hover:text-primary-strong focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                </a>
            @endif

            <span class="px-2 t-size2 font-bold text-text sm:hidden">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            <div class="hidden items-center gap-1.5 sm:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex h-10 min-w-8 items-center justify-center px-1 t-size2 font-bold text-text-muted" aria-hidden="true">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-primary-strong px-3 t-size2 font-extrabold text-white shadow-[0_6px_16px_var(--color-shadow)]">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" aria-label="{{ __('Buka halaman :page', ['page' => $page]) }}"
                                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl px-3 t-size2 font-bold text-text-muted hover:bg-surface hover:text-primary-strong focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-text-muted hover:bg-surface hover:text-primary-strong focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                        <path d="m9 18 6-6-6-6" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"
                    class="inline-flex h-10 w-10 cursor-not-allowed items-center justify-center rounded-xl text-text-muted/35">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                        <path d="m9 18 6-6-6-6" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
