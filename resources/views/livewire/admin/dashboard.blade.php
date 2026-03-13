<div wire:key="dashboard-root">
    @if (!$modalOpen)
        <div wire:poll.2s="fetchData"></div>
    @endif

    @php
        $deviceOptions = collect($devices ?? [])
            ->map(function ($d) use ($deviceStatus) {
                $dst = ($deviceStatus ?? [])[$d['id']] ?? null;
                $online = (bool) ($dst['online'] ?? false);

                return [
                    'id' => $d['id'],
                    'name' => $d['name'] ?? null,
                    'alias' => $d['alias'] ?? null,
                    'label' => $d['label'] ?? ($d['alias'] ?? ($d['name'] ?? 'ROB ' . $d['id'])),
                    'statusLabel' => $online ? 'Online' : 'Offline',
                    'online' => $online,
                ];
            })
            ->values()
            ->toArray();

        $deviceName =
            $currentDevice['label'] ??
            ($currentDevice['alias'] ?? ($currentDevice['name'] ?? 'ROB ' . ($selectedDeviceId ?? '-')));

        $st = ($deviceStatus ?? [])[$selectedDeviceId] ?? null;
        $isOnline = (bool) ($st['online'] ?? false);
    @endphp


    <div x-data="dashboard(@js($theme))" x-init="init()" class="flex-1 w-full py-4 px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- ===== TOP TOOLBAR ===== --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-3 sm:p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                {{-- LEFT --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                    <div class="w-full sm:w-auto">
                        <span
                            class="inline-flex w-full sm:w-auto items-center gap-2 rounded-xl px-3 py-2 text-sm
                           border border-zinc-200 dark:border-zinc-800
                           bg-zinc-50 dark:bg-zinc-900/40">
                            <span
                                class="inline-flex h-2.5 w-2.5 rounded-full {{ $isOnline ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>
                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $deviceName }}</span>
                            <span class="text-xs text-zinc-500">{{ $isOnline ? 'Online' : 'Offline' }}</span>
                        </span>
                    </div>

                    @if (count($devices) > 1)
                        <div class="w-full sm:w-auto" x-data="searchableDeviceSelect({
                            selected: @entangle('selectedDeviceId').live,
                            options: {{ \Illuminate\Support\Js::from($deviceOptions) }}
                        })" @click.outside="close()">

                            <div class="relative min-w-[220px]">
                                <button type="button" @click="toggle()"
                                    class="w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                       bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100
                       px-3 py-2.5 text-sm flex items-center justify-between gap-3 cursor-pointer">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="inline-flex h-2.5 w-2.5 rounded-full"
                                            :class="selectedOption?.online ? 'bg-emerald-500' : 'bg-zinc-400'"></span>

                                        <span class="truncate" x-text="selectedOption?.label || 'Pilih device'"></span>
                                    </div>

                                    <x-heroicon-o-chevron-up-down class="w-4 h-4 text-zinc-400 shrink-0" />
                                </button>

                                <div x-show="open" x-transition
                                    class="absolute z-50 mt-2 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                       bg-white dark:bg-zinc-950 shadow-lg overflow-hidden"
                                    style="display: none;">
                                    <div class="p-2 border-b border-zinc-200 dark:border-zinc-800">
                                        <input type="text" x-ref="searchInput" x-model="query"
                                            placeholder="Cari device..."
                                            class="w-full rounded-lg border border-zinc-200 dark:border-zinc-800
                               bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100
                               px-3 py-2 text-sm outline-none">
                                    </div>

                                    <div class="max-h-64 overflow-y-auto">
                                        <template x-if="filteredOptions.length === 0">
                                            <div class="px-3 py-3 text-sm text-zinc-500">
                                                Device tidak ditemukan
                                            </div>
                                        </template>

                                        <template x-for="item in filteredOptions" :key="item.id">
                                            <button type="button" @click="select(item)"
                                                class="w-full px-3 py-2.5 text-left hover:bg-zinc-100 dark:hover:bg-zinc-900
                                   flex items-center justify-between gap-3 text-sm cursor-pointer">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="inline-flex h-2.5 w-2.5 rounded-full shrink-0"
                                                        :class="item.online ? 'bg-emerald-500' : 'bg-zinc-400'"></span>

                                                    <span class="truncate text-zinc-900 dark:text-zinc-100"
                                                        x-text="item.label"></span>
                                                </div>

                                                <span class="text-xs shrink-0"
                                                    :class="item.online ? 'text-emerald-500' : 'text-zinc-400'"
                                                    x-text="item.statusLabel"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- RIGHT --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 lg:flex lg:flex-wrap lg:items-center lg:justify-end">
                    <div class="inline-flex items-center justify-center gap-2 rounded-xl border px-3 py-2.5 sm:py-2 text-sm sm:text-xs min-h-[44px]"
                        x-bind:class="[(riskStyles?.bg || 'bg-zinc-50 dark:bg-zinc-900/40'), (riskStyles?.border ||
                            'border-zinc-200 dark:border-zinc-800')].join(' ')">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4"
                            x-bind:class="riskStyles?.text || 'text-zinc-500'" />
                        <span class="text-zinc-500 dark:text-zinc-400">Status:</span>
                        <span class="font-semibold"
                            x-bind:class="riskStyles?.text || 'text-zinc-700 dark:text-zinc-200'"
                            x-text="risk || 'Normal'"></span>
                    </div>

                    <button type="button" wire:click="openSettings"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-200 dark:border-zinc-800
                       bg-white dark:bg-zinc-900 px-3 py-2.5 sm:py-2 text-sm sm:text-xs min-h-[44px]
                       text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition cursor-pointer">
                        <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                        Settings
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== METRIC CARDS ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

            @php
                $cards = $visibleSensors ?: ['suhu', 'kelembapan', 'ketinggian_air'];
            @endphp

            @foreach ($cards as $metric)
                @continue(!array_key_exists($metric, $metricLabels))

                @php
                    $cardMap = [
                        'suhu' => [
                            'label' => 'Temperature',
                            'unit' => '°C',
                            'bg' => 'from-orange-500/20 via-orange-500/10 to-transparent',
                            'border' => 'border-orange-500/40',
                            'text' => 'text-orange-500',
                            'icon' => 'fire',
                            'valueKey' => 'suhu',
                        ],

                        'kelembapan' => [
                            'label' => 'Kelembapan',
                            'unit' => '%',
                            'bg' => 'from-cyan-500/20 via-cyan-500/10 to-transparent',
                            'border' => 'border-cyan-500/40',
                            'text' => 'text-cyan-500',
                            'icon' => 'beaker',
                            'valueKey' => 'kelembapan',
                        ],

                        'tekanan_udara' => [
                            'label' => 'Tekanan Udara',
                            'unit' => 'hPa',
                            'bg' => 'from-emerald-500/20 via-emerald-500/10 to-transparent',
                            'border' => 'border-emerald-500/40',
                            'text' => 'text-emerald-500',
                            'icon' => 'cloud',
                            'valueKey' => 'tekanan_udara',
                        ],

                        'kecepatan_angin' => [
                            'label' => 'Kecepatan Angin',
                            'unit' => 'm/s',
                            'bg' => 'from-amber-500/20 via-amber-500/10 to-transparent',
                            'border' => 'border-amber-500/40',
                            'text' => 'text-amber-500',
                            'icon' => 'flag',
                            'valueKey' => 'kecepatan_angin',
                        ],

                        'arah_angin' => [
                            'label' => 'Arah Angin',
                            'unit' => '°',
                            'bg' => 'from-purple-500/20 via-purple-500/10 to-transparent',
                            'border' => 'border-purple-500/40',
                            'text' => 'text-purple-500',
                            'icon' => 'arrow-path-rounded-square',
                            'valueKey' => 'arah_angin',
                        ],

                        'ketinggian_air' => [
                            'label' => 'Ketinggian Air',
                            'unit' => 'cm',
                            'bg' => 'from-sky-500/20 via-sky-500/10 to-transparent',
                            'border' => 'border-sky-500/40',
                            'text' => 'text-sky-500',
                            'icon' => 'arrow-trending-up',
                            'valueKey' => 'ketinggian_air',
                        ],
                    ];

                    $c = $cardMap[$metric];
                @endphp


                <div wire:click="openMetric('{{ $metric }}')"
                    class="
