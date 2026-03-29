<!DOCTYPE html>
<html lang="id"
    x-data="{ dark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && true) }"
    x-init="document.documentElement.classList.toggle('dark', dark)"
    :class="{ 'dark': dark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak | ROB Monitoring</title>
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
        <div class="absolute -top-40 left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-red-600/10 dark:bg-red-600/15 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 h-[400px] w-[400px] rounded-full bg-rose-500/10 dark:bg-rose-500/15 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.015] dark:opacity-[0.035]"
             style="background-image:linear-gradient(#ef4444 1px,transparent 1px),linear-gradient(to right,#ef4444 1px,transparent 1px);background-size:48px 48px;"></div>
    </div>

    {{-- Particles --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none" id="particles-403"></div>

    <div class="flex min-h-screen flex-col items-center justify-center px-4 text-center">

        {{-- Big number --}}
        <div class="relative mb-6 select-none afu">
            <span class="text-[9rem] sm:text-[12rem] font-extrabold leading-none tracking-tighter text-slate-200 dark:text-slate-800">
                403
            </span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-red-500/10 dark:bg-red-500/20 border border-red-500/20 text-red-500 dark:text-red-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Badge --}}
        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-red-500/20 bg-red-500/10 px-4 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 afu d1">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
            </svg>
            Akses tidak diizinkan
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold mb-3 afu d2">Akses Ditolak</h1>
        <p class="max-w-md text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-8 afu d2">
            Kamu tidak memiliki izin untuk mengakses halaman ini.
            Jika kamu merasa ini adalah kesalahan, silakan hubungi administrator.
        </p>

        <div class="flex flex-col sm:flex-row items-center gap-3 afu d3">
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-red-500 hover:bg-red-400 px-5 py-2.5 text-sm font-semibold text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                </svg>
                Ke Beranda
            </a>
            @auth
                <button onclick="history.back()"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
                    </svg>
                    Kembali
                </button>
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                    </svg>
                    Sign In
                </a>
            @endauth
        </div>

        <p class="mt-12 text-xs text-slate-400 dark:text-slate-600 afu d4">
            © {{ date('Y') }} ROB Monitoring System
        </p>
    </div>

    <script>
        (function () {
            const container = document.getElementById('particles-403');
            const isDark = document.documentElement.classList.contains('dark');
            const colors = isDark
                ? ['rgba(239,68,68,0.4)','rgba(248,113,113,0.3)','rgba(244,63,94,0.25)','rgba(252,165,165,0.2)']
                : ['rgba(239,68,68,0.15)','rgba(248,113,113,0.12)','rgba(244,63,94,0.1)','rgba(252,165,165,0.1)'];

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