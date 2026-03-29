<!DOCTYPE html>
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
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 font-ui text-slate-900 dark:text-white overflow-hidden">

    <x-preload theme="none" bg="dark"/>

    {{-- Background --}}
    <div class="fixed inset-0 -z-10 pointer-events-none">
        <div class="absolute top-[-150px] left-1/2 h-[500px] w-[500px] -translate-x-1/2 rounded-full bg-blue-600/10 dark:bg-blue-600/15 blur-3xl"></div>
        <div class="absolute bottom-[-100px] right-[-100px] h-[400px] w-[400px] rounded-full bg-cyan-500/10 dark:bg-cyan-500/10 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.015] dark:opacity-[0.03]"
             style="background-image:linear-gradient(#3b82f6 1px,transparent 1px),linear-gradient(to right,#3b82f6 1px,transparent 1px);background-size:48px 48px;"></div>
    </div>

    <div class="flex min-h-screen flex-col items-center justify-center px-4 text-center">

        {{-- Code --}}
        <div class="relative mb-6 select-none">
            <span class="text-[9rem] sm:text-[12rem] font-extrabold leading-none tracking-tighter
                         text-slate-100 dark:text-slate-800/80">
                404
            </span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl
                            bg-blue-500/10 dark:bg-blue-500/20 border border-blue-500/20
                            text-blue-500 dark:text-blue-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Text --}}
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-3">
            Halaman Tidak Ditemukan
        </h1>
        <p class="max-w-md text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-8">
            Halaman yang kamu cari tidak ada atau sudah dipindahkan.
            Pastikan URL yang kamu masukkan sudah benar.
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-blue-500 hover:bg-blue-400
                      px-5 py-2.5 text-sm font-semibold text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Kembali ke Beranda
            </a>
            <button onclick="history.back()"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700
                           bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-semibold
                           text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                </svg>
                Kembali
            </button>
        </div>

        {{-- Footer --}}
        <p class="mt-12 text-xs text-slate-400 dark:text-slate-600">
            © {{ date('Y') }} ROB Monitoring System
        </p>

    </div>
</body>
</html>