relative overflow-hidden rounded-xl
border border-zinc-200 dark:border-zinc-800
bg-white dark:bg-zinc-900
shadow-sm dark:shadow-none
p-6
cursor-pointer
hover:scale-[1.02]
transition
">

                    <div class="absolute inset-0 bg-gradient-to-br {{ $c['bg'] }}"></div>

                    <div class="relative z-10">

                        <div class="flex justify-between items-center">

                            <p class="text-sm font-medium {{ $c['text'] }}">
                                {{ $c['label'] }}
                            </p>

                            @if ($c['icon'] === 'fire')
                                <x-heroicon-o-fire class="w-5 h-5 text-orange-500" />
                            @elseif($c['icon'] === 'beaker')
                                <x-heroicon-o-beaker class="w-5 h-5 text-cyan-500" />
                            @elseif($c['icon'] === 'cloud')
                                <x-heroicon-o-cloud class="w-5 h-5 text-emerald-500" />
                            @elseif($c['icon'] === 'flag')
                                <x-heroicon-o-flag class="w-5 h-5 text-amber-500" />
                            @elseif($c['icon'] === 'arrow-path-rounded-square')
                                <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5 text-purple-500" />
                            @else
                                <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-sky-500" />
                            @endif

                        </div>


                        @if ($metric === 'arah_angin')
                            <div class="flex justify-between items-end mt-4">

                                <h2 class="text-3xl font-semibold text-zinc-900 dark:text-white">
                                    <span x-text="data.arah_angin ?? '--'"></span>°
                                </h2>

                                <span class="text-lg font-medium text-purple-500" x-text="data.arah_angin_label ?? '-'">
                                </span>

                            </div>
                        @else
                            <h2 class="text-3xl font-semibold text-zinc-900 dark:text-white mt-3">

                                <span x-text="data.{{ $c['valueKey'] }} ?? '--'"></span>

                                {{ $c['unit'] }}

                            </h2>
                        @endif

                    </div>

                </div>
            @endforeach
        </div>

        {{-- ===== CHART ===== --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Trend Sensor</h3>

                <div class="flex flex-wrap items-center gap-2">
                    <select wire:model.live="chartMetric"
                        class="appearance-none text-xs rounded-xl border border-zinc-200 dark:border-zinc-800
                               bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 cursor-pointer">
                        @foreach ($metricLabels as $k => $lbl)
                            <option value="{{ $k }}">{{ $lbl }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="selectedTimeRange"
                        class="appearance-none text-xs rounded-xl border border-zinc-200 dark:border-zinc-800
                               bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 cursor-pointer">
                        @foreach ($timeRanges as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>

                </div>
            </div>

            <div wire:ignore class="relative h-[320px] w-full">
                <canvas x-ref="waterChart" class="absolute inset-0 w-full h-full"></canvas>
            </div>
        </div>

        {{-- ===== SETTINGS MODAL ===== --}}
        @if ($showSettings)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <button class="absolute inset-0 bg-black/50 cursor-pointer" wire:click="closeSettings"></button>

                <div
                    class="relative w-[95%] max-w-2xl rounded-2xl border border-zinc-200 dark:border-zinc-800
                           bg-white dark:bg-zinc-950 p-5 shadow-xl">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Dashboard Settings</h2>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                @if ($canManageDevices)
                                    Admin: pilih device + atur sensor yang tampil & chart.
                                @else
                                    Operator: pilih device (yang diberikan admin) + atur sensor & chart.
                                @endif
                            </p>
                        </div>

                        <button wire:click="closeSettings"
                            class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            <x-heroicon-o-x-mark class="w-5 h-5 text-zinc-500" />
                        </button>
                    </div>

                    <div class="mt-4 space-y-4">
                        @if (count($devices) > 1 || !$canManageDevices)
                            <div>
                                <div class="text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-2">
                                    Pilih Alat (Device)
                                </div>

                                <div x-data="searchableDeviceSelect({
                                    selected: @entangle('selectedDeviceId').live,
                                    options: {{ \Illuminate\Support\Js::from($deviceOptions) }}
                                })" @click.outside="close()" class="relative">

                                    <button type="button" @click="toggle()"
                                        class="w-full rounded-xl border border-zinc-200 dark:border-zinc-800
               bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100
               flex items-center justify-between gap-3 cursor-pointer">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="inline-flex h-2.5 w-2.5 rounded-full"
                                                :class="selectedOption?.online ? 'bg-emerald-500' : 'bg-zinc-400'"></span>
                                            <span class="truncate"
                                                x-text="selectedOption?.label || 'Pilih device'"></span>
                                        </div>

                                        <x-heroicon-o-chevron-up-down class="w-4 h-4 text-zinc-400 shrink-0" />
                                    </button>

                                    <div x-show="open" x-transition
                                        class="absolute z-50 mt-2 w-full rounded-xl border border-zinc-200 dark:border-zinc-800
               bg-white dark:bg-zinc-950 shadow-lg overflow-hidden"
                                        style="display: none;">
                                        <div class="p-2 border-b border-zinc-200 dark:border-zinc-800">
                                            <input type="text" x-ref="searchInput" x-model="query"
                                                placeholder="Cari device..."
                                                class="w-full rounded-lg border border-zinc-200 dark:border-zinc-800
                       bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100
                       px-3 py-2 text-sm outline-none">
                                        </div>

                                        <div class="max-h-64 overflow-y-auto">
                                            <template x-if="filteredOptions.length === 0">
                                                <div class="px-3 py-3 text-sm text-zinc-500">
                                                    Device tidak ditemukan
                                                </div>
                                            </template>

                                            <template x-for="item in filteredOptions" :key="item.id">
                                                <button type="button" @click="select(item)"
                                                    class="w-full px-3 py-2.5 text-left hover:bg-zinc-100 dark:hover:bg-zinc-900
                           flex items-center justify-between gap-3 text-sm cursor-pointer">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <span class="inline-flex h-2.5 w-2.5 rounded-full shrink-0"
                                                            :class="item.online ? 'bg-emerald-500' : 'bg-zinc-400'"></span>

                                                        <span class="truncate text-zinc-900 dark:text-zinc-100"
                                                            x-text="item.label"></span>
                                                    </div>

                                                    <span class="text-xs shrink-0"
                                                        :class="item.online ? 'text-emerald-500' : 'text-zinc-400'"
                                                        x-text="item.statusLabel"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                @if (empty($devices))
                                    <div class="text-xs text-red-500 mt-2">
                                        Belum ada alat yang diberikan admin.
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div>
                            <div class="text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-2">
                                Sensor yang ditampilkan (kartu)
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach ($metricLabels as $key => $label)
                                    <label
                                        class="flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-zinc-800
                                               px-3 py-2 bg-zinc-50 dark:bg-zinc-900/40">
                                        <input type="checkbox" value="{{ $key }}"
                                            wire:model.live="visibleSensors"
                                            class="rounded border-zinc-300 dark:border-zinc-700">
                                        <span
                                            class="text-sm text-zinc-800 dark:text-zinc-100">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-2">
                                Tip: centang 2-6 sensor biar dashboard tetap rapi.
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between 
rounded-lg border border-zinc-200 dark:border-zinc-700 px-3 py-2">

                            <span class="text-sm text-zinc-700 dark:text-zinc-200">
                                Theme
                            </span>

                            <button type="button" x-on:click="
toggleTheme();
$wire.set('theme', theme);
"
                                class="px-3 py-1.5 text-sm rounded-lg
bg-zinc-200 dark:bg-zinc-800
text-zinc-800 dark:text-zinc-100 cursor-pointer grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3r">

                                <span x-text="theme === 'dark' ? 'Dark Mode' : 'Light Mode'"></span>

                            </button>

                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <div class="text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-2">
                                    Sensor Trend (Chart)
                                </div>
                                <select wire:model.live="chartMetric"
                                    class="appearance-none w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                           bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
                                    @foreach ($metricLabels as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-2">
                                    Range Default
                                </div>
                                <select wire:model.live="selectedTimeRange"
                                    class="appearance-none w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                           bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
                                    @foreach ($timeRanges as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-end gap-2">
                        <button wire:click="closeSettings"
                            class="cursor-pointer rounded-xl px-4 py-2 text-sm border border-zinc-200 dark:border-zinc-800">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- ===== MODAL METRIC ===== --}}
        @if ($modalOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center" x-data x-init="$nextTick(() => window.flushMetricChartPending?.())">
                <div class="absolute inset-0 bg-black/40" wire:click="closeModal"></div>

                <div
                    class="relative bg-white dark:bg-zinc-950 w-[95%] max-w-3xl rounded-xl border border-zinc-200 dark:border-zinc-800 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Detail Trend</h3>

                        <div class="flex items-center gap-2">
                            <select wire:model.live="modalTimeRange"
                                class="text-xs rounded-xl border border-zinc-200 dark:border-zinc-800
                               bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2">
                                @foreach ($timeRanges as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>

                            <button class="text-sm px-3 py-1 rounded border cursor-pointer" wire:click="closeModal">
                                x
                            </button>
                        </div>
                    </div>

                    <div wire:poll.2s="pollMetric"></div>

                    <div class="relative w-full h-[360px]" wire:ignore>
                        <canvas id="metricChart"></canvas>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
