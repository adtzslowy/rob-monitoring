<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - ROB Monitoring</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-ui text-white">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden">

        {{-- Background --}}
        <div class="absolute inset-0">
            <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl"></div>
            <div class="absolute right-0 bottom-0 h-80 w-80 rounded-full bg-blue-600/20 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(56,189,248,0.08),transparent_30%),radial-gradient(circle_at_bottom_left,rgba(37,99,235,0.08),transparent_30%)]"></div>
        </div>

        <div class="relative w-full max-w-md px-4">
            <div class="rounded-3xl border border-white/10 bg-white/5 shadow-2xl backdrop-blur-xl p-8 sm:p-10">

                {{-- Header --}}
                <div class="mb-8 text-center">
                    <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-500/10 border border-cyan-400/20">
                        <svg class="h-7 w-7 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Lupa Password</h2>
                    <p class="mt-2 text-sm text-slate-400">Masukkan email akunmu untuk menerima kode OTP</p>
                </div>

                {{-- Alert --}}
                @if (session('success'))
                    <div class="mb-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Form --}}
                <form action="{{ route('password.send-otp') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-200">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                            required
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full cursor-pointer rounded-xl bg-cyan-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-300"
                    >
                        Kirim Kode OTP
                    </button>
                </form>

                <a href="{{ route('login') }}" class="mt-6 block text-center text-sm text-slate-500 hover:text-cyan-400 transition">
                    ← Kembali ke halaman masuk
                </a>

            </div>
        </div>
    </div>
</body>
</html>