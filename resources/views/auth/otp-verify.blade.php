<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - ROB Monitoring</title>
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0119.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 004.5 10.5a7.464 7.464 0 01-1.15 3.993m1.989 3.559A11.209 11.209 0 008.25 10.5a3.75 3.75 0 117.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 01-3.6 9.75m6.633-4.596a18.666 18.666 0 01-2.485 5.33" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Masukkan Kode OTP</h2>
                    <p class="mt-2 text-sm text-slate-400">
                        Kode telah dikirim ke
                        <span class="font-semibold text-cyan-400">{{ session('reset_email') }}</span>
                    </p>
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
                <form action="{{ route('password.verify-otp') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="otp" class="mb-2 block text-sm font-medium text-slate-200">Kode OTP</label>
                        <input
                            type="text"
                            name="otp"
                            id="otp"
                            maxlength="6"
                            placeholder="••••••"
                            autocomplete="one-time-code"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30 text-center tracking-[0.5em] font-mono text-xl"
                            required
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full cursor-pointer rounded-xl bg-cyan-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-300"
                    >
                        Verifikasi OTP
                    </button>
                </form>

                {{-- Resend --}}
                <div
                    class="mt-5 text-center"
                    x-data="{
                        cooldown: 60,
                        timer: null,
                        init() {
                            this.timer = setInterval(() => {
                                if (this.cooldown > 0) this.cooldown--;
                                else clearInterval(this.timer);
                            }, 1000);
                        }
                    }"
                    x-init="init()"
                >
                    <template x-if="cooldown > 0">
                        <p class="text-sm text-slate-500">
                            Kirim ulang dalam
                            <span class="font-semibold text-slate-300" x-text="cooldown + ' detik'"></span>
                        </p>
                    </template>
                    <template x-if="cooldown === 0">
                        <form action="{{ route('password.resend-otp') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm font-semibold text-cyan-400 hover:text-cyan-300 transition cursor-pointer">
                                Kirim ulang kode OTP
                            </button>
                        </form>
                    </template>
                </div>

                <a href="{{ route('password.request') }}" class="mt-4 block text-center text-sm text-slate-500 hover:text-cyan-400 transition">
                    ← Ganti email
                </a>

            </div>
        </div>
    </div>
</body>
</html>