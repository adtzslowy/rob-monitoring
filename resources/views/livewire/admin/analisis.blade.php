<section class="p-3 sm:p-4 md:p-6">
    <div class="mx-auto max-w-7xl space-y-5">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4 min-w-0">
                <div class="shrink-0 flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500/20 to-blue-600/10 border border-blue-500/20 text-blue-400">
                    <x-heroicon-o-chart-bar class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">
                        Analisis Cuaca
                    </h1>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                        Perbandingan data sensor dengan prakiraan BMKG
                    </p>
                </div>
            </div>

            {{-- Refresh BMKG --}}
            <button wire:click="refreshBmkg"
                class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold
                       border border-zinc-200 dark:border-zinc-700
                       bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300
                       hover:bg-zinc-50 dark:hover:bg-zinc-800 transition cursor-pointer shadow-sm">
                <x-heroicon-o-arrow-path class="w-3.5 h-3.5" wire:loading.class="animate-spin" />
                Refresh BMKG
            </button>
        </div>

        {{-- Filter --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 shadow-sm p-4">
            <div class="flex flex-col sm:flex-row gap-3">

                {{-- Pilih Wilayah BMKG --}}
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-1.5">
                        Wilayah BMKG
                    </label>
                    <div class="relative">
                        <select wire:model.live="selectedWilayah"
                            class="appearance-none w-full rounded-xl border border-zinc-200 dark:border-zinc-700
                                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100
                                   px-3 py-2.5 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30
                                   transition cursor-pointer">
                            @foreach ($this->wilayahList as $key => $item)
                                <option value="{{ $key }}">{{ $item['label'] }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
                            <x-heroicon-o-chevron-down class="w-4 h-4" />
                        </div>
                    </div>
                </div>

                {{-- Pilih Device --}}
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-1.5">
                        Device Sensor
                    </label>
                    <div class="relative">
                        <select wire:model.live="selectedDevice"
                            class="appearance-none w-full rounded-xl border border-zinc-200 dark:border-zinc-700
                                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100
                                   px-3 py-2.5 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30
                                   transition cursor-pointer">
                            @foreach ($devices as $device)
                                <option value="{{ $device['id'] }}">{{ $device['label'] }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
                            <x-heroicon-o-chevron-down class="w-4 h-4" />
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Perbandingan Card --}}
        @if (!empty($comparison['bmkg']) || !empty($comparison['sensor']))
            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-900/60">
                    <h2 class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Perbandingan Data Terkini</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">
                        BMKG: {{ $this->wilayahLabel }} ·
                        Sensor: rata-rata 1 jam terakhir
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30">
                                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Parameter</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">BMKG</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Sensor</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Selisih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                            @php
                                $rows = [
                                    ['label' => 'Suhu', 'key' => 'suhu', 'unit' => '°C', 'color' => 'orange'],
                                    ['label' => 'Kelembapan', 'key' => 'kelembapan', 'unit' => '%', 'color' => 'cyan'],
                                    ['label' => 'Kecepatan Angin', 'key' => 'kecepatan_angin', 'unit' => 'm/s', 'color' => 'amber'],
                                ];
                            @endphp
                            @foreach ($rows as $row)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900/40 transition-colors">
                                    <td class="px-4 py-3 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                        {{ $row['label'] }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if (!empty($comparison['bmkg'][$row['key']]))
                                            <span class="font-semibold text-blue-500">{{ $comparison['bmkg'][$row['key']] }}</span>
                                            <span class="text-xs text-zinc-400"> {{ $row['unit'] }}</span>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if (!empty($comparison['sensor'][$row['key']]))
                                            <span class="font-semibold text-{{ $row['color'] }}-500">{{ $comparison['sensor'][$row['key']] }}</span>
                                            <span class="text-xs text-zinc-400"> {{ $row['unit'] }}</span>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if (!empty($comparison['selisih'][$row['key']]))
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                                {{ $comparison['selisih'][$row['key']] > 5 ? 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' : 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' }}">
                                                ±{{ $comparison['selisih'][$row['key']] }} {{ $row['unit'] }}
                                            </span>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Tabel Data Sensor 24 Jam --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-900/60">
                <h2 class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Data Sensor 24 Jam Terakhir</h2>
                <p class="text-xs text-zinc-400 mt-0.5">{{ count($sensorData) }} data tercatat</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[900px] w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30">
                            <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Waktu</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Suhu</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Kelembapan</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Tekanan</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Angin</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Arah</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Ketinggian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @forelse (array_reverse($sensorData) as $row)
                            <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900/40 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                    {{ $row['local_datetime'] }}
                                </td>
                                <td class="px-4 py-3 text-center text-xs">
                                    @if ($row['suhu'] !== null)
                                        <span class="font-semibold text-orange-500">{{ $row['suhu'] }}</span>
                                        <span class="text-zinc-400"> °C</span>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-xs">
                                    @if ($row['kelembapan'] !== null)
                                        <span class="font-semibold text-cyan-500">{{ $row['kelembapan'] }}</span>
                                        <span class="text-zinc-400"> %</span>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-xs">
                                    @if ($row['tekanan_udara'] !== null)
                                        <span class="font-semibold text-emerald-500">{{ $row['tekanan_udara'] }}</span>
                                        <span class="text-zinc-400"> hPa</span>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-xs">
                                    @if ($row['kecepatan_angin'] !== null)
                                        <span class="font-semibold text-amber-500">{{ $row['kecepatan_angin'] }}</span>
                                        <span class="text-zinc-400"> m/s</span>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-xs">
                                    @if ($row['arah_angin_deg'] !== null)
                                        <span class="font-semibold text-violet-500">{{ $row['arah_angin_deg'] }}°</span>
                                        <span class="text-zinc-400"> ({{ $row['arah_angin_label'] }})</span>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-xs">
                                    @if ($row['ketinggian_air'] !== null)
                                        <span class="font-bold text-blue-500">{{ $row['ketinggian_air'] }}</span>
                                        <span class="text-zinc-400"> cm</span>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800/60">
                                            <x-heroicon-o-chart-bar class="w-6 h-6 text-zinc-400" />
                                        </div>
                                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tidak ada data sensor</p>
                                        <p class="text-xs text-zinc-400 dark:text-zinc-500">Pilih device yang memiliki data</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="px-4 py-3 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-900/60">
                <span class="text-xs text-zinc-400">
                    Menampilkan
                    <span class="font-semibold text-zinc-600 dark:text-zinc-300">{{ count($sensorData) }}</span>
                    data dalam 24 jam terakhir
                </span>
            </div>
        </div>

        {{-- Prakiraan BMKG --}}
        @if (!empty($bmkgData))
            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-900/60">
                    <h2 class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Prakiraan BMKG</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">{{ $this->wilayahLabel }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-[900px] w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30">
                                <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Waktu</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Cuaca</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Suhu</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Kelembapan</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">Angin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                            @foreach ($bmkgData as $item)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900/40 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                        {{ $item['local_datetime'] ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-zinc-600 dark:text-zinc-400">
                                        {{ $item['cuaca'] ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs">
                                        @if (!empty($item['suhu']))
                                            <span class="font-semibold text-orange-500">{{ $item['suhu'] }}</span>
                                            <span class="text-zinc-400"> °C</span>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs">
                                        @if (!empty($item['kelembapan']))
                                            <span class="font-semibold text-cyan-500">{{ $item['kelembapan'] }}</span>
                                            <span class="text-zinc-400"> %</span>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs">
                                        @if (!empty($item['kecepatan_angin']))
                                            <span class="font-semibold text-amber-500">{{ $item['kecepatan_angin'] }}</span>
                                            <span class="text-zinc-400"> km/j</span>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</section>