<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign In - ROB Monitoring</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles()
</head>

<body class="min-h-screen bg-slate-950 font-ui text-white">

    <x-preload theme="none" bg="dark" /> 
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl"></div>
            <div class="absolute right-0 bottom-0 h-80 w-80 rounded-full bg-blue-600/20 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(56,189,248,0.08),transparent_30%),radial-gradient(circle_at_bottom_left,rgba(37,99,235,0.08),transparent_30%)]"></div>
        </div>

        <div class="relative grid w-full max-w-5xl overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-2xl backdrop-blur-xl lg:grid-cols-2">
            <div class="hidden flex-col justify-between bg-gradient-to-br from-cyan-500/10 to-blue-600/10 p-10 lg:flex">
                <div>
                    <div class="mb-6 inline-flex items-center gap-3 rounded-xl border border-cyan-400/20 bg-cyan-400/10 px-4 py-2 text-sm font-medium text-cyan-300">
                        <span class="h-2 w-2 rounded-full bg-cyan-400"></span>
                        Early Warning System
                    </div>

                    <h1 class="max-w-md text-4xl font-bold leading-tight">
                        ROB Monitoring Dashboard Access
                    </h1>

                    <p class="mt-4 max-w-md text-sm leading-6 text-slate-300">
                        Pantau data monitoring secara real-time, aman, dan terpusat dalam satu sistem.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-sm text-slate-400">Status</p>
                        <p class="mt-2 text-lg font-semibold text-emerald-400">System Online</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-sm text-slate-400">Security</p>
                        <p class="mt-2 text-lg font-semibold text-cyan-300">Protected Access</p>
                    </div>
                </div>
            </div>

            <div class="p-8 sm:p-10 lg:p-12">
                <div class="mx-auto w-full max-w-md">
                    <div class="mb-8 text-center lg:text-left">
                        <h2 class="text-3xl font-bold tracking-tight text-white">Sign in</h2>
                        <p class="mt-2 text-sm text-slate-400">
                            Masuk ke sistem ROB Monitoring
                        </p>
                    </div>

                    @if (session('error'))
                        <div class="mb-4 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('login.auth') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-slate-200">
                                Email
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan email"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                                required
                            >
                            @error('email')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ showPassword: false }">
                            <div class="mb-2 flex items-center justify-between">
                                <label for="password" class="block text-sm font-medium text-slate-200">
                                    Password
                                </label>
                                <a href="{{ route('password.request') }}" class="text-sm text-cyan-400 hover:text-cyan-300">
                                    Lupa password?
                                </a>
                            </div>

                            <div class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    id="password"
                                    placeholder="Masukkan password"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 pr-12 text-white placeholder-slate-500 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                                    required
                                >

                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-3 my-auto text-slate-400 transition hover:text-cyan-400"
                                    :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                                >
                                    <template x-if="!showPassword">
                                        <x-heroicon-o-eye class="h-5 w-5 cursor-pointer" />
                                    </template>

                                    <template x-if="showPassword">
                                        <x-heroicon-o-eye-slash class="h-5 w-5 cursor-pointer" />
                                    </template>
                                </button>
                            </div>

                            @error('password')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-sm text-slate-300">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="rounded border-white/10 bg-white/5 text-cyan-500 focus:ring-cyan-400"
                                >
                                Remember me
                            </label>
                        </div>

                        <button
                            type="submit"
                            class="cursor-pointer w-full rounded-xl bg-cyan-500 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-slate-950"
                        >
                            Sign In
                        </button>
                    </form>

                    <p class="mt-8 text-center text-sm text-slate-500">
                        © {{ date('Y') }} ROB Monitoring System
                    </p>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts()
</body>

</html>