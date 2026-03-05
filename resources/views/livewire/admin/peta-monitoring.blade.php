{{-- resources/views/livewire/peta-monitoring.blade.php --}}
<section
    x-data="windyMapComponent({
        key: '{{ $windyKey }}',
        devices: @js($devices),
        overlay: 'wind',
        lat: -6.2,
        lon: 106.8,
        zoom: 9
    })"
    x-init="init()"
    x-on:render-markers.window="onRenderMarkers($event)"
    class="w-full max-w-none px-6 py-4"
>
    <div
        class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm overflow-hidden"
    >
        {{-- Card header --}}
        <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center gap-3">
                <div class="p-1.5 rounded-xl bg-blue-500/10 text-blue-500">
                    <x-heroicon-o-map class="w-5 h-5" />
                </div>
                <div>
                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Peta Monitoring</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400" x-text="`${devices.length} alat terdeteksi`"></div>
                </div>
            </div>

            <button type="button" wire:click="loadDevices"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium
                       bg-white/90 dark:bg-zinc-900/90 border border-zinc-200 dark:border-zinc-800
                       hover:bg-white dark:hover:bg-zinc-900">
                <x-heroicon-o-arrow-path class="w-4 h-4" />
                Refresh Marker
            </button>
        </div>

        {{-- Map area: isi sisa layar (layout kamu sudah h-screen) --}}
        <div class="relative w-full h-[calc(100vh-220px)] min-h-[520px]">
            <div wire:ignore class="absolute inset-0">
                <div id="windy" class="w-full h-full"></div>
            </div>

            {{-- Loading --}}
            <div class="absolute bottom-4 left-4 z-[1000]" x-show="loading">
                <div
                    class="flex items-center gap-2 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm
                           rounded-xl px-3 py-2 border border-zinc-200 dark:border-zinc-800 shadow-sm"
                >
                    <svg class="animate-spin w-4 h-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                            class="opacity-25" />
                        <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" class="opacity-75" />
                    </svg>
                    <span class="text-xs text-zinc-600 dark:text-zinc-400">Memuat peta...</span>
                </div>
            </div>

            {{-- Error --}}
            <div x-show="error"
                class="absolute bottom-4 left-1/2 -translate-x-1/2 z-[1000]
                       max-w-md w-full rounded-xl border border-red-500/20 bg-red-500/10
                       backdrop-blur-sm p-4 shadow">
                <div class="font-semibold text-red-600 dark:text-red-300">Windy error</div>
                <div class="text-sm text-red-600/90 dark:text-red-300/90 mt-1" x-text="error"></div>
            </div>
        </div>
    </div>
</section>
