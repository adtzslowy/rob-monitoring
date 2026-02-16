<aside class="hidden lg:flex sticky top-0 h-screen flex-col w-64 bg-zinc-900 border-r border-zinc-800 p-6 flex-shrink-0">

    <div class="flex items-center gap-3 mb-8 pb-6 border-b border-zinc-600">
        <div class="w-9 h-9 bg-blue-500 rounded-xl flex items-center justify-center">
            <x-heroicon-o-bell-alert class="w-5 h-5 text-white" />
        </div>
        <div>
            <h1 class="text-lg font-semibold text-white">
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
                    : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                Dashboard
            </a>

            <a href="{{ route('peta_monitoring') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('peta_monitoring')
                    ? 'bg-blue-500 text-white'
                    : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }}">
                <x-heroicon-o-map class="w-5 h-5" />
                Peta Monitoring
            </a>
        </div>

        {{-- Menejemen Akun --}}
        <div class="space-y-2">
            <p class="px-3 text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">
                Manajemen Akun
            </p>

            <button id="accountToggle" type="button"
                class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition text-zinc-400 hover:bg-zinc-800 hover:text-white cursor-pointer">
                <div class="flex items-center gap-3">
                    <x-heroicon-o-user class="w-5 h-5" />
                    Account
                </div>

                <x-heroicon-o-chevron-down id="accountArrow"
                    class="w-4 h-4 transition-transform duration-300" />
            </button>

            <div id="accountMenu" class="hidden ml-8 space-y-2 text-sm">

                <a href="{{ route('akun_user') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
            {{ request()->routeIs('akun_user')
                ? 'bg-blue-500 text-white'
                : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }} cursor-pointer">
                    <x-heroicon-o-user class="w-5 h-5" />
                    Profil
                </a>

                <a href="{{ route('pengaturan') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
            {{ request()->routeIs('pengaturan')
                ? 'bg-blue-500 text-white'
                : 'text-zinc-400 hover:bg-zinc-800 hover:text-white' }} cursor-pointer">
                    <x-heroicon-o-cog class="w-5 h-5" />
                    Pengaturan
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-red-400 hover:bg-zinc-800 hover:text-red-300 transition cursor-pointer">
                        <x-heroicon-o-arrow-right-end-on-rectangle class="w-5 h-5" />
                        Logout
                    </button>
                </form>

            </div>
        </div>


    </nav>

</aside>
