<div class="space-y-6 px-6 py-4">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Analisis Cuaca</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Perbandingan data sensor dengan prakiraan BMKG</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Filter Wilayah BMKG --}}
            <select
                wire:model.live="selectedWilayah"
                class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-700 dark:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                <option value="delta_pawan">Delta Pawan</option>
                <option value="sungai_awan">Sungai Awan</option>
                <option value="benua_kayong">Benua Kayong</option>
            </select>

            {{-- Filter Device --}}
            <select
                wire:model.live="selectedDevice"
                class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-700 dark:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                @foreach ($devices as $d)
                    <option value="{{ $d['id'] }}">{{ $d['label'] }}</option>
                @endforeach
            </select>

            {{-- Refresh BMKG --}}
            <button
                wire:click="refreshBmkg"
                class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition"
            >
                <svg class="w-4 h-4" wire:loading.class="animate-spin" wire:target="refreshBmkg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Refresh BMKG
            </button>
        </div>
    </div>

    {{-- Comparison Cards --}}
    @if ($comparison['bmkg'] && $comparison['sensor'])
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Suhu --}}
            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-1.5 rounded-xl bg-orange-500/10">
                        <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Suhu Udara</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-orange-50 dark:bg-orange-500/10 p-3 text-center">
                        <p class="text-[10px] font-semibold text-orange-600 dark:text-orange-400 uppercase">Sensor</p>
                        <p class="text-2xl font-extrabold text-zinc-900 dark:text-white mt-1">{{ $comparison['sensor']['suhu'] }}</p>
                        <p class="text-xs text-zinc-400">°C</p>
                    </div>
                    <div class="rounded-xl bg-zinc-50 dark:bg-zinc-800 p-3 text-center">
                        <p class="text-[10px] font-semibold text-zinc-500 uppercase">BMKG</p>
                        <p class="text-2xl font-extrabold text-zinc-900 dark:text-white mt-1">{{ $comparison['bmkg']['suhu'] }}</p>
                        <p class="text-xs text-zinc-400">°C</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between px-1">
                    <span class="text-xs text-zinc-400">Selisih</span>
                    <span class="text-sm font-bold {{ $comparison['selisih']['suhu'] > 3 ? 'text-red-500' : 'text-emerald-500' }}">
                        {{ $comparison['selisih']['suhu'] }} °C
                    </span>
                </div>
            </div>

            {{-- Kelembapan --}}
            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-1.5 rounded-xl bg-cyan-500/10">
                        <svg class="w-4 h-4 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Kelembapan</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-cyan-50 dark:bg-cyan-500/10 p-3 text-center">
                        <p class="text-[10px] font-semibold text-cyan-600 dark:text-cyan-400 uppercase">Sensor</p>
                        <p class="text-2xl font-extrabold text-zinc-900 dark:text-white mt-1">{{ $comparison['sensor']['kelembapan'] }}</p>
                        <p class="text-xs text-zinc-400">%</p>
                    </div>
                    <div class="rounded-xl bg-zinc-50 dark:bg-zinc-800 p-3 text-center">
                        <p class="text-[10px] font-semibold text-zinc-500 uppercase">BMKG</p>
                        <p class="text-2xl font-extrabold text-zinc-900 dark:text-white mt-1">{{ $comparison['bmkg']['kelembapan'] }}</p>
                        <p class="text-xs text-zinc-400">%</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between px-1">
                    <span class="text-xs text-zinc-400">Selisih</span>
                    <span class="text-sm font-bold {{ $comparison['selisih']['kelembapan'] > 15 ? 'text-red-500' : 'text-emerald-500' }}">
                        {{ $comparison['selisih']['kelembapan'] }} %
                    </span>
                </div>
            </div>

            {{-- Kecepatan Angin --}}
            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-1.5 rounded-xl bg-amber-500/10">
                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12.75 3.03v.568c0 .334.148.65.405.864l1.068.89c.442.369.535 1.01.216 1.49l-.51.766a2.25 2.25 0 01-1.161.886l-.143.048a1.107 1.107 0 00-.57 1.664c.369.555.169 1.307-.427 1.605L9 13.125l.423 1.059a.956.956 0 01-1.652.928l-.679-.906a1.125 1.125 0 00-1.906.172L4.5 15.75l-.612.153M12.75 3.031a9 9 0 00-8.862 12.872M12.75 3.031a9 9 0 016.69 14.036m0 0l-.177-.529A2.25 2.25 0 0017.128 15H16.5l-.324-.324a1.453 1.453 0 00-2.328.377l-.036.073a1.586 1.586 0 01-.982.816l-.99.282c-.55.157-.894.702-.8 1.267l.073.438c.08.474.49.821.97.821.846 0 1.598.542 1.865 1.345l.215.643" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Kecepatan Angin</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-amber-50 dark:bg-amber-500/10 p-3 text-center">
                        <p class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 uppercase">Sensor</p>
                        <p class="text-2xl font-extrabold text-zinc-900 dark:text-white mt-1">{{ $comparison['sensor']['kecepatan_angin'] }}</p>
                        <p class="text-xs text-zinc-400">m/s</p>
                    </div>
                    <div class="rounded-xl bg-zinc-50 dark:bg-zinc-800 p-3 text-center">
                        <p class="text-[10px] font-semibold text-zinc-500 uppercase">BMKG</p>
                        <p class="text-2xl font-extrabold text-zinc-900 dark:text-white mt-1">{{ $comparison['bmkg']['kecepatan_angin'] }}</p>
                        <p class="text-xs text-zinc-400">m/s</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between px-1">
                    <span class="text-xs text-zinc-400">Selisih</span>
                    <span class="text-sm font-bold {{ $comparison['selisih']['kecepatan_angin'] > 5 ? 'text-red-500' : 'text-emerald-500' }}">
                        {{ $comparison['selisih']['kecepatan_angin'] }} m/s
                    </span>
                </div>
            </div>

        </div>
    @else
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 p-8 text-center">
            <p class="text-sm text-zinc-500">Tidak ada data perbandingan tersedia saat ini.</p>
        </div>
    @endif

    {{-- Chart Perbandingan --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Chart Suhu --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-4">Tren Suhu — Sensor vs BMKG</h3>
            <div class="h-48">
                <canvas id="chartSuhu"></canvas>
            </div>
        </div>

        {{-- Chart Kelembapan --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-4">Tren Kelembapan — Sensor vs BMKG</h3>
            <div class="h-48">
                <canvas id="chartKelembapan"></canvas>
            </div>
        </div>

        {{-- Chart Kecepatan Angin --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-4">Tren Kecepatan Angin — Sensor vs BMKG</h3>
            <div class="h-48">
                <canvas id="chartAngin"></canvas>
            </div>
        </div>

        {{-- Prakiraan BMKG Table --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-5">
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-4">Prakiraan BMKG — {{ collect(BmkgServices::WILAYAH)[$selectedWilayah]['label'] ?? '' }}</h3>
            <div class="overflow-auto max-h-48">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-zinc-100 dark:border-zinc-800">
                            <th class="text-left py-2 px-2 text-zinc-500 font-medium">Waktu</th>
                            <th class="text-center py-2 px-2 text-zinc-500 font-medium">Suhu</th>
                            <th class="text-center py-2 px-2 text-zinc-500 font-medium">RH</th>
                            <th class="text-center py-2 px-2 text-zinc-500 font-medium">Angin</th>
                            <th class="text-left py-2 px-2 text-zinc-500 font-medium">Cuaca</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (array_slice($bmkgData, 0, 8) as $item)
                            <tr class="border-b border-zinc-50 dark:border-zinc-800/50 hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                                <td class="py-2 px-2 text-zinc-600 dark:text-zinc-400">
                                    {{ \Carbon\Carbon::parse($item['local_datetime'])->format('d M H:i') }}
                                </td>
                                <td class="py-2 px-2 text-center font-semibold text-orange-500">{{ $item['suhu'] }}°</td>
                                <td class="py-2 px-2 text-center text-cyan-500">{{ $item['kelembapan'] }}%</td>
                                <td class="py-2 px-2 text-center text-amber-500">{{ $item['kecepatan_angin'] }} m/s</td>
                                <td class="py-2 px-2 text-zinc-500">{{ $item['cuaca'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Chart JS --}}
    <script>
        (function () {
            const sensorData  = @js($sensorData);
            const bmkgData    = @js($bmkgData);

            const isDark = document.documentElement.classList.contains('dark');
            const gridColor  = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
            const tickColor  = isDark ? '#71717a' : '#a1a1aa';

            const chartDefaults = {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: { legend: { labels: { color: tickColor, font: { size: 11 } } } },
                scales: {
                    x: { ticks: { color: tickColor, maxTicksLimit: 6, font: { size: 10 } }, grid: { color: gridColor } },
                    y: { ticks: { color: tickColor, font: { size: 10 } }, grid: { color: gridColor } },
                }
            };

            // Labels sensor
            const sensorLabels = sensorData.map(d => d.local_datetime.substring(11, 16));
            const bmkgLabels   = bmkgData.map(d => d.local_datetime.substring(11, 16));

            // Chart Suhu
            const ctxSuhu = document.getElementById('chartSuhu');
            if (ctxSuhu) {
                new Chart(ctxSuhu.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: [...new Set([...sensorLabels, ...bmkgLabels])].slice(0, 12),
                        datasets: [
                            {
                                label: 'Sensor',
                                data: sensorData.map(d => d.suhu),
                                borderColor: '#fb923c',
                                backgroundColor: 'rgba(251,146,60,0.1)',
                                tension: 0.4, fill: true, borderWidth: 2,
                                pointRadius: 3,
                            },
                            {
                                label: 'BMKG',
                                data: bmkgData.map(d => d.suhu),
                                borderColor: '#94a3b8',
                                backgroundColor: 'rgba(148,163,184,0.1)',
                                tension: 0.4, fill: true, borderWidth: 2,
                                borderDash: [5, 5],
                                pointRadius: 3,
                            },
                        ],
                    },
                    options: chartDefaults,
                });
            }

            // Chart Kelembapan
            const ctxRH = document.getElementById('chartKelembapan');
            if (ctxRH) {
                new Chart(ctxRH.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: sensorLabels.slice(0, 12),
                        datasets: [
                            {
                                label: 'Sensor',
                                data: sensorData.map(d => d.kelembapan),
                                borderColor: '#22d3ee',
                                backgroundColor: 'rgba(34,211,238,0.1)',
                                tension: 0.4, fill: true, borderWidth: 2,
                                pointRadius: 3,
                            },
                            {
                                label: 'BMKG',
                                data: bmkgData.map(d => d.kelembapan),
                                borderColor: '#94a3b8',
                                backgroundColor: 'rgba(148,163,184,0.1)',
                                tension: 0.4, fill: true, borderWidth: 2,
                                borderDash: [5, 5],
                                pointRadius: 3,
                            },
                        ],
                    },
                    options: chartDefaults,
                });
            }

            // Chart Angin
            const ctxAngin = document.getElementById('chartAngin');
            if (ctxAngin) {
                new Chart(ctxAngin.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: sensorLabels.slice(0, 12),
                        datasets: [
                            {
                                label: 'Sensor',
                                data: sensorData.map(d => d.kecepatan_angin),
                                borderColor: '#fbbf24',
                                backgroundColor: 'rgba(251,191,36,0.1)',
                                tension: 0.4, fill: true, borderWidth: 2,
                                pointRadius: 3,
                            },
                            {
                                label: 'BMKG',
                                data: bmkgData.map(d => d.kecepatan_angin),
                                borderColor: '#94a3b8',
                                backgroundColor: 'rgba(148,163,184,0.1)',
                                tension: 0.4, fill: true, borderWidth: 2,
                                borderDash: [5, 5],
                                pointRadius: 3,
                            },
                        ],
                    },
                    options: chartDefaults,
                });
            }
        })();
    </script>

</div>