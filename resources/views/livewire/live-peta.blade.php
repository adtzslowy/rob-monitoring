<section wire:poll.5s="loadDevices" x-data="windyMapComponent({
    key: '{{ $windyKey }}',
    devices: @js($devices),
    overlay: 'wind',
    lat: -1.8367,
    lon: 110.0167,
    zoom: 11,
})" x-init="init()"
    x-on:render-markers.window="onRenderMarkers($event)" class="w-full px-4 sm:px-6 lg:px-12 xl:px-16 py-3 sm:py-4 lg:py-6">

    {{-- Intro teks --}}
    <div class="text-center max-w-2xl mt-10 mx-auto mb-4 sm:mb-6 lg:mb-8 px-2 sm:px-4 pt-2 sm:pt-4">
        <span
            class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-2.5 sm:px-3 py-1 text-[10px] sm:text-xs font-semibold text-slate-600 dark:text-slate-400 mb-3 sm:mb-4">
            Peta Monitoring
        </span>
        <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white leading-tight sm:leading-normal">
            Sebaran Alat di Wilayah Pesisir
        </h2>
        <p class="mt-2 sm:mt-3 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
            Pantau posisi dan status seluruh sensor IoT secara real-time langsung di peta.
            Setiap titik menunjukkan kondisi terkini — dari aman hingga bahaya.
        </p>
    </div>

    <div class="max-w-7xl mx-auto rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">

        <!-- ================= HEADER ================= -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-0 px-3 sm:px-4 py-2.5 sm:py-3 border-b border-slate-200 dark:border-slate-800">

            <!-- LEFT -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <div class="p-1.5 rounded-lg sm:rounded-xl bg-blue-500/10 text-blue-500 shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">
                        Peta Monitoring
                    </div>

                    <div class="flex items-center gap-1.5 sm:gap-2 text-[10px] sm:text-xs text-slate-500 dark:text-slate-400">
                        <span x-text="`${devices.length} alat terdeteksi`"></span>

                        <span class="hidden xs:inline text-slate-300 dark:text-slate-600">•</span>

                        <!-- LIVE INDICATOR -->
                        <div class="flex items-center gap-1 text-blue-500 whitespace-nowrap">
                            <span class="hidden xs:inline">Live</span>
                            <span class="xs:hidden text-[9px]">LIVE</span>
                            <span class="relative flex h-1.5 w-1.5">
                                <span
                                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-500 opacity-75"></span>
                                <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="flex items-center gap-2 sm:gap-3 mt-2 sm:mt-0">

                <!-- DESKTOP LEGEND -->
                <div class="hidden md:flex items-center gap-2 lg:gap-3">
                    @foreach ([['color' => 'bg-emerald-500', 'label' => 'Aman'], ['color' => 'bg-amber-500', 'label' => 'Waspada'], ['color' => 'bg-orange-500', 'label' => 'Siaga'], ['color' => 'bg-red-500', 'label' => 'Bahaya']] as $item)
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $item['color'] }}"></span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $item['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <!-- TABLET LEGEND -->
                <div class="hidden sm:flex md:hidden items-center gap-1.5">
                    @foreach ([['color' => 'bg-emerald-500'], ['color' => 'bg-amber-500'], ['color' => 'bg-orange-500'], ['color' => 'bg-red-500']] as $item)
                        <span class="w-2 h-2 rounded-full {{ $item['color'] }}"></span>
                    @endforeach
                </div>

                <div class="hidden sm:block w-px h-5 bg-slate-200 dark:bg-slate-700"></div>

                <!-- REFRESH -->
                <button type="button" wire:click="loadDevices"
                    class="inline-flex items-center gap-1.5 sm:gap-2 rounded-lg sm:rounded-xl px-2.5 sm:px-3 py-1.5 text-xs sm:text-sm font-medium
                           bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700
                           text-slate-600 dark:text-slate-400
                           hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" wire:loading.class="animate-spin" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16.023 9.348h4.992M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>

                    <span wire:loading.remove wire:target="loadDevices" class="hidden xs:inline">Refresh</span>
                    <span wire:loading.remove wire:target="loadDevices" class="xs:hidden text-[10px]">Refr.</span>
                    <span wire:loading wire:target="loadDevices" class="hidden xs:inline">Loading...</span>
                    <span wire:loading wire:target="loadDevices" class="xs:hidden">...</span>
                </button>
            </div>
        </div>

        <!-- ================= MAP ================= -->
        <div class="relative w-full" style="height: 500px;"></div>a

            <!-- MAP -->
            <div wire:ignore class="absolute inset-0">
                <div id="windy" class="w-full h-full"></div>
            </div>

            <!-- LOADING -->
            <div class="absolute bottom-3 sm:bottom-4 left-3 sm:left-4 z-[1000]" x-show="loading" x-cloak>
                <div
                    class="flex items-center gap-2 px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-lg sm:rounded-xl shadow-sm
                            bg-white/90 dark:bg-slate-900/90 backdrop-blur
                            border border-slate-200 dark:border-slate-800">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 animate-spin text-blue-500" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                            class="opacity-25" />
                        <path fill="currentColor" d="M4 12a8 8 0 018-8v8z" class="opacity-75" />
                    </svg>
                    <span class="text-[10px] sm:text-xs text-slate-600 dark:text-slate-400">
                        Memuat peta...
                    </span>
                </div>
            </div>

            <!-- ERROR -->
            <div x-show="error" x-cloak
                class="absolute bottom-3 sm:bottom-4 left-1/2 -translate-x-1/2 z-[1000]
                       max-w-[280px] sm:max-w-md w-[90%] sm:w-full p-3 sm:p-4 rounded-lg sm:rounded-xl shadow
                       border border-red-500/20 bg-red-500/10 backdrop-blur">
                <div class="font-semibold text-xs sm:text-sm text-red-600 dark:text-red-300">
                    Windy error
                </div>
                <div class="text-[10px] sm:text-sm mt-1 text-red-600/90 dark:text-red-300/90" x-text="error">
                </div>
            </div>

            <!-- MOBILE LEGEND - Bottom right -->
            <div class="absolute bottom-3 right-3 sm:hidden z-[1000]">
                <div
                    class="px-2.5 py-2 rounded-lg shadow
                            bg-white/90 dark:bg-slate-900/90 backdrop-blur
                            border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        @foreach ([['color' => 'bg-emerald-500', 'label' => 'Aman'], ['color' => 'bg-amber-500', 'label' => 'Waspada'], ['color' => 'bg-orange-500', 'label' => 'Siaga'], ['color' => 'bg-red-500', 'label' => 'Bahaya']] as $item)
                            <div class="flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full {{ $item['color'] }}"></span>
                                <span class="text-[9px] text-slate-600 dark:text-slate-400">
                                    {{ $item['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
