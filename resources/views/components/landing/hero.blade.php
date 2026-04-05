<section class="relative min-h-screen flex items-center pt-16 pb-16 sm:pb-20 lg:pb-24 overflow-hidden">

    {{-- Decorative wave bottom --}}
    <div class="absolute bottom-0 inset-x-0 h-16 sm:h-20 lg:h-24 overflow-hidden opacity-20 dark:opacity-10 pointer-events-none">
        <div class="animate-wave flex" style="width: 200%;">
            <svg viewBox="0 0 1200 60" class="w-1/2 h-16 sm:h-20 lg:h-24" preserveAspectRatio="none">
                <path d="M0,30 C200,60 400,0 600,30 C800,60 1000,0 1200,30 L1200,60 L0,60 Z" fill="#3b82f6"/>
            </svg>
            <svg viewBox="0 0 1200 60" class="w-1/2 h-16 sm:h-20 lg:h-24" preserveAspectRatio="none">
                <path d="M0,30 C200,60 400,0 600,30 C800,60 1000,0 1200,30 L1200,60 L0,60 Z" fill="#3b82f6"/>
            </svg>
        </div>
    </div>

    <div class="relative mx-auto max-w-7xl px-3 sm:px-4 lg:px-8 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 xl:gap-20 items-center">

            {{-- ===== LEFT ===== --}}
            <div class="flex flex-col">

                {{-- Badge --}}
                <div class="opacity-0 animate-fade-up">
                    <span class="inline-flex items-center gap-1.5 sm:gap-2 rounded-full border border-blue-200/70 dark:border-blue-700/50 bg-blue-50 dark:bg-blue-950/60 px-3 py-1 sm:py-1.5 text-[10px] sm:text-xs font-semibold text-blue-700 dark:text-blue-300 mb-6 sm:mb-8 shadow-sm">
                        <span class="relative flex h-1.5 sm:h-2 w-1.5 sm:w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                            <span class="relative inline-flex h-1.5 sm:h-2 w-1.5 sm:w-2 rounded-full bg-blue-500"></span>
                        </span>
                        <span class="hidden xs:inline">Sistem Aktif — Ketapang, Kalimantan Barat</span>
                        <span class="xs:hidden">Sistem Aktif</span>
                    </span>
                </div>

                {{-- Headline --}}
                <h1 class="text-[1.75rem] sm:text-3xl lg:text-[2.5rem] xl:text-[3.5rem] font-extrabold leading-[1.15] sm:leading-[1.1] tracking-tight opacity-0 animate-fade-up delay-1">
                    <span class="text-slate-900 dark:text-white">Sistem Peringatan</span>
                    <span class="block mt-0.5 sm:mt-1 bg-gradient-to-r from-blue-500 via-blue-400 to-cyan-400 bg-clip-text text-transparent">
                        Dini Banjir ROB
                    </span>
                </h1>

                {{-- Desc --}}
                <p class="mt-4 sm:mt-6 text-sm sm:text-base text-slate-500 dark:text-slate-400 leading-relaxed max-w-lg opacity-0 animate-fade-up delay-2">
                    Pemantauan kondisi air laut dan cuaca secara <span class="text-slate-700 dark:text-slate-300 font-medium">real-time</span> berbasis IoT dan fuzzy logic untuk wilayah pesisir Kabupaten Ketapang.
                </p>

                {{-- CTA --}}
                <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row gap-2.5 sm:gap-3 opacity-0 animate-fade-up delay-3">
                    <a href="{{ route('login') }}"
                        class="group inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 px-5 py-3 sm:px-6 sm:py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/30 hover:shadow-blue-600/40 transition-all duration-200">
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        <span class="hidden sm:inline">Masuk ke Dashboard</span>
                        <span class="sm:hidden">Dashboard</span>
                    </a>
                    <a href="{{ route('peta') }}"
                        class="group inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 dark:border-slate-700/80 bg-white/80 dark:bg-slate-900/80 px-5 py-3 sm:px-6 sm:py-3.5 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-white dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 backdrop-blur transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                        </svg>
                        <span class="hidden sm:inline">Lihat Peta</span>
                        <span class="sm:hidden">Peta</span>
                    </a>
                </div>

                {{-- Stats --}}
                <div class="mt-8 sm:mt-10 grid grid-cols-3 gap-2 sm:gap-3 opacity-0 animate-fade-up delay-4">
                    <div class="group rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 p-3 sm:p-4 text-center backdrop-blur hover:border-blue-200 dark:hover:border-blue-800/60 hover:shadow-sm transition-all duration-200">
                        <p class="text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white">24/7</p>
                        <p class="text-[9px] sm:text-[11px] font-medium text-slate-400 dark:text-slate-500 mt-0.5 sm:mt-1 uppercase tracking-wide">Pemantauan</p>
                    </div>
                    <div class="group rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 p-3 sm:p-4 text-center backdrop-blur hover:border-blue-200 dark:hover:border-blue-800/60 hover:shadow-sm transition-all duration-200">
                        <p class="text-lg sm:text-2xl font-extrabold text-slate-900 dark:text-white">6</p>
                        <p class="text-[9px] sm:text-[11px] font-medium text-slate-400 dark:text-slate-500 mt-0.5 sm:mt-1 uppercase tracking-wide">Sensor</p>
                    </div>
                    <div class="group rounded-xl sm:rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 p-3 sm:p-4 text-center backdrop-blur hover:border-blue-200 dark:hover:border-blue-800/60 hover:shadow-sm transition-all duration-200">
                        <p class="text-lg sm:text-2xl font-extrabold bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">IoT</p>
                        <p class="text-[9px] sm:text-[11px] font-medium text-slate-400 dark:text-slate-500 mt-0.5 sm:mt-1 uppercase tracking-wide">Berbasis</p>
                    </div>
                </div>

            </div>

            {{-- ===== RIGHT — Dashboard Preview ===== --}}
            <div class="hidden lg:block opacity-0 animate-fade-up delay-3">
                <div class="relative animate-float">

                    {{-- Glow behind card --}}
                    <div class="absolute inset-0 -z-10 scale-95 rounded-3xl bg-blue-500/10 dark:bg-blue-500/5 blur-2xl"></div>

                    {{-- Main card --}}
                    <div class="rounded-3xl border border-slate-200/80 dark:border-slate-700/60 bg-white/90 dark:bg-slate-900/90 shadow-2xl shadow-slate-200/50 dark:shadow-black/30 p-5 sm:p-6 backdrop-blur">

                        {{-- Card header --}}
                        <div class="flex items-center justify-between mb-4 sm:mb-5">
                            <div class="flex items-center gap-2 sm:gap-2.5">
                                <span class="relative flex h-2 sm:h-2.5 w-2 sm:w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                                    <span class="relative inline-flex h-2 sm:h-2.5 w-2 sm:w-2.5 rounded-full bg-emerald-500"></span>
                                </span>
                                <span class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200">ROB3 — Online</span>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full sm:gap-1.5 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/60 px-2 py-0.5 sm:px-3 sm:py-1 text-[10px] sm:text-xs font-bold text-emerald-700 dark:text-emerald-400">
                                <span class="h-1 sm:h-1.5 w-1 sm:w-1.5 rounded-full bg-emerald-500"></span>
                                AMAN
                            </span>
                        </div>

                        {{-- Sensor grid --}}
                        <div class="grid grid-cols-3 gap-2 sm:gap-2.5 mb-3 sm:mb-4">
                            @foreach ([
                                ['label' => 'Ketinggian', 'value' => '111',  'unit' => 'cm',  'color' => 'sky'],
                                ['label' => 'Suhu',       'value' => '29.8', 'unit' => '°C',  'color' => 'orange'],
                                ['label' => 'Angin',      'value' => '10.3', 'unit' => 'm/s', 'color' => 'amber'],
                                ['label' => 'Kelembapan', 'value' => '77',   'unit' => '%',   'color' => 'cyan'],
                                ['label' => 'Tekanan',    'value' => '1011', 'unit' => 'hPa', 'color' => 'emerald'],
                                ['label' => 'Arah Angin', 'value' => '70',   'unit' => '°',   'color' => 'purple'],
                            ] as $s)
                                <div class="rounded-xl sm:rounded-2xl bg-{{ $s['color'] }}-50 dark:bg-{{ $s['color'] }}-500/10 border border-{{ $s['color'] }}-100/80 dark:border-{{ $s['color'] }}-500/20 p-2 sm:p-3">
                                    <p class="text-[8px] sm:text-[9px] font-bold text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400 uppercase tracking-wider">{{ $s['label'] }}</p>
                                    <p class="text-base sm:text-xl font-extrabold text-slate-900 dark:text-white mt-1 sm:mt-1.5 leading-none">
                                        {{ $s['value'] }}
                                        <span class="text-[8px] sm:text-[10px] font-normal text-slate-400 ml-0.5">{{ $s['unit'] }}</span>
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Mini chart --}}
                        <div class="rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/40 p-2 sm:p-3">
                            <div class="flex items-center justify-between mb-1.5 sm:mb-2">
                                <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 dark:text-slate-400">Trend Ketinggian Air</p>
                                <span class="text-[9px] sm:text-[10px] text-emerald-500 font-semibold">↑ Naik</span>
                            </div>
                            <svg viewBox="0 0 240 48" class="w-full h-10 sm:h-12">
                                <defs>
                                    <linearGradient id="heroChartGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.35"/>
                                        <stop offset="100%" stop-color="#38bdf8" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                                <polyline
                                    points="0,38 24,34 48,36 72,28 96,30 120,24 144,26 168,18 192,20 216,15 240,12"
                                    fill="none" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <polyline
                                    points="0,38 24,34 48,36 72,28 96,30 120,24 144,26 168,18 192,20 216,15 240,12 240,48 0,48"
                                    fill="url(#heroChartGrad)" stroke="none"/>
                                <circle cx="240" cy="12" r="3" fill="#38bdf8"/>
                                <circle cx="240" cy="12" r="6" fill="#38bdf8" fill-opacity="0.2"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Floating badge — Telegram --}}
                    <div class="absolute -top-4 sm:-top-5 -right-4 sm:-right-5 rounded-xl sm:rounded-2xl border border-blue-200/80 dark:border-blue-800/60 bg-white dark:bg-slate-900 shadow-xl shadow-blue-100/50 dark:shadow-black/30 px-3 py-2 sm:px-3.5 sm:py-2.5 animate-float delay-2">
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <div class="flex h-6 sm:h-7 w-6 sm:w-7 items-center justify-center rounded-xl bg-blue-500/10">
                                <svg class="h-3.5 sm:h-4 w-3.5 sm:w-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] sm:text-xs font-bold text-slate-700 dark:text-slate-200 leading-none">Notifikasi Aktif</p>
                                <p class="text-[9px] sm:text-[10px] text-slate-400 mt-0.5">via Telegram</p>
                            </div>
                        </div>
                    </div>

                    {{-- Floating badge — AI --}}
                    <div class="absolute -bottom-4 sm:-bottom-5 -left-4 sm:-left-5 rounded-xl sm:rounded-2xl border border-purple-200/80 dark:border-purple-800/60 bg-white dark:bg-slate-900 shadow-xl shadow-purple-100/50 dark:shadow-black/30 px-3 py-2 sm:px-3.5 sm:py-2.5 animate-float delay-4">
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <div class="flex h-6 sm:h-7 w-6 sm:w-7 items-center justify-center rounded-xl bg-purple-500/10">
                                <svg class="h-3.5 sm:h-4 w-3.5 sm:w-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] sm:text-xs font-bold text-slate-700 dark:text-slate-200 leading-none">Fuzzy Logic AI</p>
                                <p class="text-[9px] sm:text-[10px] text-slate-400 mt-0.5">Risk Analysis</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>