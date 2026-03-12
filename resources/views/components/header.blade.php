<header
    class="sticky top-0 z-30 bg-gray-50 text-zinc-900 dark:text-white dark:bg-zinc-900/80 backdrop-blur-md border-b border-zinc-900/20 shadow-sm dark:border-zinc-800">

    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">

        {{-- LEFT SECTION --}}
        <div class="flex items-center gap-4">

            {{-- Toggle Sidebar (Mobile Only) --}}
            <button @click="sidebarOpen = true"
                class="text-zinc-900 bg-white dark:bg-zinc-800 lg:hidden p-2 rounded-lg transition cursor-pointer">
                <x-heroicon-o-bars-3 class="w-6 h-6 dark:text-white" />
            </button>

            {{-- Logo + Title --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-500 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-bell-alert class="w-5 h-5 text-black" />
                </div>

                <div class="leading-tight">
                    <h1 class="text-base sm:text-lg font-semibold text-zinc-900 dark:text-white">
                        ROB Monitoring
                    </h1>
                    <p class="text-xs text-zinc-600 dark:text-zinc-300">
                        Early Warning System
                    </p>
                </div>
            </div>
        </div>

        {{-- RIGHT SECTION --}}
        <div class="flex items-center gap-3 sm:gap-4 text-sm">
            {{-- Time --}}
            <div class="hidden md:block px-3 py-1 rounded-full bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                {{ now()->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}
            </div>

            {{-- User Menu --}}
            <div x-data="{ open: false }" class="relative">
                <button
                    @click="open = !open"
                    type="button"
                    class="flex items-center gap-3 rounded-xl border border-zinc-200 bg-white px-2.5 py-2 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-800"
                >
                    @php
                        $headerAvatar = auth()->user()?->foto_profil
                            ? asset('storage/' . auth()->user()->foto_profil)
                            : null;

                        $headerName = auth()->user()?->name ?? 'User';
                        $headerRole = auth()->user()?->roles->first()?->name ?? 'No Role';
                    @endphp

                    @if ($headerAvatar)
                        <img
                            src="{{ $headerAvatar }}"
                            alt="Foto Profil"
                            class="h-9 w-9 rounded-full object-cover border border-zinc-200 dark:border-zinc-700"
                        >
                    @else
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">
                            {{ strtoupper(substr($headerName, 0, 1)) }}
                        </div>
                    @endif

                    <div class="hidden sm:block text-left leading-tight">
                        <div class="max-w-[140px] truncate font-medium text-zinc-900 dark:text-white">
                            {{ $headerName }}
                        </div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ ucfirst($headerRole) }}
                        </div>
                    </div>

                    <x-heroicon-o-chevron-down class="h-4 w-4 text-zinc-500 dark:text-zinc-400" />
                </button>

                {{-- Dropdown --}}
                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    class="cursor-pointer absolute right-0 mt-2 w-64 overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900"
                    style="display: none;"
                >
                    <div class="border-b border-zinc-200 px-4 py-4 dark:border-zinc-700">
                        <div class="font-semibold text-zinc-900 dark:text-white">
                            {{ $headerName }}
                        </div>
                        <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ auth()->user()?->email }}
                        </div>
                        <div class="mt-2 inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">
                            {{ ucfirst($headerRole) }}
                        </div>
                    </div>

                    <div class="p-2">
                        <a
                            href="{{ route('profil') }}"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-zinc-700 transition hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800"
                        >
                            <x-heroicon-o-user-circle class="h-5 w-5" />
                            Akun saya
                        </a>

                        <a
                            href="#"
                            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-zinc-700 transition hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800"
                        >
                            <x-heroicon-o-cog-6-tooth class="h-5 w-5" />
                            Pengaturan
                        </a>

                        <div class="my-2 border-t border-zinc-200 dark:border-zinc-700"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                            >
                                <x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5" />
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</header>