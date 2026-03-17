<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Beranda - ROB Monitoring</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles()
</head>
<body class="bg-slate-950 text-white font-ui overflow-x-hidden">

    <!-- 🌌 BACKGROUND EFFECT -->
    <div class="absolute inset-0 -z-10">
        <div class="absolute top-[-100px] left-1/2 h-[400px] w-[400px] -translate-x-1/2 rounded-full bg-blue-600/20 blur-3xl"></div>
        <div class="absolute bottom-[-100px] right-[-100px] h-[300px] w-[300px] rounded-full bg-cyan-500/10 blur-3xl"></div>
    </div>

    <!-- 🚀 HERO -->
    <section class="px-6 py-20 text-center">
        <div class="mx-auto max-w-3xl">

            <h1 class="text-4xl font-bold tracking-tight sm:text-6xl leading-tight">
                ROB Monitoring
                <span class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
                    System
                </span>
            </h1>

            <p class="mt-6 text-slate-400 text-lg">
                Sistem monitoring berbasis IoT untuk mendeteksi kondisi lingkungan
                dan potensi banjir rob secara real-time dengan visualisasi interaktif.
            </p>

            <!-- BUTTON -->
            <div class="mt-8 flex justify-center gap-4">
                <a href="{{ route('peta') }}"
                    class="group inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-medium shadow-lg shadow-blue-600/20 transition-all duration-300 hover:bg-blue-500 hover:scale-105">
                    
                    <x-heroicon-o-map class="w-5 h-5 transition-transform group-hover:rotate-6"/>
                    Lihat Peta
                </a>

                <a href="#about"
                    class="rounded-xl border border-slate-700 px-6 py-3 text-sm hover:bg-slate-800 transition">
                    Pelajari Sistem
                </a>
            </div>

        </div>
    </section>

    <!-- 🧠 ABOUT -->
    <section id="about" class="px-6 py-16 border-t border-slate-800">
        <div class="mx-auto max-w-5xl text-center">

            <h2 class="text-2xl font-semibold">Tentang Sistem</h2>

            <p class="mt-4 text-slate-400 max-w-2xl mx-auto">
                ROB Monitoring System dirancang untuk memberikan informasi kondisi lingkungan
                secara real-time melalui sensor IoT. Sistem ini membantu pengguna memahami
                perubahan lingkungan dan mengambil keputusan lebih cepat.
            </p>

            <!-- FEATURES -->
            <div class="mt-10 grid gap-6 sm:grid-cols-2 md:grid-cols-3">

                <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-6 backdrop-blur">
                    <h3 class="font-medium">Real-time Data</h3>
                    <p class="mt-2 text-sm text-slate-400">
                        Monitoring data sensor secara langsung tanpa delay.
                    </p>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-6 backdrop-blur">
                    <h3 class="font-medium">Visual Interaktif</h3>
                    <p class="mt-2 text-sm text-slate-400">
                        Data ditampilkan melalui peta dan grafik yang mudah dipahami.
                    </p>
                </div>

                <div class="rounded-xl border border-slate-800 bg-slate-900/50 p-6 backdrop-blur">
                    <h3 class="font-medium">Early Warning</h3>
                    <p class="mt-2 text-sm text-slate-400">
                        Memberikan peringatan dini terhadap potensi bahaya lingkungan.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- ⚡ CTA -->
    <section class="px-6 py-16 text-center border-t border-slate-800">
        <h2 class="text-2xl font-semibold">
            Mulai Monitoring Sekarang
        </h2>

        <p class="mt-3 text-slate-400">
            Akses peta dan data sensor secara langsung.
        </p>

        <a href="{{ route('peta') }}"
            class="mt-6 inline-block rounded-xl bg-cyan-500 px-6 py-3 text-sm font-medium shadow-lg shadow-cyan-500/20 hover:bg-cyan-400 transition">
            Buka Dashboard
        </a>
    </section>

    <!-- ⚓ FOOTER -->
    <footer class="border-t border-slate-800 px-6 py-6 text-center text-sm text-slate-500">
        <p>© 2026 ROB Monitoring System</p>
        <p class="mt-1">Built with Laravel • Livewire • Tailwind</p>
    </footer>

    @livewireScripts()
</body>
</html>