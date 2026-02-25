@php
    $dbTheme = auth()->check() ? (optional(auth()->user()->dashSetting)->theme ?? 'dark') : 'dark';
    $isDark = $dbTheme === 'dark';
@endphp

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="themeRoot()"
    x-init="initTheme()"
    :class="{'dark': dark}"
>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - ROB Monitoring</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">

    {{-- Anti FOUC: apply tema secepat mungkin dari sesuai database --}}
    <script>
        (function() {
            const dbTheme = @json($dbTheme);
            if ($dbTheme === 'dark') document.documentElement.classList.add('dark');
            else document.documentElement.classList.add.remove('dark');
        })
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body x-data="{sidebarOpen: false, accountOpen: false, alatOpen: false}"
    class="bg-gray-50 dark:bg-zinc-900 text-zinc-900 dark:text-white font-ibm transition-colors duration-300">
    <div class="text-underline"></div>
    <div class="flex h-screen overflow-hidden">
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            style="display: none;"
            x-transition.opacity
            >
        </div>
        {{-- Sidebar --}}
        <x-sidebar />

        {{-- main layout --}}
        <main class="flex-1 flex flex-col overflow-y-auto">
            <x-header/>
            <div class="flex-1 p-6 lg:p-10">
                {{ $slot }}

            </div>
            <x-footer/>
        </main>
    </div>

    @livewireScripts
    <script>
        function themeRoot() {
            return (
                dark: @json($isDark),
                initTheme() {
                    document.documentElement.classList.toggle('dark', this.dark);

                    window.addEventListener('theme-changed', (e) => {
                        const t = e.detail.theme;
                        this.dark === (t === 'dark');
                        document.documentElement.classList.toggle('dark', this.dark);
                    });
                }
            )
        }
    </script>

</body>

</html>
