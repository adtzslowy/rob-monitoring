<div wire:poll.5s="refresh()"
    class="py-16 sm:py-20 lg:py-24 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 transition-colors duration-300">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- ============================================================
             HEADER
        ============================================================ --}}
        <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12 lg:mb-14">
            <span
                class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-1.5 text-xs font-semibold tracking-wide text-slate-500 dark:text-slate-400 mb-4">
                Analisis
            </span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Kondisi Sensor Saat Ini
            </h2>
            <p class="mt-3 text-sm sm:text-base text-slate-500 dark:text-slate-400 leading-relaxed max-w-lg mx-auto">
                Analisis sensor adalah proses pengumpulan, pengolahan, dan interpretasi data fisik
                (seperti suhu, tekanan, cahaya) yang diubah menjadi sinyal listrik/digital
                untuk mendapatkan informasi yang akurat dan real-time.
            </p>
        </div>

        {{-- ============================================================
             DEVICE SELECTOR
        ============================================================ --}}
        @if (!empty($devices))
            <div class="mb-8 flex flex-col sm:flex-row items-center justify-center gap-3">

                {{-- SELECTOR --}}
                @if (count($devices) > 1)
                    <select wire:model="selectedDeviceId"
                        class="appearance-none px-4 py-2.5 pr-10 rounded-xl border border-slate-300 dark:border-slate-600
                   bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                   text-sm font-medium shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach ($devices as $device)
                            <option value="{{ $device['id'] }}">
                                {{ $device['label'] }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <span
                        class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-500 dark:text-slate-400">
                        {{ $devices[0]['label'] }}
                    </span>
                @endif

                {{-- NOTE --}}
                <p class="text-xs text-slate-400 dark:text-slate-500 text-center sm:text-left max-w-xs leading-relaxed">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                    Setiap device mewakili lokasi sensor yang berbeda
                </p>

            </div>
        @endif

        {{-- ============================================================
             STATUS ANALISIS
        ============================================================ --}}
        @if (!empty($analisisData['analisa']))
            @php
                $status = $analisisData['analisa']['status'];

                $statusTheme = [
                    'AMAN' => [
                        'card' => 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800',
                        'header' => 'bg-emerald-100 dark:bg-emerald-900/40 border-emerald-200 dark:border-emerald-800',
                        'badge' => 'bg-emerald-500 text-white',
                    ],
                    'WASPADA' => [
                        'card' => 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800',
                        'header' => 'bg-amber-100 dark:bg-amber-900/40 border-amber-200 dark:border-amber-800',
                        'badge' => 'bg-amber-500 text-white',
                    ],
                    'SIAGA' => [
                        'card' => 'bg-orange-50 dark:bg-orange-950/40 border-orange-200 dark:border-orange-800',
                        'header' => 'bg-orange-100 dark:bg-orange-900/40 border-orange-200 dark:border-orange-800',
                        'badge' => 'bg-orange-500 text-white',
                    ],
                    'BAHAYA' => [
                        'card' => 'bg-red-50 dark:bg-red-950/40 border-red-200 dark:border-red-800',
                        'header' => 'bg-red-100 dark:bg-red-900/40 border-red-200 dark:border-red-800',
                        'badge' => 'bg-red-500 text-white',
                    ],
                ];

                $theme = $statusTheme[$status] ?? [
                    'card' => 'bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700',
                    'header' => 'bg-slate-100 dark:bg-slate-800 border-slate-200 dark:border-slate-700',
                    'badge' => 'bg-slate-500 text-white',
                ];
            @endphp

            <div class="mb-10 rounded-2xl border-2 {{ $theme['card'] }} shadow-lg overflow-hidden">

                {{-- Card Header --}}
                <div
                    class="px-5 sm:px-6 py-4 {{ $theme['header'] }} border-b flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-bold uppercase tracking-widest {{ $theme['badge'] }}">
                            {{ $status }}
                        </span>
                        <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">
                            Status Kondisi
                        </h3>
                    </div>
                    <time class="text-xs text-slate-400 dark:text-slate-500 whitespace-nowrap">
                        {{ $analisisData['timestamp'] }}
                    </time>
                </div>

                {{-- Card Body --}}
                <div class="p-5 sm:p-6">

                    {{-- Ringkasan --}}
                    <p
                        class="text-sm sm:text-base font-semibold text-slate-700 dark:text-slate-200 leading-relaxed mb-5">
                        {{ $analisisData['analisa']['ringkasan'] }}
                    </p>

                    {{-- AI Narrative --}}
                    @if (!empty($analisisData['analisa']['ai_narrative']))
                        <div
                            class="mb-5 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                                    Analisis AI
                                </h4>
                            </div>
                            <p class="text-sm text-slate-700 dark:text-slate-200 leading-relaxed">
                                {{ $analisisData['analisa']['ai_narrative'] }}
                            </p>
                        </div>
                    @endif

                    {{-- Detail Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ([['title' => 'Kondisi', 'content' => $analisisData['analisa']['kondisi']], ['title' => 'Risiko', 'content' => $analisisData['analisa']['resiko']], ['title' => 'Rekomendasi', 'content' => $analisisData['analisa']['rekomendasi']]] as $item)
                            <div
                                class="p-4 rounded-xl bg-white/70 dark:bg-slate-800/60 border border-white dark:border-slate-700 shadow-sm">
                                <h4
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2">
                                    {{ $item['title'] }}
                                </h4>
                                <p
                                    class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                    {{ $item['content'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 mb-5">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-slate-400 dark:text-slate-500" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <p class="text-base sm:text-lg font-semibold text-slate-500 dark:text-slate-400">Belum ada data sensor
                </p>
                <p class="mt-1.5 text-sm text-slate-400 dark:text-slate-500">Sensor belum mengirim data apapun</p>
            </div>
        @endif

        {{-- ============================================================
             SENSOR READINGS
        ============================================================ --}}
        @if (!empty($sensorReadings))
            <div class="mb-10">
                <h3
                    class="flex flex-wrap items-center gap-2 text-lg sm:text-xl font-bold text-slate-800 dark:text-white mb-5">
                    <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                    Data Sensor Terkini
                    <span class="text-xs font-semibold text-green-500 animate-pulse">● Live</span>
                </h3>

                @php
                    $sensors = [
                        [
                            'label' => 'Suhu',
                            'key' => 'suhu',
                            'unit' => '°C',
                            'value_class' => 'text-orange-500 dark:text-orange-400',
                        ],
                        [
                            'label' => 'Kelembapan',
                            'key' => 'kelembapan',
                            'unit' => '%',
                            'value_class' => 'text-cyan-500 dark:text-cyan-400',
                        ],
                        [
                            'label' => 'Tekanan Udara',
                            'key' => 'tekanan_udara',
                            'unit' => 'hPa',
                            'value_class' => 'text-emerald-500 dark:text-emerald-400',
                        ],
                        [
                            'label' => 'Ketinggian Air',
                            'key' => 'ketinggian_air',
                            'unit' => 'cm',
                            'value_class' => 'text-blue-500 dark:text-blue-400',
                        ],
                        [
                            'label' => 'Kecepatan Angin',
                            'key' => 'kecepatan_angin',
                            'unit' => 'm/s',
                            'value_class' => 'text-amber-500 dark:text-amber-400',
                        ],
                        [
                            'label' => 'Arah Angin',
                            'key' => 'arah_angin',
                            'unit' => '°',
                            'value_class' => 'text-violet-500 dark:text-violet-400',
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 sm:gap-4">
                    @foreach ($sensors as $sensor)
                        @php $val = $sensorReadings[$sensor['key']] ?? null; @endphp
                        <div
                            class="flex flex-col items-center justify-center gap-1 p-4 rounded-2xl
                                    bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800
                                    shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 text-center">
                            <span class="text-xs font-medium text-slate-400 dark:text-slate-500 leading-tight">
                                {{ $sensor['label'] }}
                            </span>
                            @if ($val !== null)
                                <span
                                    class="text-2xl font-extrabold tracking-tight leading-none {{ $sensor['value_class'] }}">
                                    {{ $val }}
                                </span>
                                <span class="text-xs text-slate-400 dark:text-slate-500">
                                    {{ $sensor['unit'] }}
                                </span>
                            @else
                                <span
                                    class="text-2xl font-extrabold text-slate-300 dark:text-slate-600 leading-none">—</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ============================================================
             LAST UPDATED
        ============================================================ --}}
        @if (!empty($analisisData['timestamp']))
            <p class="flex items-center justify-center gap-1.5 text-xs text-slate-400 dark:text-slate-500">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Diperbarui {{ $analisisData['timestamp'] }}
            </p>
        @endif

    </div>
</div>
