<section class="p-4 sm:p-6">
    <div class="mx-auto space-y-5">

        <!-- HEADER -->
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

        <!-- MAP CARD -->
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950/60 overflow-hidden">

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
                x-on:render-markers.window="onRenderMarkers($event)"
            >

                <!-- TOP INFO -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div>
                        <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                            Windy Map
                        </div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400"
                            x-text="`${devices.length} alat terdeteksi`">
                        </div>
                    </div>

                    <!-- Badge layer aktif -->
                    <div class="flex items-center gap-2" x-show="!loading">
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Layer:</span>
                        <span class="text-xs font-semibold text-blue-500 bg-blue-500/10 border border-blue-500/20 px-2 py-0.5 rounded-full"
                              x-text="layers.find(l => l.key === activeOverlay)?.label ?? '-'">
                        </span>
                    </div>
                </div>

                <!-- MAP AREA -->
                <div class="relative w-full h-[calc(100vh-260px)] min-h-[500px]">

                    <!-- MAP -->
                    <div wire:ignore>
                        <div id="windy" class="w-full h-full"></div>
                    </div>

                    <!-- ─── LAYER SWITCHER ─────────────────────────────────── -->
                    <div
                        x-show="!loading && !error"
                        x-cloak
                        class="absolute top-3 right-3 z-[1000]"
                    >
                        <div class="bg-white/95 dark:bg-zinc-900/95 backdrop-blur-sm rounded-2xl shadow-lg border border-zinc-200 dark:border-zinc-800 overflow-hidden">

                            <!-- Label -->
                            <div class="px-3 pt-2.5 pb-1.5 border-b border-zinc-100 dark:border-zinc-800">
                                <span class="text-[10px] font-bold tracking-widest text-zinc-400 uppercase">Layer Peta</span>
                            </div>

                            <!-- Tombol layer -->
                            <div class="p-1.5 flex flex-col gap-0.5">
                                <template x-for="layer in layers" :key="layer.key">
                                    <button
                                        type="button"
                                        @click="changeOverlay(layer.key)"
                                        class="flex items-center gap-2.5 w-full px-3 py-2 rounded-xl text-left text-sm font-medium transition-all duration-150"
                                        :class="activeOverlay === layer.key
                                            ? 'bg-blue-500 text-white shadow-sm'
                                            : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800'"
                                    >
                                        <span class="text-base leading-none" x-text="layer.icon"></span>
                                        <span x-text="layer.label"></span>
                                    </button>
                                </template>
                            </div>

                        </div>
                    </div>
                    <!-- ─────────────────────────────────────────────────────── -->

                    <!-- LOADING -->
                    <div class="absolute bottom-4 left-4 z-[1000]" x-show="loading" x-cloak>
                        <div class="flex items-center gap-2 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm rounded-xl px-3 py-2 border border-zinc-200 dark:border-zinc-800 shadow-sm">
                            <svg class="animate-spin w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                                <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" class="opacity-75"/>
                            </svg>
                            <span class="text-xs text-zinc-600 dark:text-zinc-400">Memuat peta...</span>
                        </div>
                    </div>

                    <!-- ERROR -->
                    <div
                        x-show="error"
                        x-cloak
                        class="absolute bottom-4 left-1/2 -translate-x-1/2 z-[1000]
                               max-w-md w-full rounded-xl border border-red-500/20
                               bg-red-500/10 backdrop-blur-sm p-4 shadow"
                    >
                        <div class="font-semibold text-red-600 dark:text-red-300">Windy error</div>
                        <div class="text-sm text-red-600/90 dark:text-red-300/90 mt-1" x-text="error"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>