<section class="p-4 sm:p-6">
    <div class="mx-auto space-y-5">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="p-2.5 rounded-2xl bg-blue-500/10 text-blue-400 border border-blue-500">
                    <x-heroicon-o-map class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                        Peta Monitoring
                    </h1>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Marker otomatis dari lokasi alat (latitude/longitude).
                    </p>
                </div>
            </div>
            <button type="button" wire:click="loadDevices"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium
                       bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800
                       hover:bg-zinc-50 dark:hover:bg-zinc-900/60">
                <x-heroicon-o-arrow-path class="w-4 h-4" />
                Refresh Marker
            </button>
        </div>

        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950/60 overflow-hidden p-4">
            <div class="relative"
                x-data="windyMapComponent({
                    key: '{{ $windyKey }}',
                    devices: @js($devices),
                    overlay: 'wind',
                    lat: -6.2,
                    lon: 106.8,
                    zoom: 9
                })"
                x-init="init()"
                x-on:render-markers.window="onRenderMarkers($event)">

                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Windy Map</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400"
                            x-text="`Total alat terdeteksi lokasi: ${devices.length}`"></div>
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400" x-show="!error">
                        Memuat peta Windy...
                    </div>
                </div>

                <div wire:ignore>
                    <div id="windy"
                        class="w-full rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800" style="height: 512px;"></div>
                </div>

                <div x-show="error"
                    class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-zinc-950/70 p-4">
                    <div class="max-w-md w-full rounded-xl border border-red-500/20 bg-red-500/10 p-4">
                        <div class="font-semibold text-red-600 dark:text-red-300">Windy error</div>
                        <div class="text-sm text-red-600/90 dark:text-red-300/90 mt-1" x-text="error"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>