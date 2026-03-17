<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Beranda - ROB Monitoring</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
     <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles()
</head>
<body class="bg-slate-950 text-white font-ui">


    {{-- Section --}}
    <section class="px-6 py-16 text-center">
        <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">
            ROB Monitoring System
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-slate-400">
            Sistem monitoring untuk mendeteksi kondisi lingkungan seperti suhu, kelembapan,
            dan potensi banjir rob secara real-time.
        </p>
    </section>

    <div class="mt-6 flex justify-center gap-3">
        <a href="{{ route('peta') }}" class="rounded bg-blue-600 px-5 py-2 text-sm font-medium hover:bg-blue-500 transition-all duration-300">
            <x-heroicon-o-map class="w-5 h-5"/>
            Lihat Peta
        </a>
    </div>

    @livewireScripts()
</body>
</html>