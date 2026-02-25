@if ($paginator->hasPages())
    <nav aria-label="Pagination">
        <ul class="inline-flex items-stretch -space-x-px">
            {{-- Previous --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="flex items-center justify-center h-full py-2 px-3 rounded-l-lg
                                 border border-zinc-200 dark:border-zinc-800
                                 bg-white dark:bg-zinc-900 text-zinc-400 cursor-not-allowed">
                        ‹
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled"
                        class="flex items-center justify-center h-full py-2 px-3 rounded-l-lg
                               border border-zinc-200 dark:border-zinc-800
                               bg-white dark:bg-zinc-900 text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                        ‹
                    </button>
                @endif
            </li>

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li>
                        <span class="flex items-center justify-center text-sm py-2 px-3
                                     border border-zinc-200 dark:border-zinc-800
                                     bg-white dark:bg-zinc-900 text-zinc-500">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                    class="flex items-center justify-center text-sm z-10 py-2 px-3
                                           border border-blue-500/40
                                           bg-blue-500/10 text-blue-600 dark:text-blue-300">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPage({{ $page }})"
                                    class="flex items-center justify-center text-sm py-2 px-3
                                           border border-zinc-200 dark:border-zinc-800
                                           bg-white dark:bg-zinc-900 text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                    {{ $page }}
                                </button>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            <li>
                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled"
                        class="flex items-center justify-center h-full py-2 px-3 rounded-r-lg
                               border border-zinc-200 dark:border-zinc-800
                               bg-white dark:bg-zinc-900 text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                        ›
                    </button>
                @else
                    <span class="flex items-center justify-center h-full py-2 px-3 rounded-r-lg
                                 border border-zinc-200 dark:border-zinc-800
                                 bg-white dark:bg-zinc-900 text-zinc-400 cursor-not-allowed">
                        ›
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
