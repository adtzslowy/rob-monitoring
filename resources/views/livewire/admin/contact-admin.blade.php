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
                    <p class="text-sm text-zinc-500">
                        Pesan dari pengguna
                    </p>
                </div>
            </div>

            <span class="text-xs text-green-500 animate-pulse">● Live</span>
        </div>

        {{-- CARD --}}
        <div
            class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950/60 overflow-hidden">

            {{-- TOOLBAR --}}
            <div
                class="flex flex-col gap-3 p-3 sm:p-4 bg-zinc-50/70 dark:bg-zinc-900/40 border-b border-zinc-200 dark:border-zinc-800">
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

                    <thead class="bg-zinc-50 dark:bg-zinc-900 text-xs uppercase text-zinc-500">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Pesan</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse($contacts as $c)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40">

                                <td class="px-4 py-3 text-left">
                                    {{ ($contacts->firstItem() ?? 1) + $loop->index }}
                                </td>

                                <td class="px-4 py-3 font-medium">
                                    {{ $c->name }}
                                </td>

                                <td class="px-4 py-3 text-zinc-500">
                                    {{ $c->email }}
                                </td>

                                <td class="px-4 py-3 truncate">
                                    {{ $c->message }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($c->status === \App\ContactStatus::NEW)
                                        <span class="px-2 py-1 text-xs bg-red-500 text-white rounded">
                                            NEW
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-blue-500 text-white rounded">
                                            READ
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <button wire:click="openModal({{ $c->id }})"
                                        class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 cursor-pointer">
                                        <x-heroicon-o-eye class="w-5 h-5 text-blue-500" />
                                    </button>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-zinc-400">
                                    Tidak ada pesan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>

            {{-- FOOTER --}}
            <div class="p-4 flex justify-between text-sm">
                <span>
                    {{ $contacts->firstItem() ?? 0 }} - {{ $contacts->lastItem() ?? 0 }}
                    dari {{ $contacts->total() }}
                </span>

                {{ $contacts->links() }}
            </div>

        </div>

        {{-- MODAL --}}
        @if ($modalOpen && $selectedContact)
            <div class="fixed inset-0 bg-black/60 flex items-center justify-center">

                <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl w-full max-w-lg">

                    <div class="flex justify-between mb-4">
                        <h3 class="font-bold text-lg">Detail Pesan</h3>
                        <button wire:click="closeModal">✕</button>
                    </div>

                    <div class="space-y-3">
                        <p><b>Nama:</b> {{ $selectedContact->name }}</p>
                        <p><b>Email:</b> {{ $selectedContact->email }}</p>

                        <div class="p-4 bg-zinc-100 dark:bg-zinc-800 rounded-xl">
                            {{ $selectedContact->message }}
                        </div>
                    </div>

                </div>

            </div>
        @endif

    </div>
</section>
