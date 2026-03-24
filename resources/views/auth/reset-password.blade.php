<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ROB Monitoring</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 font-ui text-white">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden">

        {{-- Background --}}
        <div class="absolute inset-0">
            <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl"></div>
            <div class="absolute right-0 bottom-0 h-80 w-80 rounded-full bg-blue-600/20 blur-3xl"></div>
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(56,189,248,0.08),transparent_30%),radial-gradient(circle_at_bottom_left,rgba(37,99,235,0.08),transparent_30%)]">
            </div>
        </div>

        <div class="relative w-full max-w-md px-4">
            <div class="rounded-3xl border border-white/10 bg-white/5 shadow-2xl backdrop-blur-xl p-8 sm:p-10">

                {{-- Header --}}
                <div class="mb-8 text-center">
                    <div
                        class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-500/10 border border-cyan-400/20">
                        <svg class="h-7 w-7 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-white">Password Baru</h2>
                    <p class="mt-2 text-sm text-slate-400">Buat password baru untuk akunmu</p>
                </div>

                {{-- Alert --}}
                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Form --}}
                <form action="{{ route('password.update') }}" method="POST" class="space-y-5" x-data="{ showPass: false, showConfirm: false }">
                    @csrf

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-200">Password
                            Baru</label>
                        <div class="relative">
                            <input type="password" :type="showPass ? 'text' : 'password'" name="password" id="password"
                                placeholder="Minimal 8 karakter"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 pr-12 text-white placeholder-slate-500 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                                required>
                            <button type="button" @click="showPass = !showPass"
                                class="absolute inset-y-0 right-3 my-auto flex items-center text-slate-400 hover:text-cyan-400 transition">
                                {{-- Eye open --}}
                                <svg x-show="!showPass" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{-- Eye slash --}}
                                <svg x-show="showPass" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="mb-2 block text-sm font-medium text-slate-200">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" :type="showConfirm ? 'text' : 'password'"
                                name="password_confirmation" id="password_confirmation"
                                placeholder="Ulangi password baru"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 pr-12 text-white placeholder-slate-500 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                                required>
                            <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute inset-y-0 right-3 my-auto flex items-center text-slate-400 hover:text-cyan-400 transition">
                                <svg x-show="!showConfirm" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showConfirm" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full cursor-pointer rounded-xl bg-cyan-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-300">
                        Reset Password
                    </button>
                </form>

            </div>
        </div>
    </div>
</body>

</html>
