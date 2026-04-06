<section class="p-3 sm:p-4 md:p-6">
    <div class="mx-auto space-y-4 sm:space-y-5">

        {{-- HEADER --}}
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="p-2.5 rounded-2xl bg-blue-500/10 text-blue-400 border border-blue-500">
                    <x-heroicon-o-inbox class="w-6 h-6" />
                </div>

                <div>
                    <h1 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">
                        Kritik & Saran
                    </h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Pesan dari pengguna
                    </p>
                </div>
            </div>
        </div>

        {{-- CARD --}}
        <div
            class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950/60 overflow-hidden">

            {{-- TOOLBAR --}}
            <div
                class="flex flex-col gap-3 p-3 sm:p-4 bg-zinc-50/70 dark:bg-zinc-900/40 border-b border-zinc-200/40 dark:border-zinc-800/30">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">

                    {{-- SEARCH --}}
                    <div class="w-full lg:max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-zinc-400" />
                            </div>

                            <input wire:model.live.debounce.200ms="search" type="text"
                                class="w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100
                                       placeholder:text-zinc-400 dark:placeholder:text-zinc-500
                                       pl-10 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                                placeholder="Search nama / alias / ID..." />
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div wire:poll.visible.5s="refreshContacts" @if ($modalOpen) wire:poll.remove @endif
                class="overflow-x-auto">

                <table class="min-w-[760px] w-full text-sm table-fixed">

                    <thead class="bg-zinc-50 dark:bg-zinc-900 text-xs uppercase text-zinc-500 border-b border-zinc-200/40 dark:border-zinc-800/30">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">No</th>
                            <th class="px-4 py-3 text-left font-medium">Nama</th>
                            <th class="px-4 py-3 text-left font-medium">Email</th>
                            <th class="px-4 py-3 text-left font-medium">Pesan</th>
                            <th class="px-4 py-3 text-center font-medium">Status</th>
                            <th class="px-4 py-3 text-center font-medium">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/20">
                        @forelse($contacts as $c)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors duration-150">

                                <td class="px-4 py-3 text-left text-zinc-500 dark:text-zinc-400">
                                    {{ ($contacts->firstItem() ?? 1) + $loop->index }}
                                </td>

                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $c->name }}
                                </td>

                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 sm:truncate md:truncate">
                                    {{ $c->email }}
                                </td>

                                <td class="px-4 py-3 truncate text-zinc-600 dark:text-zinc-300">
                                    {{ $c->message }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($c->status === \App\ContactStatus::NEW)
                                        <span class="px-2.5 py-1 text-xs bg-red-500 text-white rounded-md font-semibold shadow-sm shadow-red-500/20">
                                            NEW
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs bg-blue-500/15 text-blue-400 border border-blue-500/20 rounded-md font-medium">
                                            READ
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <button wire:click="openModal({{ $c->id }})"
                                        class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer transition-colors duration-150">
                                        <x-heroicon-o-eye class="w-5 h-5 text-blue-500" />
                                    </button>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-zinc-400 dark:text-zinc-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-8 h-8 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V8.844a2.25 2.25 0 011.183-1.98l7.5-4.04a2.25 2.25 0 012.134 0l7.5 4.04a2.25 2.25 0 011.183 1.98V19.5z" />
                                        </svg>
                                        <span>Tidak ada pesan ditemukan</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>

            {{-- FOOTER --}}
            <div class="p-4 flex justify-between items-center text-sm text-zinc-500 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-800/30">
                <span>
                    {{ $contacts->firstItem() ?? 0 }} - {{ $contacts->lastItem() ?? 0 }}
                    dari {{ $contacts->total() }}
                </span>

                <div>
                    {{ $contacts->links() }}
                </div>
            </div>

        </div>

        {{-- MODAL --}}
        @if ($modalOpen && $selectedContact)
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 px-4"
                 x-data x-show x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl w-full max-w-lg shadow-2xl border border-zinc-200 dark:border-zinc-800"
                     x-show x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                    <div class="flex justify-between items-center mb-5">
                        <h3 class="font-bold text-lg text-zinc-900 dark:text-zinc-100">Detail Pesan</h3>
                        <button wire:click="closeModal" 
                                class="p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4 text-zinc-700 dark:text-zinc-300 text-sm">
                        <div>
                            <span class="text-xs font-semibold uppercase text-zinc-400 dark:text-zinc-500">Nama</span>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedContact->name }}</p>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase text-zinc-400 dark:text-zinc-500">Email</span>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-zinc-100">{{ $selectedContact->email }}</p>
                        </div>

                        <div>
                            <span class="text-xs font-semibold uppercase text-zinc-400 dark:text-zinc-500">Pesan</span>
                            <div class="mt-1 p-4 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl border border-zinc-100 dark:border-zinc-700/50 leading-relaxed">
                                {{ $selectedContact->message }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif

    </div>
</section>