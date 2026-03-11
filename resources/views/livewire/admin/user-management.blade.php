<section class="p-3 sm:p-4 md:p-6">
    <div class="mx-auto space-y-4 sm:space-y-5">

        {{-- Page Header --}}
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3 min-w-0">
                <div class="shrink-0 p-2.5 rounded-2xl bg-blue-500/10 text-blue-400 border border-blue-500/10">
                    <x-heroicon-o-users class="w-5 h-5 sm:w-6 sm:h-6" />
                </div>
                <div class="min-w-0">
                    <h1 class="text-lg sm:text-xl md:text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                        Manajemen User
                    </h1>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Kelola akun dan role pengguna sistem.
                    </p>
                </div>
            </div>
        </div>

        {{-- Flash message --}}
        @if (session('success'))
            <div class="p-3 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-3 rounded-xl border border-red-500/20 bg-red-500/10 text-red-700 dark:text-red-300 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Card --}}
        <div
            class="rounded-2xl border border-zinc-200 dark:border-zinc-800
                   bg-white dark:bg-zinc-950/60
                   shadow-sm dark:shadow-[0_0_0_1px_rgba(255,255,255,0.04)]
                   overflow-hidden">

            {{-- Toolbar --}}
            <div
                class="flex flex-col gap-3 p-3 sm:p-4
                       bg-zinc-50/70 dark:bg-zinc-900/40
                       border-b border-zinc-200 dark:border-zinc-800">

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    {{-- Search --}}
                    <div class="w-full lg:max-w-md">
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
                                       pl-10 pr-3 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                                placeholder="Search nama atau email..." />
                        </div>
                    </div>

                    {{-- Right actions --}}
                    <div class="w-full lg:w-auto flex flex-col sm:flex-row sm:items-center justify-end gap-2">
                        <button
                            wire:click="openCreate"
                            class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-medium
                                   bg-blue-600 text-white hover:bg-blue-500
                                   focus:outline-none focus:ring-2 focus:ring-blue-500/30 cursor-pointer">
                            <x-heroicon-o-plus class="w-5 h-5" />
                            Tambah User
                        </button>

                        {{-- Per page --}}
                        <div class="relative w-full sm:w-[170px]">
                            <select
                                wire:model.live="perPage"
                                class="appearance-none w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950
                                       text-zinc-900 dark:text-zinc-100
                                       px-3 py-2.5 pr-10 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500/30">
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
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        Tips: klik icon ✏️ untuk edit, 🗑️ untuk hapus.
                    </div>

                    @if (!empty($search))
                        <button
                            wire:click="$set('search','')"
                            class="text-xs inline-flex items-center gap-1 text-zinc-600 hover:text-zinc-900
                                   dark:text-zinc-400 dark:hover:text-zinc-100">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                            Reset
                        </button>
                    @endif
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-[760px] w-full text-sm border-collapse table-fixed">
                    <thead class="text-[11px] sm:text-xs uppercase bg-zinc-50 dark:bg-zinc-900/50 text-zinc-600 dark:text-zinc-400">
                        <tr>
                            <th class="w-[70px] px-3 sm:px-5 py-3 text-center">No</th>
                            <th class="w-[180px] px-3 sm:px-5 py-3 text-center">Nama</th>
                            <th class="w-[240px] px-3 sm:px-5 py-3 text-center">Email</th>
                            <th class="w-[140px] px-3 sm:px-5 py-3 text-center">Role</th>
                            <th class="w-[120px] px-3 sm:px-5 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($users as $u)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40 transition">
                                <td class="px-3 sm:px-5 py-3 sm:py-4 text-center whitespace-nowrap">
                                    {{ ($users->firstItem() ?? 1) + $loop->index }}
                                </td>

                                <td class="px-3 sm:px-5 py-3 sm:py-4 text-center">
                                    <div class="truncate text-zinc-900 dark:text-zinc-100 font-medium">
                                        {{ $u->name }}
                                    </div>
                                </td>

                                <td class="px-3 sm:px-5 py-3 sm:py-4 text-center">
                                    <div class="truncate text-zinc-600 dark:text-zinc-300">
                                        {{ $u->email }}
                                    </div>
                                </td>

                                <td class="px-3 sm:px-5 py-3 sm:py-4 text-center">
                                    @php $role = $u->roles->first()?->name; @endphp
                                    <span
                                        class="inline-flex items-center justify-center
                                               rounded-full px-2.5 py-1 text-[11px] sm:text-xs font-medium
                                               border border-blue-500/15
                                               bg-blue-500/10 text-blue-700 dark:text-blue-300">
                                        {{ $role ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-3 sm:px-5 py-3 sm:py-4">
                                    <div class="flex items-center justify-center gap-1 sm:gap-2">
                                        <button
                                            wire:click="openEdit({{ $u->id }})"
                                            class="p-2 rounded-lg text-zinc-500 dark:text-zinc-400
                                                   hover:bg-zinc-100 dark:hover:bg-zinc-800
                                                   hover:text-blue-600 dark:hover:text-blue-400 transition cursor-pointer"
                                            title="Edit"
                                            type="button">
                                            <x-heroicon-o-pencil-square class="w-4 h-4 sm:w-5 sm:h-5" />
                                        </button>

                                        <button
                                            wire:click="delete({{ $u->id }})"
                                            onclick="confirm('Yakin hapus user ini?') || event.stopImmediatePropagation()"
                                            class="p-2 rounded-lg text-zinc-500 dark:text-zinc-400
                                                   hover:bg-red-50 dark:hover:bg-red-500/10
                                                   hover:text-red-600 dark:hover:text-red-400 transition cursor-pointer"
                                            title="Hapus"
                                            type="button">
                                            <x-heroicon-o-trash class="w-4 h-4 sm:w-5 sm:h-5" />
                                        </button>
                                    </div>
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
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 sm:p-4
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

                <div class="overflow-x-auto">
                    {{ $users->onEachSide(1)->links('components.pagination') }}
                </div>
            </div>
        </div>

        {{-- Modal --}}
        @if ($modalOpen)
            <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
                {{-- overlay --}}
                <button wire:click="closeModal" class="absolute inset-0 bg-black/60"></button>

                {{-- panel --}}
                <div
                    class="relative w-full sm:max-w-lg mx-0 sm:mx-4 rounded-t-2xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800
                           bg-white dark:bg-zinc-950 p-4 sm:p-5 shadow-xl max-h-[90vh] overflow-y-auto">

                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $editId ? 'Edit User' : 'Tambah User' }}
                            </h2>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Isi data user lalu simpan.
                            </p>
                        </div>

                        <button
                            wire:click="closeModal"
                            class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            <x-heroicon-o-x-mark class="w-5 h-5 text-zinc-500" />
                        </button>
                    </div>

                    <div class="mt-4 space-y-3">
                        {{-- Name --}}
                        <div>
                            <label class="text-sm text-zinc-600 dark:text-zinc-400">Nama</label>
                            <input
                                wire:model.defer="name"
                                type="text"
                                class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm
                                       text-zinc-900 dark:text-zinc-100">
                            @error('name')
                                <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="text-sm text-zinc-600 dark:text-zinc-400">Email</label>
                            <input
                                wire:model.defer="email"
                                type="email"
                                class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm
                                       text-zinc-900 dark:text-zinc-100">
                            @error('email')
                                <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="text-sm text-zinc-600 dark:text-zinc-400">
                                Password {{ $editId ? '(kosongkan jika tidak diganti)' : '' }}
                            </label>

                            <input
                                wire:model.defer="password"
                                type="password"
                                class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm
                                       text-zinc-900 dark:text-zinc-100">

                            @error('password')
                                <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div>
                            <label class="text-sm text-zinc-600 dark:text-zinc-400">Role</label>
                            <select
                                wire:model.defer="role"
                                class="appearance-none mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm
                                       text-zinc-900 dark:text-zinc-100">
                                <option value="admin">admin</option>
                                <option value="operator">operator</option>
                            </select>
                            @error('role')
                                <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($role === 'operator')
                            <div
                                class="mt-3"
                                x-data="{
                                    adding: false,

                                    addDevice(val) {
                                        val = parseInt(val);
                                        if (!val) return;

                                        if (!Array.isArray($wire.operator_device_ids)) $wire.operator_device_ids = [];

                                        if (!$wire.operator_device_ids.includes(val)) {
                                            $wire.operator_device_ids = [...$wire.operator_device_ids, val];
                                        }

                                        this.adding = false;
                                    },

                                    removeDevice(val) {
                                        val = parseInt(val);
                                        $wire.operator_device_ids = ($wire.operator_device_ids || []).filter(v => parseInt(v) !== val);
                                    },

                                    labelOf(val) {
                                        val = parseInt(val);
                                        const found = (this.allDevices || []).find(d => parseInt(d.value) === val);
                                        return found ? found.label : ('Device ' + val);
                                    },

                                    allDevices: @js($devices),

                                    availableOptions() {
                                        const chosen = new Set(($wire.operator_device_ids || []).map(v => String(v)));
                                        return (this.allDevices || []).filter(d => !chosen.has(String(d.value)));
                                    },
                                }">

                                <label class="text-sm text-zinc-600 dark:text-zinc-400">Alat Operator</label>

                                {{-- Chips selected --}}
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <template x-for="val in ($wire.operator_device_ids || [])" :key="val">
                                        <span
                                            class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs
                                                   border border-zinc-200 dark:border-zinc-800
                                                   bg-zinc-50 dark:bg-zinc-900 text-zinc-700 dark:text-zinc-200">
                                            <span x-text="labelOf(val)"></span>
                                            <button
                                                type="button"
                                                @click="removeDevice(val)"
                                                class="text-zinc-400 hover:text-red-500">
                                                ✕
                                            </button>
                                        </span>
                                    </template>

                                    <template x-if="($wire.operator_device_ids || []).length === 0">
                                        <span class="text-xs text-zinc-500">Belum ada alat dipilih.</span>
                                    </template>
                                </div>

                                {{-- tombol tambah --}}
                                <button
                                    type="button"
                                    @click="adding = !adding"
                                    class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl px-3 py-2.5 text-sm
                                           border border-zinc-200 dark:border-zinc-800
                                           bg-white dark:bg-zinc-950 text-zinc-700 dark:text-zinc-200
                                           hover:bg-zinc-50 dark:hover:bg-zinc-900 cursor-pointer">
                                    <span x-text="adding ? 'Tutup' : '+ More device'"></span>
                                </button>

                                {{-- Dropdown add device --}}
                                <div
                                    x-show="adding"
                                    x-transition
                                    class="mt-3"
                                    x-data="searchSelect({
                                        getOptions: () => availableOptions(),
                                        value: null,
                                        placeholder: 'Pilih alat...',
                                        searchPlaceholder: 'Cari alat...'
                                    })"
                                    x-init="$watch('value', (v) => {
                                        if (v) {
                                            addDevice(v);
                                            $nextTick(() => { value = null })
                                        }
                                    })">

                                    {{-- button --}}
                                    <button
                                        type="button"
                                        @click="toggle()"
                                        class="mt-1 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                               bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm
                                               text-zinc-900 dark:text-zinc-100 flex items-center justify-between">
                                        <span class="truncate" x-text="selectedLabel()"></span>
                                        <span class="text-zinc-400">▾</span>
                                    </button>

                                    {{-- dropdown --}}
                                    <div
                                        x-show="isOpen"
                                        @click.outside="close()"
                                        x-transition
                                        class="mt-2 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                               bg-white dark:bg-zinc-950 shadow-lg overflow-hidden">

                                        <div class="p-2 border-b border-zinc-200 dark:border-zinc-800">
                                            <input
                                                x-ref="search"
                                                type="text"
                                                x-model="query"
                                                :placeholder="searchPlaceholder"
                                                class="w-full rounded-lg border border-zinc-200 dark:border-zinc-800
                                                       bg-white dark:bg-zinc-950 px-3 py-2 text-sm
                                                       text-zinc-900 dark:text-zinc-100 focus:outline-none" />
                                        </div>

                                        <div class="max-h-56 overflow-auto" x-ref="list">
                                            <template x-for="(opt, idx) in filteredOptions()" :key="opt.value">
                                                <button
                                                    type="button"
                                                    data-opt
                                                    @click="select(opt.value)"
                                                    class="w-full text-left px-3 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-900
                                                           flex items-center justify-between">
                                                    <span x-text="opt.label"></span>
                                                </button>
                                            </template>

                                            <div
                                                x-show="filteredOptions().length === 0"
                                                class="px-3 py-3 text-sm text-zinc-500">
                                                Tidak ada hasil.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @error('operator_device_ids')
                                    <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                                @enderror

                                @error('operator_device_ids.*')
                                    <div class="text-xs text-red-400 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-2 sm:flex sm:items-center sm:justify-end">
                        <button
                            wire:click="closeModal"
                            class="rounded-xl px-4 py-2.5 text-sm border border-zinc-200 dark:border-zinc-800 cursor-pointer">
                            Batal
                        </button>

                        <button
                            wire:click="save"
                            class="rounded-xl px-4 py-2.5 text-sm bg-blue-600 text-white hover:bg-blue-500 cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</section>