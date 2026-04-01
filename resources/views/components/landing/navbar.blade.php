@php
    $navLinks = [
        ['label' => 'Beranda', 'route' => 'home'],
        ['label' => 'Tentang', 'route' => 'tentang'],
        ['label' => 'Peta Monitoring', 'route' => 'peta'],
        ['label' => 'Analisis', 'route' => 'analisis'],
        ['label' => 'Kontak', 'route' => 'kontak'],
    ];
@endphp

<nav
    class="fixed top-0 inset-x-0 z-50 border-b border-slate-200/80 dark:border-slate-800/80 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md transition-colors duration-300">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 group-hover:bg-blue-700 transition">
                    <x-heroicon-o-bell-alert class="w-5 h-5 text-white" />
                </div>
                <div class="hidden sm:block">
                    <p class="text-sm font-bold text-slate-900 dark:text-white leading-none">ROB Monitoring</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Early Warning System</p>
                </div>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach ($navLinks as $link)
                    <a href="{{ route($link['route']) }}" @class([
                        'px-3 py-2 rounded-xl text-sm transition',
                        'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold' => request()->routeIs(
                            $link['route']),
                        'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->routeIs(
                            $link['route']),
                    ])>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- Right --}}
            <div class="flex items-center gap-2 sm:gap-3">

                {{-- Theme Toggle --}}
                <button @click="dark = !dark"
                    class="p-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <svg x-show="!dark" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                    <svg x-show="dark" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                </button>

                {{-- CTA --}}
                <a href="{{ route('login') }}"
                    class="hidden sm:inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm font-semibold text-white transition shadow-sm">
                    Masuk
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>

                {{-- Mobile Menu Button --}}
                <button @click="mobileMenu = !mobileMenu"
                    class="md:hidden p-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400">
                    <svg x-show="!mobileMenu" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="mobileMenu" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-cloak x-transition
            class="md:hidden border-t border-slate-200 dark:border-slate-800 py-3 space-y-1">
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route']) }}" @click="mobileMenu = false" @class([
                    'block px-3 py-2 rounded-xl text-sm transition',
                    'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold' => request()->routeIs(
                        $link['route']),
                    'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->routeIs(
                        $link['route']),
                ])>
                    {{ $link['label'] }}
                </a>
            @endforeach
            <a href="{{ route('login') }}"
                class="block px-3 py-2 rounded-xl text-sm font-semibold text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30">
                Masuk ke Dashboard →
            </a>
        </div>
    </div>
</nav>
