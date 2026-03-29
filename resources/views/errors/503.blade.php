<!DOCTYPE html>
<html lang="id"
    x-data="{ dark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && true) }"
    x-init="document.documentElement.classList.toggle('dark', dark)"
    :class="{ 'dark': dark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 — Layanan Tidak Tersedia | ROB Monitoring</title>
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

    {{-- Background --}}
    <div class="fixed inset-0 -z-10 pointer-events-none">
        <div class="absolute top-[-150px] left-1/2 h-[500px] w-[500px] -translate-x-1/2 rounded-full bg-amber-500/10 dark:bg-amber-500/10 blur-3xl"></div>
        <div class="absolute bottom-[-100px] right-[-100px] h-[400px] w-[400px] rounded-full bg-orange-500/10 dark:bg-orange-500/10 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.015] dark:opacity-[0.03]"
             style="background-image:linear-gradient(#f59e0b 1px,transparent 1px),linear-gradient(to right,#f59e0b 1px,transparent 1px);background-size:48px 48px;"></div>
    </div>

    <div class="flex min-h-screen flex-col items-center justify-center px-4 text-center">

        {{-- Code --}}
        <div class="relative mb-6 select-none">
            <span class="text-[9rem] sm:text-[12rem] font-extrabold leading-none tracking-tighter
                         text-slate-100 dark:text-slate-800/80">
                503
            </span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl
                            bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/20
                            text-amber-500 dark:text-amber-400">
                    {{-- Animated pulse to show "maintenance" --}}
                    <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Maintenance badge --}}
        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-amber-500/20
                    bg-amber-500/10 px-4 py-1.5 text-xs font-semibold text-amber-600 dark:text-amber-400">
            <span class="relative flex h-2 w-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-amber-500"></span>
            </span>
            Sedang dalam pemeliharaan
        </div>

        {{-- Text --}}
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-3">
            Layanan Tidak Tersedia
        </h1>
        <p class="max-w-md text-sm text-slate-500 dark:text-slate-400 leading-relaxed mb-8">
            Sistem ROB Monitoring sedang dalam pemeliharaan terjadwal.
            Kami akan segera kembali online. Terima kasih atas kesabaranmu.
        </p>

        {{-- Retry --}}
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <button onclick="window.location.reload()"
                    class="inline-flex items-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-400
                           px-5 py-2.5 text-sm font-semibold text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.023 9.348h4.992m-4.992 0a8.25 8.25 0 10-9.565 9.565m9.565-9.565L19.5 6M4.5 19.5l3.75-3.75" />
                </svg>
                Coba Lagi
            </button>
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700
                      bg-white dark:bg-slate-900 px-5 py-2.5 text-sm font-semibold
                      text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Ke Beranda
            </a>
        </div>

        {{-- Footer --}}
        <p class="mt-12 text-xs text-slate-400 dark:text-slate-600">
            © {{ date('Y') }} ROB Monitoring System
        </p>

    </div>
</body>
</html>