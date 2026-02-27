<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - ROB Monitoring</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-zinc-950 text-white flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-zinc-900 border border-zinc-800 rounded-2xl p-8 shadow-lg">

        <h2 class="text-2xl font-bold mb-3 text-center">
            Sign In with Email
        </h2>
        <p class="text-sm text-muted text-center mb-6">
            Teknologi yang siaga 24 jam.<br /> Karena keselamatan tidak bisa menunggu
        </p>

        @if ($errors->any())
            <div class="mb-4 text-red-400 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST"  action="{{ route('login.auth') }}" class="space-y-6 p-7">
            @csrf

            <input type="email" name="email" placeholder="Email"
                class="w-full p-3 rounded bg-zinc-800 border border-zinc-700 focus:outline-none focus:border-emerald-500">

            <input type="password" name="password" placeholder="Password"
                class="w-full p-3 rounded bg-zinc-800 border border-zinc-700 focus:outline-none focus:border-emerald-500">

            <button type="submit"
                class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-semibold py-3 rounded transition">
                Login
            </button>
        </form>
    </div>

</body>

</html>
