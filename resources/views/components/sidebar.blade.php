
<aside id="sidebar"
    :class="{ 'translate-x-0': sidebarOpen }"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-50 dark:bg-zinc-900 border-r border-zinc-800/20 dark:border-zinc-800 p-6
           transform -translate-x-full transition-transform duration-300 ease-in-out
           lg:static lg:translate-x-0 lg:flex lg:flex-col lg:h-screen">
    <div class="flex items-center gap-3 mb-8 pb-6 border-b border-zinc-600/20">
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
                    : 'text-zinc-900 dark:text-zinc-400 hover:bg-blue-500/10 hover:text-zinc-900' }}">
                <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                Dashboard
            </a>

            <a href="{{ route('peta_monitoring') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('peta_monitoring')
                    ? 'bg-blue-500 text-white'
                    : 'text-zinc-900 dark:text-zinc-400 hover:bg-blue-500/10 hover:text-zinc-900' }}">
                <x-heroicon-o-map class="w-5 h-5" />
                Peta Monitoring
            </a>

            <a href="{{ route('peta_monitoring') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('peta_monitoring')
                    ? 'bg-blue-500 text-white'
                    : 'text-zinc-900 dark:text-zinc-400 hover:bg-blue-500/10 hover:text-zinc-900' }}">
                <x-heroicon-o-wrench-screwdriver class="w-5 h-5" />
                Daftar Alat
            </a>

            <button type="button"
                @click="alatOpen = !alatOpen"
                class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition text-zinc-900 dark:text-zinc-400 hover:bg-blue-500/10 hover:text-zinc-900 cursor-pointer">
                <div class="flex items-center gap-3">
                    <x-heroicon-o-server class="w-5 h-5" />
                    Data Sensor
                </div>

                <x-heroicon-o-chevron-down
                    :class="{ '-rotate-90': !alatOpen }"
                    class="w-4 h-4 transition-transform duration-300" />
            </button>

            <div x-show="alatOpen" x-transition class="ml-8 space-y-2 text-sm" style="display: none;">
                {{-- @foreach ($sensors as $sensor )
                    <a href="" class="block text-sm text-zinc-500 hover:text-white">
                        {{ $sensor->name }}
                    </a>
                @endforeach --}}
            </div>
        </div>

        {{-- Menejemen Akun --}}
        <div class="space-y-2">
            <p class="px-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">
                Manajemen Akun
            </p>

            <button type="button"
                @click="accountOpen = !accountOpen"
                class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition text-zinc-900 dark:text-zinc-400 hover:bg-blue-500/10 hover:text-zinc-900 cursor-pointer">
                <div class="flex items-center gap-3">
                    <x-heroicon-o-user class="w-5 h-5" />
                    Account
                </div>

                <x-heroicon-o-chevron-down
                    :class="{ '-rotate-90': !accountOpen }"
                    class="w-4 h-4 transition-transform duration-300" />
            </button>

            <div x-show="accountOpen" x-transition class="ml-8 space-y-2 text-sm" style="display: none;">

                <a href="{{ route('akun_user') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
            {{ request()->routeIs('akun_user')
                ? 'bg-blue-500 text-white'
                : 'text-zinc-900 dark:text-zinc-400 hover:bg-blue-500/10 hover:text-zinc-900' }} cursor-pointer">
                    <x-heroicon-o-user class="w-5 h-5" />
                    Profil
                </a>

                <a href="{{ route('pengaturan') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
            {{ request()->routeIs('pengaturan')
                ? 'bg-blue-500 text-white'
                : 'text-zinc-900 dark:text-zinc-400 hover:bg-blue-500/10 hover:text-zinc-900' }} cursor-pointer">
                    <x-heroicon-o-cog class="w-5 h-5" />
                    Pengaturan
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-red-400 dark:hover:bg-zinc-400 hover:text-red-500 transition cursor-pointer">
                        <x-heroicon-o-arrow-right-end-on-rectangle class="w-5 h-5" />
                        Logout
                    </button>
                </form>

            </div>
        </div>


    </nav>

</aside>
