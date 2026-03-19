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
    class="w-full"
    style="height: calc(100vh - 64px);"
>
    <div class="relative w-full h-full">

        {{-- Windy Map — wire:ignore biar tidak kena re-render Livewire --}}
        <div wire:ignore class="absolute inset-0 z-0">
            <div id="windy" class="w-full h-full"></div>
        </div>

        {{-- Header overlay --}}
        <div class="absolute top-4 left-4 z-[1000] pointer-events-none">
            <div class="flex items-center gap-3 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl px-4 py-2.5 border border-slate-200 dark:border-slate-700 shadow-lg pointer-events-auto">
                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-600">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900 dark:text-white leading-none">Peta Monitoring ROB</p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                        <span x-text="`${devices.length} alat terdeteksi`"></span>
                        &middot;
                        <span class="text-blue-500">Live</span>
                        <span class="relative inline-flex ml-1">
                            <span class="animate-ping absolute inline-flex h-1.5 w-1.5 rounded-full bg-blue-500 opacity-75"></span>
                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="absolute top-4 right-4 z-[1000]">
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-2xl px-4 py-3 border border-slate-200 dark:border-slate-700 shadow-lg">
                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Status Risiko</p>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-xs text-slate-700 dark:text-slate-300">Aman</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="text-xs text-slate-700 dark:text-slate-300">Waspada</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                        <span class="text-xs text-slate-700 dark:text-slate-300">Siaga</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-xs text-slate-700 dark:text-slate-300">Bahaya</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading --}}
        <div
            x-show="loading"
            x-cloak
            class="absolute bottom-6 left-1/2 -translate-x-1/2 z-[1000]"
        >
            <div class="flex items-center gap-2 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-xl px-4 py-2.5 border border-slate-200 dark:border-slate-700 shadow-lg">
                <svg class="animate-spin w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" class="opacity-75"/>
                </svg>
                <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Memuat peta...</span>
            </div>
        </div>

        {{-- Error --}}
        <div
            x-show="error"
            x-cloak
            class="absolute bottom-6 left-1/2 -translate-x-1/2 z-[1000] rounded-2xl border border-red-200 dark:border-red-800 bg-red-50/90 dark:bg-red-900/30 backdrop-blur-md px-4 py-3 shadow-lg"
        >
            <p class="text-sm font-semibold text-red-600 dark:text-red-400">Gagal memuat peta</p>
            <p class="text-xs text-red-500 dark:text-red-400 mt-0.5" x-text="error"></p>
        </div>

    </div>
</section>