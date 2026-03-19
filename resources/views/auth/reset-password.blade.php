<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ROB Monitoring</title>
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
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
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-200">Password Baru</label>
                        <div class="relative">
                            <input
                                :type="showPass ? 'text' : 'password'"
                                name="password"
                                id="password"
                                placeholder="Minimal 8 karakter"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 pr-12 text-white placeholder-slate-500 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                                required
                            >
                            <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-3 my-auto text-slate-400 hover:text-cyan-400 transition">
                                <template x-if="!showPass">
                                    <x-heroicon-o-eye class="h-5 w-5"/>
                                </template>
                                <template x-if="showPass">
                                    <x-heroicon-o-eye-slash class="h-5 w-5"/>
                                </template>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-200">Konfirmasi Password</label>
                        <div class="relative">
                            <input
                                :type="showConfirm ? 'text' : 'password'"
                                name="password_confirmation"
                                id="password_confirmation"
                                placeholder="Ulangi password baru"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 pr-12 text-white placeholder-slate-500 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                                required
                            >
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-3 my-auto text-slate-400 hover:text-cyan-400 transition">
                                <template x-if="!showConfirm">
                                    <x-heroicon-o-eye class="h-5 w-5"/>
                                </template>
                                <template x-if="showConfirm">
                                    <x-heroicon-o-eye-slash class="h-5 w-5"/>
                                </template>
                            </button>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full cursor-pointer rounded-xl bg-cyan-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-300"
                    >
                        Reset Password
                    </button>
                </form>

            </div>
        </div>
    </div>
</body>
</html>