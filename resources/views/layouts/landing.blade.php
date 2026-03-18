<!DOCTYPE html>
<html lang="id"
    x-data="{
        dark: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && true),
        mobileMenu: false
    }"
    x-init="
        document.documentElement.classList.toggle('dark', dark);
        $watch('dark', val => {
            localStorage.setItem('theme', val ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', val);
        });
    "
    :class="{ 'dark': dark }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'ROB Monitoring' }} - Early Warning System</title>
    <meta name="description" content="Sistem peringatan dini banjir rob berbasis IoT untuk wilayah pesisir Kabupaten Ketapang, Kalimantan Barat.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles()
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white transition-colors duration-300 overflow-x-hidden font-ui">
    
    {{-- Background Effects --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-150px] left-1/2 h-[500px] w-[500px] -translate-x-1/2 rounded-full bg-blue-600/10 dark:bg-blue-600/20 blur-3xl"></div>
        <div class="absolute bottom-[-100px] right-[-100px] h-[400px] w-[400px] rounded-full bg-cyan-500/10 dark:bg-cyan-500/15 blur-3xl"></div>
        <div class="absolute top-1/2 left-[-100px] h-[300px] w-[300px] rounded-full bg-indigo-500/5 dark:bg-indigo-500/10 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.02] dark:opacity-[0.04]"
            style="background-image: linear-gradient(#3b82f6 1px, transparent 1px), linear-gradient(to right, #3b82f6 1px, transparent 1px); background-size: 48px 48px;"></div>
    </div>

    <x-landing.navbar/>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <div class="flex-1">
            {{ $slot }}
        </div>
        <x-landing.footer/>
    </main>
    
    @livewireScripts()
</body>
</html>