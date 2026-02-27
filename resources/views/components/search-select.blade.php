@php
    // contoh $deviceOptions dikirim dari Livewire render()
    // $deviceOptions = [
    //   ['value'=>'71','label'=>'ROB 71'],
    //   ...
    // ];
@endphp

<div
    x-data="searchSelect({
        options: @js($deviceOptions),
        value: @entangle('selectedDeviceId').live,
        placeholder: 'Pilih alat...',
        searchPlaceholder: 'Cari alat...',
        disabled: @js(!$canManageDevices),
    })"
    class="w-full md:w-[260px]"
>
    {{-- Trigger --}}
    <button
        type="button"
        class="w-full inline-flex items-center justify-between gap-2 rounded-xl
               border border-zinc-200 dark:border-zinc-800
               bg-white/70 dark:bg-zinc-950/50
               px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100
               hover:bg-zinc-50 dark:hover:bg-zinc-900 transition"
        :class="disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'"
        @click="toggle()"
        @keydown.enter.prevent="toggle()"
        @keydown.arrow-down.prevent="openAndFocus()"
        :disabled="disabled"
    >
        <span class="truncate" x-text="selectedLabel()"></span>

        <svg class="w-4 h-4 text-zinc-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/>
        </svg>
    </button>

    {{-- Panel --}}
    <div
        x-show="open"
        x-transition.origin.top
        @click.outside="close()"
        class="absolute z-50 mt-2 w-full overflow-hidden rounded-xl
               border border-zinc-200 dark:border-zinc-800
               bg-white dark:bg-zinc-950 shadow-lg"
        style="display:none"
    >
        <div class="p-2 border-b border-zinc-200 dark:border-zinc-800">
            <input
                x-ref="search"
                type="text"
                x-model="query"
                :placeholder="searchPlaceholder"
                class="w-full rounded-lg border border-zinc-200 dark:border-zinc-800
                       bg-white dark:bg-zinc-950 px-3 py-2 text-sm
                       text-zinc-900 dark:text-zinc-100
                       focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                @keydown.arrow-down.prevent="highlightNext()"
                @keydown.arrow-up.prevent="highlightPrev()"
                @keydown.enter.prevent="chooseHighlighted()"
                @keydown.esc.prevent="close()"
            />
        </div>

        <ul x-ref="list" class="max-h-60 overflow-auto py-1">
            <template x-for="(opt, idx) in filteredOptions()" :key="String(opt.value)">
                <li>
                    <button
                        type="button"
                        data-opt
                        class="w-full text-left px-3 py-2 text-sm flex items-center justify-between gap-2
                               hover:bg-zinc-50 dark:hover:bg-zinc-900 transition"
                        :class="idx === highlighted ? 'bg-zinc-50 dark:bg-zinc-900' : ''"
                        @mouseenter="highlighted = idx"
                        @click="select(opt.value)"
                    >
                        <span class="truncate" x-text="opt.label"></span>
                        <span class="text-xs text-zinc-400" x-show="String(opt.value) === String(value)">✓</span>
                    </button>
                </li>
            </template>

            <li x-show="filteredOptions().length === 0" class="px-3 py-3 text-sm text-zinc-500">
                Tidak ada hasil.
            </li>
        </ul>
    </div>
</div>
