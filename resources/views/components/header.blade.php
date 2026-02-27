<header
    class="sticky top-0 z-30 bg-gray-50 text-zinc-900 dark:text-white dark:bg-zinc-900/80 backdrop-blur-md border-b border-zinc-900/20 shadow-sm dark:border-zinc-800">

    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">

        {{-- LEFT SECTION --}}
        <div class="flex items-center gap-4">

            {{-- Toggle Sidebar (Mobile Only) --}}
            <button @click="sidebarOpen = true"
                class=" text-zinc-900 bg-white dark:bg-zinc-800 lg:hidden p-2 rounded-lg transition cursor-pointer">
                <x-heroicon-o-bars-3 class="w-6 h-6 white dark:text-white" />
            </button>

            {{-- Logo + Title --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-500 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-bell-alert class="w-5 h-5 text-black" />
                </div>

                <div class="leading-tight">
                    <h1 class="text-dark text-base sm:text-lg font-semibold dark:text-white">
                        ROB Monitoring
                    </h1>
                    <p class="text-xs text-zinc-900 dark:text-white">
                        Early Warning System
                    </p>
                </div>
            </div>

        </div>

        {{-- RIGHT SECTION --}}
        <div class="flex items-center gap-4 text-sm">

            {{-- Time --}}
            <div class="hidden sm:block px-3 py-1 rounded-full bg-zinc-800 text-zinc-300">
                {{ now()->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}
            </div>

            {{-- Live Indicator --}}
            <div class="flex items-center gap-2 text-emerald-400 font-medium">
                <span class="relative flex h-2 w-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75">
                    </span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                </span>
                <span class="hidden sm:inline">LIVE</span>
            </div>

        </div>

    </div>

</header>
