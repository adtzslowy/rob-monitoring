<!DOCTYPE html>
<html lang="id"
    x-data="{
        dark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && true),
        mobileMenu: false
    }"
    x-init="
        document.documentElement.classList.toggle('dark', dark);
        $watch('dark', val => {
            localStorage.setItem('theme', val ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', val);
        });
    "
    :class="{ 'dark': dark }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ROB Monitoring — Early Warning System Ketapang</title>
    <meta name="description" content="Sistem peringatan dini banjir rob berbasis IoT untuk wilayah pesisir Kabupaten Ketapang, Kalimantan Barat.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles()
    <style>
        [x-cloak] { display: none !important; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-10px); }
        }
        @keyframes wave {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(1.4); }
        }

        .animate-fade-up  { animation: fadeUp 0.7s ease forwards; }
        .animate-float    { animation: float 4s ease-in-out infinite; }
        .animate-wave     { animation: wave 12s linear infinite; }
        .animate-pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }

        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        .opacity-0 { opacity: 0; }
    </style>
</head>

<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white transition-colors duration-300 overflow-x-hidden font-ui">

    {{-- ===== BACKGROUND EFFECTS ===== --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-150px] left-1/2 h-[500px] w-[500px] -translate-x-1/2 rounded-full bg-blue-600/10 dark:bg-blue-600/20 blur-3xl"></div>
        <div class="absolute bottom-[-100px] right-[-100px] h-[400px] w-[400px] rounded-full bg-cyan-500/10 dark:bg-cyan-500/15 blur-3xl"></div>
        <div class="absolute top-1/2 left-[-100px] h-[300px] w-[300px] rounded-full bg-indigo-500/5 dark:bg-indigo-500/10 blur-3xl"></div>
        {{-- Grid pattern --}}
        <div class="absolute inset-0 opacity-[0.02] dark:opacity-[0.04]"
            style="background-image: linear-gradient(#3b82f6 1px, transparent 1px), linear-gradient(to right, #3b82f6 1px, transparent 1px); background-size: 48px 48px;"></div>
    </div>

    {{-- ===== NAVBAR ===== --}}
    <nav class="fixed top-0 inset-x-0 z-50 border-b border-slate-200/80 dark:border-slate-800/80 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md transition-colors duration-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="/" class="flex items-center gap-3 group">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 group-hover:bg-blue-700 transition">
                        <x-heroicon-o-bell-alert class="w-5 h-5"/>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-bold text-slate-900 dark:text-white leading-none">ROB Monitoring</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Early Warning System</p>
                    </div>
                </a>

                {{-- Nav Links Desktop --}}
                <div class="hidden md:flex items-center gap-6">
                    <a href="#tentang" class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">Tentang</a>
                    <a href="#fitur" class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">Fitur</a>
                    <a href="#cara-kerja" class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">Cara Kerja</a>
                    <a href="#status" class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition">Status</a>
                </div>

                {{-- Right --}}
                <div class="flex items-center gap-2 sm:gap-3">
                    {{-- Theme toggle --}}
                    <button @click="dark = !dark"
                        class="p-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        <svg x-show="!dark" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                        <svg x-show="dark" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                    </button>

                    {{-- CTA --}}
                    <a href="{{ route('login') }}"
                        class="hidden sm:inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition shadow-sm">
                        Masuk
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    {{-- Mobile menu button --}}
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400">
                        <svg x-show="!mobileMenu" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg x-show="mobileMenu" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileMenu" x-cloak x-transition
                class="md:hidden border-t border-slate-200 dark:border-slate-800 py-3 space-y-1">
                <a href="#tentang" @click="mobileMenu = false" class="block px-3 py-2 rounded-xl text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">Tentang</a>
                <a href="#fitur" @click="mobileMenu = false" class="block px-3 py-2 rounded-xl text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">Fitur</a>
                <a href="#cara-kerja" @click="mobileMenu = false" class="block px-3 py-2 rounded-xl text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">Cara Kerja</a>
                <a href="#status" @click="mobileMenu = false" class="block px-3 py-2 rounded-xl text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">Status</a>
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30">Masuk ke Dashboard →</a>
            </div>
        </div>
    </nav>

    {{-- ===== HERO ===== --}}
    <section class="relative min-h-screen flex items-center pt-16 pb-20 overflow-hidden">

        {{-- Wave animation --}}
        <div class="absolute bottom-0 inset-x-0 h-20 overflow-hidden opacity-30 dark:opacity-20">
            <div class="animate-wave flex" style="width: 200%;">
                <svg viewBox="0 0 1200 60" class="w-1/2 h-20" preserveAspectRatio="none">
                    <path d="M0,30 C200,60 400,0 600,30 C800,60 1000,0 1200,30 L1200,60 L0,60 Z" fill="#3b82f6"/>
                </svg>
                <svg viewBox="0 0 1200 60" class="w-1/2 h-20" preserveAspectRatio="none">
                    <path d="M0,30 C200,60 400,0 600,30 C800,60 1000,0 1200,30 L1200,60 L0,60 Z" fill="#3b82f6"/>
                </svg>
            </div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">

                {{-- Left --}}
                <div>
                    <div class="opacity-0 animate-fade-up">
                        <span class="inline-flex items-center gap-2 rounded-full border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/30 px-3 py-1 text-xs font-semibold text-blue-700 dark:text-blue-300 mb-6">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-500"></span>
                            </span>
                            Sistem Aktif — Ketapang, Kalimantan Barat
                        </span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight opacity-0 animate-fade-up delay-1">
                        Sistem Peringatan Dini
                        <span class="bg-gradient-to-r from-blue-500 to-cyan-400 bg-clip-text text-transparent block">
                            Banjir ROB
                        </span>
                    </h1>

                    <p class="mt-6 text-lg text-slate-600 dark:text-slate-400 leading-relaxed max-w-lg opacity-0 animate-fade-up delay-2">
                        Pemantauan kondisi air laut dan cuaca secara real-time berbasis IoT dan kecerdasan buatan fuzzy logic untuk wilayah pesisir Kabupaten Ketapang.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3 opacity-0 animate-fade-up delay-3">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 hover:bg-blue-700 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/25 transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Masuk ke Dashboard
                        </a>
                        <a href="{{ route('peta_monitoring') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-6 py-3.5 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                            </svg>
                            Lihat Peta
                        </a>
                    </div>

                    {{-- Stats --}}
                    <div class="mt-10 grid grid-cols-3 gap-3 opacity-0 animate-fade-up delay-4">
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 p-4 text-center backdrop-blur">
                            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">24/7</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 p-4 text-center backdrop-blur">
                            <p class="text-2xl font-extrabold text-slate-900 dark:text-white">6</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Jenis Sensor</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 p-4 text-center backdrop-blur">
                            <p class="text-2xl font-extrabold text-blue-600">IoT</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Berbasis</p>
                        </div>
                    </div>
                </div>

                {{-- Right — Dashboard preview card --}}
                <div class="hidden lg:block opacity-0 animate-fade-up delay-3">
                    <div class="relative animate-float">
                        {{-- Main card --}}
                        <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xl p-6">
                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center gap-2.5">
                                    <span class="relative flex h-2.5 w-2.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                    </span>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">ROB3 — Online</span>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                                    AMAN
                                </span>
                            </div>

                            {{-- Sensor grid --}}
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="rounded-2xl bg-sky-50 dark:bg-sky-500/10 border border-sky-100 dark:border-sky-500/20 p-3">
                                    <p class="text-[10px] font-semibold text-sky-600 dark:text-sky-400 uppercase tracking-wide">Ketinggian</p>
                                    <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">111 <span class="text-xs font-normal text-slate-400">cm</span></p>
                                </div>
                                <div class="rounded-2xl bg-orange-50 dark:bg-orange-500/10 border border-orange-100 dark:border-orange-500/20 p-3">
                                    <p class="text-[10px] font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wide">Suhu</p>
                                    <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">29.8 <span class="text-xs font-normal text-slate-400">°C</span></p>
                                </div>
                                <div class="rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 p-3">
                                    <p class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wide">Angin</p>
                                    <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">10.3 <span class="text-xs font-normal text-slate-400">m/s</span></p>
                                </div>
                                <div class="rounded-2xl bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-100 dark:border-cyan-500/20 p-3">
                                    <p class="text-[10px] font-semibold text-cyan-600 dark:text-cyan-400 uppercase tracking-wide">Kelembapan</p>
                                    <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">77 <span class="text-xs font-normal text-slate-400">%</span></p>
                                </div>
                                <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 p-3">
                                    <p class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Tekanan</p>
                                    <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">1011 <span class="text-xs font-normal text-slate-400">hPa</span></p>
                                </div>
                                <div class="rounded-2xl bg-purple-50 dark:bg-purple-500/10 border border-purple-100 dark:border-purple-500/20 p-3">
                                    <p class="text-[10px] font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wide">Arah Angin</p>
                                    <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">70 <span class="text-xs font-normal text-slate-400">°</span></p>
                                </div>
                            </div>

                            {{-- Mini chart --}}
                            <div class="rounded-2xl bg-slate-50 dark:bg-slate-800 p-3">
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2">Trend Ketinggian Air</p>
                                <svg viewBox="0 0 240 48" class="w-full h-12">
                                    <defs>
                                        <linearGradient id="heroChartGrad" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.3"/>
                                            <stop offset="100%" stop-color="#38bdf8" stop-opacity="0"/>
                                        </linearGradient>
                                    </defs>
                                    <polyline points="0,38 24,34 48,36 72,28 96,30 120,24 144,26 168,18 192,20 216,15 240,12"
                                        fill="none" stroke="#38bdf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <polyline points="0,38 24,34 48,36 72,28 96,30 120,24 144,26 168,18 192,20 216,15 240,12 240,48 0,48"
                                        fill="url(#heroChartGrad)" stroke="none"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Floating badges --}}
                        <div class="absolute -top-4 -right-6 rounded-2xl border border-blue-200 dark:border-blue-800 bg-white dark:bg-slate-900 shadow-lg px-3 py-2 animate-float delay-2">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Notifikasi Aktif</span>
                            </div>
                        </div>
                        <div class="absolute -bottom-4 -left-6 rounded-2xl border border-purple-200 dark:border-purple-800 bg-white dark:bg-slate-900 shadow-lg px-3 py-2 animate-float delay-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">Fuzzy Logic AI</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ===== TENTANG ===== --}}
    <section id="tentang" class="py-20 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400 mb-4">
                    Tentang Sistem
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">
                    Apa itu ROB Monitoring?
                </h2>
                <p class="mt-4 text-slate-600 dark:text-slate-400 leading-relaxed">
                    ROB Monitoring adalah sistem peringatan dini banjir rob berbasis IoT yang memantau kondisi air laut dan cuaca secara real-time di wilayah pesisir Kabupaten Ketapang, Kalimantan Barat. Sistem ini menggunakan algoritma fuzzy logic untuk menganalisis data multi-sensor secara cerdas.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="group rounded-3xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 p-6 hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 dark:bg-blue-900/40 mb-4 group-hover:bg-blue-200 dark:group-hover:bg-blue-900/60 transition">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Pemantauan Real-time</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">Data sensor dikirim setiap detik dan ditampilkan langsung di dashboard dengan grafik tren yang akurat dan terus diperbarui.</p>
                </div>

                <div class="group rounded-3xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 p-6 hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-100 dark:bg-purple-900/40 mb-4 group-hover:bg-purple-200 dark:group-hover:bg-purple-900/60 transition">
                        <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Fuzzy Logic AI</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">Menggunakan metode fuzzy logic untuk menentukan tingkat risiko banjir rob berdasarkan kombinasi data multi-sensor secara cerdas.</p>
                </div>

                <div class="group rounded-3xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 p-6 hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-100 dark:bg-green-900/40 mb-4 group-hover:bg-green-200 dark:group-hover:bg-green-900/60 transition">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Notifikasi Telegram</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">Peringatan otomatis dikirim ke Telegram saat status berubah ke WASPADA, SIAGA, atau BAHAYA — meski tidak sedang membuka aplikasi.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== FITUR ===== --}}
    <section id="fitur" class="py-20 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400 mb-4">
                    Fitur Unggulan
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">
                    Lengkap untuk Pemantauan Pesisir
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $features = [
                        ['icon' => 'chart-bar', 'title' => 'Dashboard Real-time', 'desc' => 'Tampilan data sensor langsung dengan grafik tren dan status risiko terkini yang diperbarui setiap detik.', 'color' => 'blue'],
                        ['icon' => 'map', 'title' => 'Peta Monitoring', 'desc' => 'Visualisasi lokasi alat sensor di peta interaktif Windy dengan lapisan cuaca dan status online/offline.', 'color' => 'cyan'],
                        ['icon' => 'bell', 'title' => 'Alert Digest', 'desc' => 'Notifikasi Telegram otomatis dalam format digest — satu pesan untuk semua device yang berubah status.', 'color' => 'green'],
                        ['icon' => 'cpu', 'title' => 'Fuzzy Logic', 'desc' => 'Penentuan status risiko menggunakan logika fuzzy Sugeno dengan input 4 sensor secara bersamaan.', 'color' => 'purple'],
                        ['icon' => 'shield', 'title' => 'Multi-level Access', 'desc' => 'Manajemen pengguna dengan role Admin dan Operator. Admin kelola semua device, operator hanya device miliknya.', 'color' => 'orange'],
                        ['icon' => 'clock', 'title' => 'Riwayat Data', 'desc' => 'Akses data historis sensor dari 1 menit hingga 1 tahun ke belakang dengan grafik yang bisa di-drill down.', 'color' => 'rose'],
                    ];
                @endphp

                @foreach ($features as $f)
                    @php
                        $colors = [
                            'blue'   => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                            'cyan'   => 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400',
                            'green'  => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                            'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
                            'orange' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
                            'rose'   => 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400',
                        ];
                    @endphp
                    <div class="group rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 hover:-translate-y-1 transition-transform duration-300">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $colors[$f['color']] }} mb-3">
                            @if ($f['icon'] === 'chart-bar')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                            @elseif ($f['icon'] === 'map')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" /></svg>
                            @elseif ($f['icon'] === 'bell')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                            @elseif ($f['icon'] === 'cpu')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm.75-12h9v9h-9v-9z" /></svg>
                            @elseif ($f['icon'] === 'shield')
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                            @else
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            @endif
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white mb-1">{{ $f['title'] }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== CARA KERJA ===== --}}
    <section id="cara-kerja" class="py-20 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400 mb-4">
                    Cara Kerja
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">
                    Bagaimana Sistem Bekerja?
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 relative">
                {{-- Connector --}}
                <div class="hidden lg:block absolute top-8 left-[15%] right-[15%] h-px bg-gradient-to-r from-blue-200 via-purple-300 to-blue-200 dark:from-blue-900 dark:via-purple-800 dark:to-blue-900"></div>

                @php
                    $steps = [
                        ['no' => '01', 'title' => 'Sensor IoT', 'desc' => 'Alat sensor mengukur ketinggian air, suhu, angin, tekanan, dan kelembapan secara real-time di lapangan.', 'color' => 'from-blue-500 to-blue-600'],
                        ['no' => '02', 'title' => 'Kirim Data', 'desc' => 'Data dikirim ke server melalui API IoT Kabupaten Ketapang setiap beberapa detik sekali.', 'color' => 'from-cyan-500 to-cyan-600'],
                        ['no' => '03', 'title' => 'Fuzzy Logic', 'desc' => 'Server menganalisis data dengan algoritma fuzzy logic untuk menentukan tingkat risiko secara akurat.', 'color' => 'from-purple-500 to-purple-600'],
                        ['no' => '04', 'title' => 'Alert & Notif', 'desc' => 'Status ditampilkan di dashboard dan notifikasi dikirim ke Telegram jika kondisi berbahaya.', 'color' => 'from-green-500 to-green-600'],
                    ];
                @endphp

                @foreach ($steps as $step)
                    <div class="text-center relative">
                        <div class="flex justify-center mb-4">
                            <div class="relative flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br {{ $step['color'] }} shadow-lg">
                                <span class="text-2xl font-extrabold text-white">{{ $step['no'] }}</span>
                            </div>
                        </div>
                        <h3 class="font-bold text-slate-900 dark:text-white mb-2">{{ $step['title'] }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===== STATUS LEVEL ===== --}}
    <section id="status" class="py-20 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400 mb-4">
                    Tingkat Status
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">
                    4 Tingkat Status Risiko
                </h2>
                <p class="mt-4 text-slate-600 dark:text-slate-400">Sistem mengklasifikasikan kondisi menjadi 4 tingkat status berdasarkan skor defuzzifikasi fuzzy logic.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="group rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 p-6 text-center hover:-translate-y-1 transition-transform duration-300">
                    <div class="text-4xl mb-3">🟢</div>
                    <h3 class="text-xl font-extrabold text-emerald-700 dark:text-emerald-400 mb-2">AMAN</h3>
                    <p class="text-xs text-emerald-600/80 dark:text-emerald-400/70 leading-relaxed">Kondisi normal, tidak ada ancaman banjir rob yang signifikan. Skor fuzzy di bawah 40.</p>
                </div>
                <div class="group rounded-2xl border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-6 text-center hover:-translate-y-1 transition-transform duration-300">
                    <div class="text-4xl mb-3">🟡</div>
                    <h3 class="text-xl font-extrabold text-amber-700 dark:text-amber-400 mb-2">WASPADA</h3>
                    <p class="text-xs text-amber-600/80 dark:text-amber-400/70 leading-relaxed">Kondisi mulai meningkat, perlu perhatian dan pemantauan lebih ketat. Skor 40–64.</p>
                </div>
                <div class="group rounded-2xl border border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20 p-6 text-center hover:-translate-y-1 transition-transform duration-300">
                    <div class="text-4xl mb-3">🟠</div>
                    <h3 class="text-xl font-extrabold text-orange-700 dark:text-orange-400 mb-2">SIAGA</h3>
                    <p class="text-xs text-orange-600/80 dark:text-orange-400/70 leading-relaxed">Kondisi berbahaya, bersiap untuk tindakan evakuasi jika diperlukan. Skor 65–84.</p>
                </div>
                <div class="group rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-6 text-center hover:-translate-y-1 transition-transform duration-300">
                    <div class="text-4xl mb-3">🔴</div>
                    <h3 class="text-xl font-extrabold text-red-700 dark:text-red-400 mb-2">BAHAYA</h3>
                    <p class="text-xs text-red-600/80 dark:text-red-400/70 leading-relaxed">Kondisi kritis, banjir rob diprediksi terjadi. Segera lakukan evakuasi. Skor ≥ 85.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== CTA ===== --}}
    <section class="py-20 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-cyan-600 p-8 sm:p-12 lg:p-16 text-center">
                <div class="absolute inset-0 opacity-10"
                    style="background-image: linear-gradient(white 1px, transparent 1px), linear-gradient(to right, white 1px, transparent 1px); background-size: 32px 32px;"></div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-black/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                <div class="relative">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                        Siap Memantau Kondisi Pesisir?
                    </h2>
                    <p class="text-blue-100 mb-8 max-w-2xl mx-auto text-lg">
                        Masuk ke dashboard untuk melihat data sensor real-time dan status risiko banjir rob terkini di wilayah Ketapang.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white hover:bg-blue-50 px-8 py-4 text-sm font-bold text-blue-600 shadow-lg transition">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Masuk ke Dashboard
                        </a>
                        <a href="{{ route('peta_monitoring') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/30 bg-white/10 hover:bg-white/20 px-8 py-4 text-sm font-bold text-white transition backdrop-blur">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                            </svg>
                            Lihat Peta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== FOOTER ===== --}}
    <footer class="bg-slate-900 dark:bg-slate-950 border-t border-slate-800 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white leading-none">ROB Monitoring</p>
                            <p class="text-xs text-slate-400">Early Warning System</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">Sistem peringatan dini banjir rob berbasis IoT dan kecerdasan buatan untuk wilayah pesisir Ketapang, Kalimantan Barat.</p>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white mb-4">Navigasi</h4>
                    <ul class="space-y-2">
                        <li><a href="#tentang" class="text-sm text-slate-400 hover:text-white transition">Tentang Sistem</a></li>
                        <li><a href="#fitur" class="text-sm text-slate-400 hover:text-white transition">Fitur Unggulan</a></li>
                        <li><a href="#cara-kerja" class="text-sm text-slate-400 hover:text-white transition">Cara Kerja</a></li>
                        <li><a href="#status" class="text-sm text-slate-400 hover:text-white transition">Tingkat Status</a></li>
                        <li><a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-white transition">Masuk Dashboard</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-white mb-4">Wilayah Pemantauan</h4>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Kabupaten Ketapang<br>
                        Kalimantan Barat, Indonesia<br>
                        <span class="text-xs text-slate-500 mt-1 block">Koordinat: -1.8367°, 110.0167°</span>
                    </p>
                    <p class="text-sm text-slate-400 mt-3 leading-relaxed">Data bersumber dari sensor IoT yang terpasang di titik-titik strategis wilayah pesisir.</p>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-slate-500">© {{ date('Y') }} ROB Monitoring — Early Warning System. Built with Laravel • Livewire • Alpine.js • Tailwind CSS</p>
                <p class="text-xs text-slate-500">Kabupaten Ketapang, Kalimantan Barat</p>
            </div>
        </div>
    </footer>

    @livewireScripts()
</body>
</html>