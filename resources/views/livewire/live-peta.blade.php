<section
    wire:poll.5s="loadDevices"
    x-data="windyMapComponent({
        key: '{{ $windyKey }}',
        devices: @js($devices),
        overlay: 'wind',
        lat: -1.8367,
        lon: 110.0167,
        zoom: 11,
    })"
    x-init="init()"
    x-on:render-markers.window="onRenderMarkers($event)"
    class="w-full px-6 py-4"
>
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">

        {{-- Header Card --}}
        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="p-1.5 rounded-xl bg-blue-500/10 text-blue-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Peta Monitoring ROB</div>
                    <div class="flex items-center gap-1.5">
                        <div class="text-xs text-slate-500 dark:text-slate-400" x-text="`${devices.length} alat terdeteksi`"></div>
                        <span class="text-slate-300 dark:text-slate-600">·</span>
                        <div class="flex items-center gap-1 text-xs text-blue-500">
                            Live
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                                <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Legend + Refresh --}}
            <div class="flex items-center gap-3">
                {{-- Legend --}}
                <div class="hidden sm:flex items-center gap-3">
                    @foreach ([
                        ['color' => 'bg-emerald-500', 'label' => 'Aman'],
                        ['color' => 'bg-amber-500',   'label' => 'Waspada'],
                        ['color' => 'bg-orange-500',  'label' => 'Siaga'],
                        ['color' => 'bg-red-500',     'label' => 'Bahaya'],
                    ] as $item)
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full {{ $item['color'] }}"></span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">{{ $item['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="w-px h-5 bg-slate-200 dark:bg-slate-700 hidden sm:block"></div>

                {{-- Refresh button --}}
                <button
                    type="button"
                    wire:click="loadDevices"
                    class="inline-flex items-center gap-2 rounded-xl px-3 py-1.5 text-sm font-medium
                           bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700
                           text-slate-600 dark:text-slate-400
                           hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                >
                    <svg class="w-4 h-4" wire:loading.class="animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <span wire:loading.remove wire:target="loadDevices">Refresh</span>
                    <span wire:loading wire:target="loadDevices">Loading...</span>
                </button>
            </div>
        </div>

        {{-- Map --}}
        <div class="relative w-full h-[calc(100vh-220px)] min-h-[520px]">

            {{-- Windy --}}
            <div wire:ignore class="absolute inset-0 z-0">
                <div id="windy" class="w-full h-full"></div>
            </div>

            {{-- Loading --}}
            <div class="absolute bottom-4 left-4 z-[1000]" x-show="loading" x-cloak>
                <div class="flex items-center gap-2 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm rounded-xl px-3 py-2 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <svg class="animate-spin w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                        <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" class="opacity-75"/>
                    </svg>
                    <span class="text-xs text-slate-600 dark:text-slate-400">Memuat peta...</span>
                </div>
            </div>

            {{-- Error --}}
            <div
                x-show="error"
                x-cloak
                class="absolute bottom-4 left-1/2 -translate-x-1/2 z-[1000] max-w-md w-full rounded-xl border border-red-500/20 bg-red-500/10 backdrop-blur-sm p-4 shadow"
            >
                <div class="font-semibold text-red-600 dark:text-red-300">Windy error</div>
                <div class="text-sm text-red-600/90 dark:text-red-300/90 mt-1" x-text="error"></div>
            </div>

            {{-- Legend mobile --}}
            <div class="absolute top-3 right-3 z-[1000] sm:hidden">
                <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-xl px-3 py-2 border border-slate-200 dark:border-slate-700 shadow">
                    <div class="space-y-1">
                        @foreach ([
                            ['color' => 'bg-emerald-500', 'label' => 'Aman'],
                            ['color' => 'bg-amber-500',   'label' => 'Waspada'],
                            ['color' => 'bg-orange-500',  'label' => 'Siaga'],
                            ['color' => 'bg-red-500',     'label' => 'Bahaya'],
                        ] as $item)
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $item['color'] }}"></span>
                                <span class="text-[10px] text-slate-600 dark:text-slate-400">{{ $item['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>