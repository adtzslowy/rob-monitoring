<section class="p-4 sm:p-6">
    <div class="mx-auto space-y-5">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="p-2.5 rounded-2xl bg-blue-500/10 text-blue-400 border border-blue-500">
                    <x-heroicon-o-cpu-chip class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                        Manajemen Alat
                    </h1>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Tambah / edit / hapus data alat.
                    </p>
                </div>
            </div>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="p-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-blue-300 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-3 rounded-xl border border-red-500/20 bg-red-500/10 text-red-300 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Card --}}
        <div
            class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950/60 overflow-hidden">

            {{-- Toolbar --}}
            <div
                class="flex flex-col gap-3 p-4 bg-zinc-50/70 dark:bg-zinc-900/40 border-b border-zinc-200 dark:border-zinc-800">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                    {{-- Search --}}
                    <div class="w-full md:max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-zinc-400" />
                            </div>

                            <input wire:model.live.debounce.200ms="search" type="text"
                                class="w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100
                                       placeholder:text-zinc-400 dark:placeholder:text-zinc-500
                                       pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                                placeholder="Search nama / alias / ID..." />
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="w-full md:w-auto flex flex-col sm:flex-row sm:items-center justify-end gap-2">
                        @if ($canManageDevices)
                            <button wire:click="openCreate"
                            class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-medium
                                   bg-blue-600 text-white hover:bg-blue-500
                                   focus:outline-none focus:ring-2 focus:ring-blue-500/30 cursor-pointer">
                                <x-heroicon-o-plus class="w-5 h-5" />
                                Tambah Alat
                            </button>
                        @endif

                        <div class="relative">
                            <select wire:model.live="perPage"
                                class="appearance-none w-full sm:w-[170px]
                                       rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100
                                       px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                <option value="10">10 / halaman</option>
                                <option value="25">25 / halaman</option>
                                <option value="50">50 / halaman</option>
                                <option value="100">100 / halaman</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        Tips: ✏️ edit, 🗑️ hapus.
                    </div>

                    @if (!empty($search))
                        <button wire:click="$set('search','')"
                            class="text-xs inline-flex items-center gap-1 text-zinc-600 hover:text-zinc-900
                                   dark:text-zinc-400 dark:hover:text-zinc-100">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                            Reset
                        </button>
                    @endif
                </div>
            </div>

            {{-- Table --}}
            <div wire:poll.visible.2s @class(['overflow-x-auto'])
                @if ($modalOpen) wire:poll.remove @endif>
                <table class="min-w-full w-full text-sm border-collapse table-fixed">
                    <thead class="text-xs uppercase bg-zinc-50 dark:bg-zinc-900/50 text-zinc-600 dark:text-zinc-400">
                        <tr>
                            <th class="w-[70px] px-5 py-3 text-center">No</th>
                            <th class="w-[90px] px-5 py-3 text-center">ID</th>
                            <th class="w-[220px] px-5 py-3 text-left">Nama</th>
                            <th class="w-[220px] px-5 py-3 text-left">Alias</th>
                            <th class="w-[160px] px-5 py-3 text-center">Status</th>
                            <th class="w-[170px] px-5 py-3 text-center">Last Seen</th>
                            <th class="w-[140px] px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($devices as $d)
                            @php
                                $st = strtolower((string) $d->status);
                                $isOnline = $st === 'online';
                            @endphp

                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40 transition">
                                <td class="px-5 py-4 text-center">{{ ($devices->firstItem() ?? 1) + $loop->index }}</td>
                                <td class="px-5 py-4 text-center">{{ $d->id }}</td>

                                <td class="px-5 py-4 text-left truncate font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $d->name ?? 'ROB ' . $d->id }}
                                </td>

                                <td class="px-5 py-4 text-left truncate text-zinc-600 dark:text-zinc-300">
                                    {{ $d->alias ?? '-' }}
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex items-center justify-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium border
                                        {{ $isOnline
                                            ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-300'
                                            : 'border-red-500/20 bg-red-500/10 text-red-600 dark:text-red-300' }}">
                                        <span
                                            class="inline-flex h-2 w-2 rounded-full {{ $isOnline ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                        {{ $isOnline ? 'Online' : 'Offline' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-center text-zinc-600 dark:text-zinc-300">
                                    {{ $d->last_seen ? \Illuminate\Support\Carbon::parse($d->last_seen)->timezone('Asia/Jakarta')->format('d M H:i') : '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    @if ($canManageDevices)
                                        <div class="flex items-center justify-center gap-2">
                                            <button wire:click="openEdit({{ $d->id }})"
                                                class="p-2 rounded-lg text-zinc-500 dark:text-zinc-400
                       hover:bg-zinc-100 dark:hover:bg-zinc-800
                       hover:text-blue-600 dark:hover:text-blue-400 transition cursor-pointer"
                                                title="Edit" type="button">
                                                <x-heroicon-o-pencil-square class="w-5 h-5" />
                                            </button>

                                            <button wire:click="delete({{ $d->id }})"
                                                onclick="confirm('Yakin hapus alat ini?') || event.stopImmediatePropagation()"
                                                class="p-2 rounded-lg text-zinc-500 dark:text-zinc-400
                       hover:bg-red-50 dark:hover:bg-red-500/10
                       hover:text-red-600 dark:hover:text-red-400 transition cursor-pointer"
                                                title="Hapus" type="button">
                                                <x-heroicon-o-trash class="w-5 h-5" />
                                            </button>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center">
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs
                         border border-zinc-200 dark:border-zinc-800
                         bg-zinc-50 dark:bg-zinc-900/40
                         text-zinc-500 dark:text-zinc-400">
                                                <x-heroicon-o-x-circle class="w-4 h-4" />
                                                Denied
                                            </span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-zinc-500">
                                    Tidak ada data alat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div
                class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-4
                        bg-zinc-50/70 dark:bg-zinc-900/40 border-t border-zinc-200 dark:border-zinc-800">
                <span class="text-sm text-zinc-600 dark:text-zinc-400">
                    Menampilkan
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $devices->firstItem() ?? 0 }}-{{ $devices->lastItem() ?? 0 }}
                    </span>
                    dari
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $devices->total() }}
                    </span>
                </span>

                <div>
                    {{ $devices->onEachSide(1)->links('components.pagination') }}
                </div>
            </div>
        </div>

        {{-- Modal --}}
        @if ($modalOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <button wire:click="closeModal" class="absolute inset-0 bg-black/60"></button>

                <div
                    class="relative w-full max-w-lg mx-4 rounded-2xl border border-zinc-200 dark:border-zinc-800
                            bg-white dark:bg-zinc-950 p-5 shadow-xl">

                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $editId ? 'Edit Alat' : 'Tambah Alat' }}
                            </h2>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Isi data alat lalu simpan.
                            </p>
                        </div>
                        <button wire:click="closeModal" class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            <x-heroicon-o-x-mark class="w-5 h-5 text-zinc-500" />
                        </button>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div>
                            <label class="text-sm text-zinc-600 dark:text-zinc-400">Nama</label>
                            <input wire:model.defer="name" type="text"
                                class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
                            @error('name')
                                <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm text-zinc-600 dark:text-zinc-400">Alias</label>
                            <input wire:model.defer="alias" type="text"
                                class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
                            @error('alias')
                                <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-sm text-zinc-600 dark:text-zinc-400">Latitude</label>
                                <input wire:model.defer="latitude" type="number" step="any"
                                    class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                           bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
                                @error('latitude')
                                    <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="text-sm text-zinc-600 dark:text-zinc-400">Longitude</label>
                                <input wire:model.defer="longitude" type="number" step="any"
                                    class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                           bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
                                @error('longitude')
                                    <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-sm text-zinc-600 dark:text-zinc-400">Status</label>
                                <select wire:model.defer="status"
                                    class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                           bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
                                    <option value="online">online</option>
                                    <option value="offline">offline</option>
                                </select>
                                @error('status')
                                    <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="text-sm text-zinc-600 dark:text-zinc-400">Last Seen (optional)</label>
                                <input wire:model.defer="last_seen" type="datetime-local"
                                    class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                           bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
                                @error('last_seen')
                                    <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-end gap-2">
                        <button wire:click="closeModal"
                            class="rounded-xl px-4 py-2 text-sm border border-zinc-200 dark:border-zinc-800 cursor-pointer">
                            Batal
                        </button>
                        <button wire:click="save" wire:loading.attr="disabled" wire:target="save"
                            class="rounded-xl px-4 py-2 text-sm bg-blue-600 text-white hover:bg-blue-500 cursor-pointer">
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</section>
