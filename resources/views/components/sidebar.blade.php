<aside id="sidebar" :class="{ 'translate-x-0': sidebarOpen }"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-zinc-100 dark:bg-zinc-950 border-r border-zinc-400/50 dark:border-zinc-800 p-6
           transform -translate-x-full transition-transform duration-300 ease-in-out
           lg:static lg:translate-x-0 lg:flex lg:flex-col lg:h-screen">
    <div class="flex items-center gap-3 mb-8 pb-6 border-b border-zinc-600/30">
        <div class="w-9 h-9 bg-blue-500 rounded-xl flex items-center justify-center">
            <x-heroicon-o-bell-alert class="w-5 h-5 text-white" />
        </div>
        <div>
            <h1 class="text-lg font-semibold text-zinc-900 dark:text-white">
                ROB Monitoring
            </h1>
            <p class="text-xs text-zinc-400">
                Early Warning System
            </p>
        </div>
    </div>

    <nav class="space-y-6 text-sm">

        <!-- MENU -->
        <div class="space-y-2">
            <p class="px-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">
                Menu
            </p>

            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('dashboard')
                    ? 'bg-blue-500 text-white'
                    : 'text-zinc-900 dark:text-zinc-400 hover:bg-blue-100 hover:text-zinc-900' }}">
                <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                Dashboard
            </a>

            <a href="{{ route('peta_monitoring') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('peta_monitoring')
                    ? 'bg-blue-500 text-white'
                    : 'text-zinc-900 dark:text-zinc-400 hover:bg-blue-100 hover:text-zinc-900' }}">
                <x-heroicon-o-map class="w-5 h-5" />
                Peta Monitoring
            </a>
        </div>

        {{-- menejemen user --}}

        <div class="space-y-2">
            <p class="px-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">
                Manajemen Data
            </p>
            <a href="{{ route('manajemen_alat') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('manajemen_alat')
                    ? 'bg-blue-500 text-white'
                    : 'text-zinc-900 dark:text-zinc-400 hover:bg-blue-100 hover:text-zinc-900' }}">
                <x-heroicon-o-wrench-screwdriver class="w-5 h-5" />
                Daftar Alat
            </a>
            <a href="{{ route("sensor.list") }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition
    {{ request()->routeIs('sensor.list')
        ? 'bg-blue-500 text-white'
        : 'text-zinc-900 dark:text-zinc-400 hover:bg-blue-100 hover:text-zinc-900' }}">
                <x-heroicon-o-server class="w-5 h-5" />
                Data Sensor
            </a>
            @can('manage users')
                <a href="{{ route('admin.akun') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
            {{ request()->routeIs('admin.akun')
                ? 'bg-blue-500 text-white'
                : 'text-zinc-900 dark:text-zinc-400 hover:bg-blue-100 hover:text-zinc-900' }} cursor-pointer">
                    <x-heroicon-o-users class="w-5 h-5" />
                    Data Operator
                </a>
            @endcan
        </div>

    </nav>

</aside>
