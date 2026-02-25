<section class="p-4 sm:p-6">
    <div class="mx-auto max-w-6xl space-y-5">

        {{-- Page Header --}}
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="p-2.5 rounded-2xl bg-blue-500/10 text-blue-400 border border-blue-500/10">
                    <x-heroicon-o-users class="w-6 h-6" />
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                        Manajemen User
                    </h1>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Kelola akun dan role pengguna sistem.
                    </p>
                </div>
            </div>

            <div class="hidden sm:flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 border border-zinc-200 dark:border-zinc-800 bg-white/60 dark:bg-zinc-950/40">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                    Live
                </span>
            </div>
        </div>

        {{-- Card --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800
                    bg-white dark:bg-zinc-950/60
                    shadow-sm dark:shadow-[0_0_0_1px_rgba(255,255,255,0.04)]
                    overflow-hidden">

            {{-- Toolbar --}}
            <div class="flex flex-col gap-3 p-4
                        bg-zinc-50/70 dark:bg-zinc-900/40
                        border-b border-zinc-200 dark:border-zinc-800">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    {{-- Search --}}
                    <div class="w-full md:max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-zinc-400" />
                            </div>

                            <input
                                wire:model.live.debounce.200ms="search"
                                type="text"
                                class="w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950
                                       text-zinc-900 dark:text-zinc-100
                                       placeholder:text-zinc-400 dark:placeholder:text-zinc-500
                                       pl-10 pr-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                                placeholder="Search nama atau email..."
                            />
                        </div>
                    </div>

                    {{-- Right actions --}}
                    <div class="w-full md:w-auto flex flex-col sm:flex-row sm:items-center justify-end gap-2">
                        <button
                            wire:click="openCreate"
                            class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-medium
                                   bg-blue-600 text-white hover:bg-blue-500
                                   focus:outline-none focus:ring-2 focus:ring-blue-500/30 cursor-pointer"
                        >
                            <x-heroicon-o-plus class="w-5 h-5" />
                            Tambah User
                        </button>

                        {{-- Per page --}}
                        <div class="relative">
                            <select
                                wire:model.live="perPage"
                                class="appearance-none w-full sm:w-[170px]
                                       rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950
                                       text-zinc-900 dark:text-zinc-100
                                       px-3 py-2 pr-10 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                            >
                                <option value="10">10 / halaman</option>
                                <option value="25">25 / halaman</option>
                                <option value="50">50 / halaman</option>
                                <option value="100">100 / halaman</option>
                            </select>

                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Helper line --}}
                <div class="flex items-center justify-between">
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        Tips: klik icon ✏️ untuk edit, 🗑️ untuk hapus.
                    </div>

                    @if (!empty($search))
                        <button
                            wire:click="$set('search','')"
                            class="text-xs inline-flex items-center gap-1 text-zinc-600 hover:text-zinc-900
                                   dark:text-zinc-400 dark:hover:text-zinc-100"
                        >
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                            Reset
                        </button>
                    @endif
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full w-full text-sm border-collapse table-fixed">
                    <thead class="text-xs uppercase bg-zinc-50 dark:bg-zinc-900/50 text-zinc-600 dark:text-zinc-400">
                        <tr>
                            <th class="w-1/5 px-5 py-3 text-center">No</th>
                            <th class="w-1/5 px-5 py-3 text-center">Aksi</th>
                            <th class="w-1/5 px-5 py-3 text-center">Nama</th>
                            <th class="w-1/5 px-5 py-3 text-center">Email</th>
                            <th class="w-1/5 px-5 py-3 text-center">Role</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($users as $u)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40 transition">
                                <td class="px-5 py-4 text-center">
                                    {{ ($users->firstItem() ?? 1) + $loop->index }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            wire:click="openEdit({{ $u->id }})"
                                            class="p-2 rounded-lg text-zinc-500 dark:text-zinc-400
                                                   hover:bg-zinc-100 dark:hover:bg-zinc-800
                                                   hover:text-blue-600 dark:hover:text-blue-400 transition cursor-pointer"
                                            title="Edit"
                                        >
                                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                                        </button>

                                        <button
                                            wire:click="delete({{ $u->id }})"
                                            onclick="confirm('Yakin hapus user ini?') || event.stopImmediatePropagation()"
                                            class="p-2 rounded-lg text-zinc-500 dark:text-zinc-400
                                                   hover:bg-red-50 dark:hover:bg-red-500/10
                                                   hover:text-red-600 dark:hover:text-red-400 transition cursor-pointer"
                                            title="Hapus"
                                        >
                                            <x-heroicon-o-trash class="w-5 h-5" />
                                        </button>
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-center truncate">{{ $u->name }}</td>
                                <td class="px-5 py-4 text-center truncate">{{ $u->email }}</td>

                                <td class="px-5 py-4 text-center">
                                    @php $role = $u->roles->first()?->name; @endphp
                                    <span class="inline-flex items-center justify-center rounded-full px-2.5 py-1 text-xs font-medium
                                                 border border-blue-500/15 bg-blue-500/10 text-blue-700 dark:text-blue-300">
                                        {{ $role ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-zinc-500">
                                    Tidak ada data user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-4
                        bg-zinc-50/70 dark:bg-zinc-900/40
                        border-t border-zinc-200 dark:border-zinc-800">

                <span class="text-sm text-zinc-600 dark:text-zinc-400">
                    Menampilkan
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }}
                    </span>
                    dari
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $users->total() }}
                    </span>
                </span>

                <div>
                    {{ $users->onEachSide(1)->links('components.pagination') }}
                </div>
            </div>
        </div>

        {{-- Flash message --}}
        @if (session('message'))
            <div class="p-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-300 text-sm">
                {{ session('message') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-3 rounded-xl border border-red-500/20 bg-red-500/10 text-red-300 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Modal --}}
        @if ($modalOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                {{-- overlay --}}
                <button wire:click="closeModal" class="absolute inset-0 bg-black/60"></button>

                {{-- panel --}}
                <div class="relative w-full max-w-lg mx-4 rounded-2xl border border-zinc-200 dark:border-zinc-800
                            bg-white dark:bg-zinc-950 p-5 shadow-xl">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $editId ? 'Edit User' : 'Tambah User' }}
                            </h2>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Isi data user lalu simpan.
                            </p>
                        </div>
                        <button wire:click="closeModal" class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            <x-heroicon-o-x-mark class="w-5 h-5 text-zinc-500" />
                        </button>
                    </div>

                    <div class="mt-4 space-y-3">
                        {{-- Name --}}
                        <div>
                            <label class="text-sm text-zinc-600 dark:text-zinc-400">Nama</label>
                            <input wire:model.defer="name" type="text"
                                class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 px-3 py-2 text-sm
                                       text-zinc-900 dark:text-zinc-100">
                            @error('name') <div class="text-xs text-red-400 mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="text-sm text-zinc-600 dark:text-zinc-400">Email</label>
                            <input wire:model.defer="email" type="email"
                                class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 px-3 py-2 text-sm
                                       text-zinc-900 dark:text-zinc-100">
                            @error('email') <div class="text-xs text-red-400 mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="text-sm text-zinc-600 dark:text-zinc-400">
                                Password {{ $editId ? '(kosongkan jika tidak diganti)' : '' }}
                            </label>
                            <input wire:model.defer="password" type="password"
                                class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 px-3 py-2 text-sm
                                       text-zinc-900 dark:text-zinc-100">
                            @error('password') <div class="text-xs text-red-400 mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- Role --}}
                        <div>
                            <label class="text-sm text-zinc-600 dark:text-zinc-400">Role</label>
                            <select wire:model.live="role"
                                class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 px-3 py-2 text-sm
                                       text-zinc-900 dark:text-zinc-100">
                                <option value="admin">admin</option>
                                <option value="operator">operator</option>
                            </select>
                            @error('role') <div class="text-xs text-red-400 mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- Device khusus operator --}}
                        @if($role === 'admin')
                            <div>
                                <label class="text-sm text-zinc-600 dark:text-zinc-400">
                                    Alat untuk Operator
                                </label>

                                <select wire:model.defer="operator_device_id"
                                    class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                           bg-white dark:bg-zinc-950 px-3 py-2 text-sm
                                           text-zinc-900 dark:text-zinc-100">
                                    <option value="">-- pilih alat --</option>

                                    @foreach($devices as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>

                                @error('operator_device_id')
                                    <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 flex items-center justify-end gap-2">
                        <button wire:click="closeModal"
                            class="rounded-xl px-4 py-2 text-sm border border-zinc-200 dark:border-zinc-800 cursor-pointer">
                            Batal
                        </button>
                        <button wire:click="save"
                            class="rounded-xl px-4 py-2 text-sm bg-blue-600 text-white hover:bg-blue-500 cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</section>
