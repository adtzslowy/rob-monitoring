<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title>ROB Monitoring - Early Warning System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">

    {{-- NAVBAR --}}
    <header class="w-full fixed top-0 left-0 z-50 backdrop-blur-lg bg-white/5 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-lg font-bold tracking-wide">
                🌊 ROB Monitoring
            </h1>

            <div class="flex items-center gap-4">
                <a href="#features" class="text-sm hover:text-blue-400 transition">Fitur</a>
                <a href="#about" class="text-sm hover:text-blue-400 transition">Tentang</a>

                <a href="{{ route('login') }}"
                   class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 transition text-sm font-medium shadow-lg">
                    Login
                </a>
            </div>
        </div>
    </header>

    {{-- HERO --}}
    <section class="min-h-screen flex items-center justify-center text-center px-6">
        <div class="max-w-3xl space-y-6">

            <h2 class="text-4xl md:text-6xl font-extrabold leading-tight">
                Sistem Monitoring
                <span class="text-blue-400">Ketinggian Air & Cuaca</span>
                Secara Realtime
            </h2>

            <p class="text-gray-300 text-lg">
                Early Warning System untuk memantau kondisi air, suhu, tekanan udara,
                serta mendeteksi potensi risiko banjir rob secara akurat dan cepat.
            </p>

            <div class="flex justify-center gap-4 pt-4">
                <a href="{{ route('login') }}"
                   class="px-6 py-3 bg-blue-600 hover:bg-blue-500 rounded-xl font-semibold shadow-xl transition">
                    Masuk Dashboard
                </a>

                <a href="#features"
                   class="px-6 py-3 border border-white/20 hover:border-white/40 rounded-xl transition">
                    Pelajari Lebih Lanjut
                </a>
            </div>

        </div>
    </section>

    {{-- FEATURES --}}
    <section id="features" class="py-24 bg-slate-950/40 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6">

            <h3 class="text-3xl font-bold text-center mb-16">
                Fitur Utama
            </h3>

            <div class="grid md:grid-cols-3 gap-8">

                {{-- Feature 1 --}}
                <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:border-blue-500/40 transition shadow-lg">
                    <div class="text-4xl mb-4">📡</div>
                    <h4 class="text-xl font-semibold mb-2">Monitoring Realtime</h4>
                    <p class="text-gray-400 text-sm">
                        Data sensor diperbarui setiap beberapa detik untuk memastikan kondisi terkini.
                    </p>
                </div>

                {{-- Feature 2 --}}
                <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:border-blue-500/40 transition shadow-lg">
                    <div class="text-4xl mb-4">📊</div>
                    <h4 class="text-xl font-semibold mb-2">Trend & Analisis</h4>
                    <p class="text-gray-400 text-sm">
                        Visualisasi grafik untuk membantu analisis perubahan kondisi lingkungan.
                    </p>
                </div>

                {{-- Feature 3 --}}
                <div class="p-8 rounded-2xl bg-white/5 border border-white/10 hover:border-blue-500/40 transition shadow-lg">
                    <div class="text-4xl mb-4">⚠️</div>
                    <h4 class="text-xl font-semibold mb-2">Sistem Peringatan</h4>
                    <p class="text-gray-400 text-sm">
                        Status risiko otomatis (AMAN, WASPADA, SIAGA, BAHAYA).
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ABOUT --}}
    <section id="about" class="py-24">
        <div class="max-w-4xl mx-auto px-6 text-center space-y-6">
            <h3 class="text-3xl font-bold">
                Tentang Sistem
            </h3>

            <p class="text-gray-300 leading-relaxed">
                ROB Monitoring adalah sistem pemantauan berbasis IoT yang dirancang
                untuk membantu mitigasi risiko banjir rob dengan memanfaatkan sensor
                lingkungan seperti ketinggian air, suhu, tekanan udara, dan angin.
            </p>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-20 bg-gradient-to-r from-blue-600 to-indigo-600 text-center">
        <div class="max-w-3xl mx-auto px-6 space-y-6">
            <h3 class="text-3xl font-bold">
                Siap Mengelola Monitoring Anda?
            </h3>

            <p class="text-white/90">
                Masuk ke dashboard untuk melihat data realtime dan mengelola perangkat Anda.
            </p>

            <a href="{{ route('login') }}"
               class="inline-block px-8 py-3 bg-white text-blue-600 font-semibold rounded-xl shadow-lg hover:scale-105 transition">
                Login Sekarang
            </a>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="py-8 text-center text-sm text-gray-400 border-t border-white/10">
        © {{ date('Y') }} ROB Monitoring — Early Warning System
    </footer>

</body>
</html>
