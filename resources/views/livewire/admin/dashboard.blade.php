<div wire:key="dashboard-root">
    @if (!$modalOpen)
        <div wire:poll.2s="fetchData"></div>
        <div wire:poll.2s="refreshMainChart"></div>
        <div wire:poll.3s="refreshStatusesOnly"></div>
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
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between lg:gap-8">

                {{-- LEFT CARD --}}
                <div
                    class="lg:shrink-0 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-4 py-3">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                        {{-- Active device --}}
                        <div class="inline-flex items-center gap-2">
                            <span
                                class="inline-flex h-2.5 w-2.5 rounded-full {{ $isOnline ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>

                            <div class="flex flex-col leading-tight">
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $deviceName }}
                                </span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $isOnline ? 'Online' : 'Offline' }}
                                </span>
                            </div>
                        </div>

                        @if (count($devices) > 1)
                            <div class="hidden sm:block w-px h-6 bg-zinc-200 dark:bg-zinc-800"></div>

                            <div x-data="searchableDeviceSelect({
                                selected: @entangle('selectedDeviceId').live,
                                options: {{ \Illuminate\Support\Js::from($deviceOptions) }}
                            })" @click.outside="close()" class="relative w-full sm:w-auto">
                                <button type="button" @click="toggle()"
                                    class="inline-flex w-full sm:w-auto min-w-[180px] items-center justify-between gap-2 text-sm text-zinc-700 dark:text-zinc-200">
                                    <span class="truncate" x-text="selectedOption?.label || 'Pilih device'"></span>
                                    <x-heroicon-o-chevron-up-down class="w-4 h-4 text-zinc-400" />
                                </button>

                                <div x-show="open" x-transition
                                    class="absolute z-50 mt-2 w-full rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 shadow-lg overflow-hidden"
                                    style="display: none;">
                                    <div class="p-2 border-b border-zinc-200 dark:border-zinc-800">
                                        <input type="text" x-model="query" placeholder="Cari device..."
                                            class="w-full rounded-lg bg-zinc-50 dark:bg-zinc-900 px-3 py-2 text-sm outline-none border-0 text-zinc-900 dark:text-zinc-100">
                                    </div>

                                    <div class="max-h-64 overflow-y-auto">
                                        <template x-for="item in filteredOptions" :key="item.id">
                                            <button type="button" @click="select(item)"
                                                class="w-full px-3 py-2.5 text-left hover:bg-zinc-50 dark:hover:bg-zinc-900 flex items-center justify-between text-sm">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="inline-flex h-2 w-2 rounded-full"
                                                        :class="item.online ? 'bg-emerald-500' : 'bg-zinc-400'"></span>

                                                    <span class="truncate text-zinc-900 dark:text-zinc-100"
                                                        x-text="item.label"></span>
                                                </div>

                                                <span class="text-xs"
                                                    :class="item.online ? 'text-emerald-500' : 'text-zinc-400'"
                                                    x-text="item.statusLabel"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- RIGHT CARD --}}
                <div
                    class="lg:shrink-0 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-4 py-3">
                    <div class="flex items-center gap-3 lg:justify-end">

                        <div class="inline-flex items-center gap-1.5 text-sm"
                            x-bind:class="riskStyles?.text || 'text-zinc-600 dark:text-zinc-300'">
                            <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                            <span class="font-medium" x-text="risk || 'AMAN'"></span>
                        </div>

                        <div class="w-px h-5 bg-zinc-200 dark:bg-zinc-800"></div>

                        <button type="button" wire:click="openSettings"
                            class="inline-flex items-center gap-1.5 text-sm text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition cursor-pointer">
                            <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                            <span class="hidden sm:inline">Settings</span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- ===== METRIC CARDS ===== --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
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
                                'soft' => 'bg-orange-500/10',
                                'text' => 'text-orange-500',
                                'icon' => 'fire',
                                'valueKey' => 'suhu',
                            ],

                            'kelembapan' => [
                                'label' => 'Kelembapan',
                                'unit' => '%',
                                'soft' => 'bg-cyan-500/10',
                                'text' => 'text-cyan-500',
                                'icon' => 'beaker',
                                'valueKey' => 'kelembapan',
                            ],

                            'tekanan_udara' => [
                                'label' => 'Tekanan Udara',
                                'unit' => 'hPa',
                                'soft' => 'bg-emerald-500/10',
                                'text' => 'text-emerald-500',
                                'icon' => 'cloud',
                                'valueKey' => 'tekanan_udara',
                            ],

                            'kecepatan_angin' => [
                                'label' => 'Kecepatan Angin',
                                'unit' => 'm/s',
                                'soft' => 'bg-amber-500/10',
                                'text' => 'text-amber-500',
                                'icon' => 'flag',
                                'valueKey' => 'kecepatan_angin',
                            ],

                            'arah_angin' => [
                                'label' => 'Arah Angin',
                                'unit' => '°',
                                'soft' => 'bg-purple-500/10',
                                'text' => 'text-purple-500',
                                'icon' => 'arrow-path-rounded-square',
                                'valueKey' => 'arah_angin',
                            ],

                            'ketinggian_air' => [
                                'label' => 'Ketinggian Air',
                                'unit' => 'cm',
                                'soft' => 'bg-sky-500/10',
                                'text' => 'text-sky-500',
                                'icon' => 'arrow-trending-up',
                                'valueKey' => 'ketinggian_air',
                            ],
                        ];

                        $c = $cardMap[$metric];
                    @endphp

                    <button type="button" wire:click="openMetric('{{ $metric }}')"
                        class="group h-full min-h-[170px] rounded-2xl border border-zinc-200 dark:border-zinc-800
                   bg-white dark:bg-zinc-900 p-5 shadow-sm hover:shadow-md
                   hover:-translate-y-0.5 transition text-left cursor-pointer">
                        <div class="flex h-full flex-col justify-between">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                        {{ $c['label'] }}
                                    </p>

                                    @if ($metric === 'arah_angin')
                                        <div class="mt-3 flex items-end gap-2 flex-wrap">
                                            <h2
                                                class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">
                                                <span x-text="data.arah_angin ?? '--'"></span>°
                                            </h2>

                                            <span class="text-sm font-medium {{ $c['text'] }}"
                                                x-text="data.arah_angin_label ?? '-'"></span>
                                        </div>
                                    @else
                                        <div class="mt-3 flex items-end gap-1 flex-wrap">
                                            <h2
                                                class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">
                                                <span x-text="data.{{ $c['valueKey'] }} ?? '--'"></span>
                                            </h2>

                                            <span class="mb-1 text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $c['unit'] }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div
                                    class="shrink-0 h-11 w-11 rounded-2xl {{ $c['soft'] }} flex items-center justify-center">
                                    @if ($c['icon'] === 'fire')
                                        <x-heroicon-o-fire class="w-5 h-5 {{ $c['text'] }}" />
                                    @elseif($c['icon'] === 'beaker')
                                        <x-heroicon-o-beaker class="w-5 h-5 {{ $c['text'] }}" />
                                    @elseif($c['icon'] === 'cloud')
                                        <x-heroicon-o-cloud class="w-5 h-5 {{ $c['text'] }}" />
                                    @elseif($c['icon'] === 'flag')
                                        <x-heroicon-o-flag class="w-5 h-5 {{ $c['text'] }}" />
                                    @elseif($c['icon'] === 'arrow-path-rounded-square')
                                        <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5 {{ $c['text'] }}" />
                                    @else
                                        <x-heroicon-o-arrow-trending-up class="w-5 h-5 {{ $c['text'] }}" />
                                    @endif
                                </div>
                            </div>

                            <div class="mt-5 flex items-center justify-between">
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">
                                    Klik untuk detail chart
                                </span>

                                <x-heroicon-o-arrow-up-right
                                    class="w-4 h-4 text-zinc-300 dark:text-zinc-600 group-hover:text-zinc-500 transition" />
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>

            {{-- ===== CHART ===== --}}
            <div
                class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-5 sm:p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-white">Trend Sensor</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            Grafik utama untuk sensor yang dipilih
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <select wire:model.live="chartMetric"
                            class="appearance-none text-sm rounded-xl border border-zinc-200 dark:border-zinc-800
                       bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 cursor-pointer">
                            @foreach ($metricLabels as $k => $lbl)
                                <option value="{{ $k }}">{{ $lbl }}</option>
                            @endforeach
                        </select>

                        <select wire:model.live="selectedTimeRange"
                            class="appearance-none text-sm rounded-xl border border-zinc-200 dark:border-zinc-800
                       bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 cursor-pointer">
                            @foreach ($timeRanges as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="rounded-xl bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-100 dark:border-zinc-800 p-3">
                    <div wire:ignore class="relative h-[320px] w-full">
                        <canvas x-ref="waterChart" class="absolute inset-0 w-full h-full"></canvas>
                    </div>
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
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Dashboard Settings
                                </h2>
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
                <div class="fixed inset-0 z-50 flex items-center justify-center px-4" x-data x-init="$nextTick(() => window.flushMetricChartPending?.())">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-[2px]" wire:click="closeModal"></div>

                    <div
                        class="relative w-full max-w-4xl rounded-2xl border border-zinc-200 dark:border-zinc-800
                   bg-white dark:bg-zinc-950 p-5 sm:p-6 shadow-2xl">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-base font-semibold text-zinc-900 dark:text-white">Detail Trend</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    Grafik detail berdasarkan sensor yang dipilih
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <select wire:model.live="modalTimeRange"
                                    class="text-sm rounded-xl border border-zinc-200 dark:border-zinc-800
                               bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2">
                                    @foreach ($timeRanges as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>

                                <button type="button" wire:click="closeModal"
                                    class="inline-flex items-center justify-center rounded-xl border border-zinc-200 dark:border-zinc-800
                               px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-900">
                                    Tutup
                                </button>
                            </div>
                        </div>

                        <div wire:poll.2s="pollMetric"></div>

                        <div
                            class="rounded-xl bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-100 dark:border-zinc-800 p-3">
                            <div class="relative w-full h-[380px]" wire:ignore>
                                <canvas id="metricChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
