@php
    $dbTheme = auth()->check() ? optional(auth()->user()->dashSetting)->theme ?? 'dark' : 'dark';
    $isDark = $dbTheme === 'dark';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="themeRoot()" x-init="initTheme()"
    :class="{ 'dark': dark }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - ROB Monitoring</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">

    {{-- Anti FOUC: apply tema secepat mungkin dari DB --}}
    <script>
        (function() {
            const dbTheme = @json($dbTheme);
            if (dbTheme === 'dark') document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');

            // optional: sync localStorage biar konsisten
            localStorage.setItem('theme', dbTheme);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body x-data="{ sidebarOpen: false, accountOpen: false, alatOpen: false }"
    class="bg-gray-50 dark:bg-zinc-900 text-zinc-900 dark:text-white font-ibm transition-colors duration-300">
    <div class="flex h-screen overflow-hidden">
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            style="display: none;" x-transition.opacity>
        </div>

        <x-sidebar />

        <main class="flex-1 flex flex-col overflow-y-auto">
            <x-header />
            <div class="flex-1 p-6 lg:p-10">
                {{ $slot }}
            </div>
            <x-footer />
        </main>
    </div>

    @livewireScripts
    @stack('scripts')

    <script>
        function themeRoot() {
            return {
                dark: @json($isDark),

                initTheme() {
                    // apply dari state awal
                    document.documentElement.classList.toggle('dark', this.dark);

                    // listen event dari Livewire (backend)
                    window.addEventListener('theme-changed', (e) => {
                        const t = e.detail?.theme ?? 'dark';
                        this.dark = (t === 'dark');
                        document.documentElement.classList.toggle('dark', this.dark);
                        localStorage.setItem('theme', t);
                    });

                    // listen event dari dashboard/app.js (optional)
                    window.addEventListener('theme-sync', (e) => {
                        const t = e.detail?.theme ?? 'dark';
                        this.dark = (t === 'dark');
                        document.documentElement.classList.toggle('dark', this.dark);
                        localStorage.setItem('theme', t);
                    });
                }
            }
        }
    </script>

</body>

</html>
