da<!DOCTYPE html>
<html lang="id"
    x-data="{ dark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && true) }"
    x-init="document.documentElement.classList.toggle('dark', dark)"
    :class="{ 'dark': dark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | ROB Monitoring</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">
    <script>
        (function(){
            const t = localStorage.getItem('theme');
            document.documentElement.classList.toggle('dark', t === 'dark' || (!t && true));
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes float-up {
            0%   { transform: translateY(0) scale(1); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 0.5; }
            100% { transform: translateY(-105vh) scale(0.3); opacity: 0; }
        }
        @keyframes sway {
            0%, 100% { margin-left: 0px; }
            33%       { margin-left: 40px; }
            66%       { margin-left: -40px; }
        }
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .particle { position: absolute; bottom: -20px; border-radius: 50%; pointer-events: none; animation: float-up linear infinite, sway ease-in-out infinite; }
        .afu { animation: fade-up 0.6s ease both; }
        .d1 { animation-delay: 0.1s; }
        .d2 { animation-delay: 0.2s; }
        .d3 { animation-delay: 0.3s; }
        .d4 { animation-delay: 0.45s; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 font-ui text-slate-900 dark:text-white overflow-hidden">

    {{-- Background blobs --}}
    <div class="fixed inset-0 -z-10 pointer-events-none">
        <div class="absolute -top-40 left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-blue-600/10 dark:bg-blue-600/20 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 h-[400px] w-[400px] rounded-full bg-cyan-500/10 dark:bg-cyan-500/15 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.015] dark:opacity-[0.035]"
             style="background-image:linear-gradient(#3b82f6 1px,transparent 1px),linear-gradient(to right,#3b82f6 1px,transparent 1px);background-size:48px 48px;"></div>
    </div>

    {{-- Particles --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none" id="particles-404"></div>

    <div class="flex min-h-screen flex-col items-center justify-center px-4 text-center">

        {{-- Big number --}}
        <div class="relative mb-6 select-none afu">
            <span class="text-[9rem] sm:text-[12rem] font-extrabold leading-none tracking-tighter text-slate-200 dark:text-slate-800">
                404
            </span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-500/10 dark:bg-blue-500/20 border border-blue-500/20 text-blue-500 dark:text-blue-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                    </svg>
                </div>
            </div>
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold mb-3 afu d1">Halaman Tidak Ditemukan</h1>
        <p class="max-w-md text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-8 afu d2">
            Halaman yang kamu cari tidak ada atau sudah dipindahkan.
            Pastikan URL yang kamu masukkan sudah benar.
        </p>

        <div class="flex flex-col sm:flex-row items-center gap-3 afu d3">
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-blue-500 hover:bg-blue-400 px-5 py-2.5 text-sm font-semibold text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                </svg>
                Kembali ke Beranda
            </a>
            <button onclick="history.back()"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
                </svg>
                Kembali
            </button>
        </div>

        <p class="mt-12 text-xs text-slate-400 dark:text-slate-600 afu d4">
            © {{ date('Y') }} ROB Monitoring System
        </p>
    </div>

    <script>
        (function () {
            const container = document.getElementById('particles-404');
            const isDark = document.documentElement.classList.contains('dark');
            const colors = isDark
                ? ['rgba(59,130,246,0.4)','rgba(96,165,250,0.3)','rgba(34,211,238,0.25)','rgba(147,197,253,0.2)']
                : ['rgba(59,130,246,0.15)','rgba(96,165,250,0.12)','rgba(34,211,238,0.1)','rgba(147,197,253,0.1)'];

            function spawn() {
                const el   = document.createElement('div');
                const size = Math.random() * 12 + 4;
                const dur  = Math.random() * 14 + 9;
                const sway = Math.random() * 7 + 5;
                const del  = Math.random() * 3;
                el.className = 'particle';
                el.style.cssText = `
                    width:${size}px;height:${size}px;
                    left:${Math.random()*100}%;
                    background:${colors[Math.floor(Math.random()*colors.length)]};
                    animation-duration:${dur}s,${sway}s;
                    animation-delay:${del}s,${del}s;
                `;
                container.appendChild(el);
                setTimeout(() => el.remove(), (dur + del) * 1000);
            }

            for (let i = 0; i < 30; i++) setTimeout(spawn, Math.random() * 5000);
            setInterval(spawn, 500);
        })();
    </script>
</body>
</html>