{{-- Charts --}}
    <div
        x-data="analisisChart"
        x-init="init()"
        wire:key="analisis-charts-{{ $selectedWilayah }}-{{ $selectedDevice }}"
        class="grid grid-cols-1 lg:grid-cols-2 gap-4"
    >
        {{-- Chart Suhu --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Tren Suhu — Sensor vs BMKG</h3>
            </div>
            <div class="h-52">
                <canvas id="chartSuhu"></canvas>
            </div>
        </div>

        {{-- Chart Kelembapan --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-2 rounded-full bg-cyan-500"></div>
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Tren Kelembapan — Sensor vs BMKG</h3>
            </div>
            <div class="h-52">
                <canvas id="chartKelembapan"></canvas>
            </div>
        </div>

        {{-- Chart Angin --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Tren Kec. Angin — Sensor vs BMKG</h3>
            </div>
            <div class="h-52">
                <canvas id="chartAngin"></canvas>
            </div>
        </div>

        {{-- Tabel Prakiraan BMKG --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                    Prakiraan BMKG — {{ $this->wilayahLabel }}
                </h3>
            </div>
            <div class="overflow-auto max-h-52">
                <table class="w-full text-xs">
                    <thead class="sticky top-0 bg-white dark:bg-zinc-900">
                        <tr class="border-b border-zinc-100 dark:border-zinc-800">
                            <th class="text-left py-2 px-2 text-zinc-500 font-medium">Waktu</th>
                            <th class="text-center py-2 px-2 text-zinc-500 font-medium">Suhu</th>
                            <th class="text-center py-2 px-2 text-zinc-500 font-medium">RH</th>
                            <th class="text-center py-2 px-2 text-zinc-500 font-medium">Angin</th>
                            <th class="text-left py-2 px-2 text-zinc-500 font-medium">Cuaca</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (array_slice($bmkgData, 0, 10) as $item)
                            <tr class="border-b border-zinc-50 dark:border-zinc-800/50 hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition">
                                <td class="py-2 px-2 text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item['local_datetime'])->format('d M H:i') }}
                                </td>
                                <td class="py-2 px-2 text-center font-semibold text-orange-500">{{ $item['suhu'] }}°</td>
                                <td class="py-2 px-2 text-center text-cyan-500">{{ $item['kelembapan'] }}%</td>
                                <td class="py-2 px-2 text-center text-amber-500">{{ $item['kecepatan_angin'] }} m/s</td>
                                <td class="py-2 px-2 text-zinc-500 dark:text-zinc-400">{{ $item['cuaca'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-zinc-400">Tidak ada data BMKG</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>