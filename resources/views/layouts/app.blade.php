<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - ROB Monitoring</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-zinc-900 text-white font-ibm">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <x-sidebar />

        {{-- main layout --}}
        <main class="flex-1 flex flex-col overflow-y-auto">
            <div class="flex-1 p-6 lg:p-10">
                {{ $slot }}

                <x-footer/>
            </div>
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const toggle = document.getElementById('accountToggle');
            const menu = document.getElementById('accountMenu');
            const arrow = document.getElementById('accountArrow');

            if (!toggle) return;

            toggle.addEventListener('click', function() {

                menu.classList.toggle('hidden');

                if (menu.classList.contains('hidden')) {
                    arrow.classList.add('rotate-90');
                } else {
                    arrow.classList.remove('rotate-90');
                }

            });

        });
    </script>

</body>

</html>
