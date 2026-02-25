<div wire:init="fetchData" wire:key="dashboard-root">
    {{-- trigger polling realtime --}}
    <div wire:poll.5s="fetchData"></div>

    <div x-data="dashboard" x-init="init()" class="flex-1 w-full px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- Risk banner --}}
        <div class="rounded-xl px-5 py-4 flex items-center justify-between border transition"
            x-bind:class="[riskStyles.bg, riskStyles.border].join(' ')">
            <div class="flex items-center gap-3">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5" x-bind:class="riskStyles.text" />

                <div>
                    <h2 class="text-lg font-bold" x-bind:class="riskStyles.text" x-text="risk"></h2>
                </div>
            </div>

            <span class="text-xs text-zinc-500">
                Monitoring aktif & singkron
            </span>
        </div>

        {{-- METRIK GRID CARD --}}
        <div class="space-y-5">
            {{-- ROW 1 --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                {{-- Temperature --}}
                <div wire:click="openMetric('suhu')"
                    class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-orange-500/40 cursor-pointer hover:border-orange-400 transition">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-zinc-9000 dark:text-white">Temperature</p>
                        <x-heroicon-o-fire class="w-5 h-5 text-orange-500 dark:text-white" />
                    </div>
                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white mt-3">
                        <span x-text="data.suhu ?? '--'"></span> °C
                    </h2>
                </div>

                {{-- KELEMBAPAN --}}
                <div wire:click="openMetric('kelembapan')"
                    class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-cyan-500/30
                           cursor-pointer hover:border-cyan-400 transition">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-zinc-900 dark:text-white">Kelembapan</p>
                        <x-heroicon-o-beaker class="w-5 h-5 text-cyan-400" />
                    </div>

                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white mt-3">
                        <span x-text="data.kelembapan ?? '--'"></span> %
                    </h2>
                </div>

                {{-- TEKANAN --}}
                <div wire:click="openMetric('tekanan_udara')"
                    class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-emerald-500/30
                           cursor-pointer hover:border-emerald-400 transition">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-zinc-900 dark:text-white">Tekanan Udara</p>
                        <x-heroicon-o-cloud class="w-5 h-5 text-emerald-400" />
                    </div>

                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white mt-3">
                        <span x-text="data.tekanan_udara ?? '--'"></span> hPa
                    </h2>
                </div>

            </div>

            {{-- ROW 2 --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                {{-- ANGIN --}}
                <div wire:click="openMetric('kecepatan_angin')"
                    class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-amber-500/30
                           cursor-pointer hover:border-amber-400 transition">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-zinc-900 dark:text-white">Kecepatan Angin</p>
                        <x-heroicon-o-flag class="w-5 h-5 text-amber-400" />
                    </div>

                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white mt-3">
                        <span x-text="data.kecepatan_angin ?? '--'"></span> m/s
                    </h2>
                </div>

                {{-- ARAH ANGIN --}}
                <div wire:click="openMetric('arah_angin')"
                    class="bg-white dark:bg-zinc-900 cursor-pointer p-6 rounded-xl border border-purple-500/30">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-zinc-900 dark:text-white">Arah Angin</p>
                        <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5 text-purple-400" />
                    </div>

                    <div class="flex justify-between items-end mt-4">
                        <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">
                            <span x-text="data.arah_angin ?? '--'"></span>°
                        </h2>

                        <span class="text-lg text-purple-400 font-medium" x-text="data.arah_angin_label ?? '-'"></span>
                    </div>
                </div>

                {{-- AIR --}}
                <div wire:click="openMetric('ketinggian_air')"
                    class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-blue-500/30
                           cursor-pointer hover:border-blue-400 transition">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-zinc-900 dark:text-white">Ketinggian Air</p>
                        <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-blue-400" />
                    </div>

                    <h2 class="text-4xl font-bold text-zinc-900 dark:text-white mt-3">
                        <span x-text="data.ketinggian_air ?? '--'"></span>
                        <span class="text-base">cm</span>
                    </h2>
                </div>

            </div>
        </div>

        {{-- ================= CHART ================= --}}
        <div class="bg-white dark:bg-zinc-950 rounded-xl border border-zinc-800 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                    Water Level Trend
                </h3>

                <div class="flex items-center gap-2">
                    <select wire:model.live="selectedTimeRange"
                        class="text-xs rounded-lg border border-zinc-300 dark:border-zinc-700
                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white
                   px-2 py-1">
                        @foreach ($timeRanges as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    <span class="text-xs text-zinc-400">Range</span>
                </div>
            </div>

            {{-- jangan pakai calc, bikin height fix --}}
            <div wire:ignore class="relative h-[320px] w-full">
                <canvas x-ref="waterChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>
    </div>

    {{-- ================= MODAL ================= --}}
    @if ($modalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" wire:click="closeModal"></div>

            <div class="relative bg-white dark:bg-zinc-950 w-[95%] max-w-3xl rounded-xl border border-zinc-800 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">
                        Detail Trend
                    </h3>

                    <div class="flex items-center gap-2">
                        <select wire:model.live="modalTimeRange"
                            class="text-xs rounded-lg border border-zinc-300 dark:border-zinc-700
                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white
                   px-2 py-1">
                            @foreach ($timeRanges as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        <button class="text-sm px-3 py-1 rounded border cursor-pointer" wire:click="closeModal">
                            x
                        </button>
                    </div>
                </div>

                {{-- polling khusus saat modal open --}}
                <div wire:poll.3s="pollMetric"></div>

                <div class="h-[360px]" wire:ignore>
                    <canvas id="metricChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
    @endif
</div>
