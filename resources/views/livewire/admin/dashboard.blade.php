<div wire:init="fetchData" wire:key="dashboard-root">
    <div wire:poll.5s="fetchData"></div>

    @php
        $currentDevice = collect($devices ?? [])->firstWhere('id', $selectedDeviceId);
        $deviceName = $currentDevice['name'] ?? 'ROB ' . ($selectedDeviceId ?? '-');

        $st = ($deviceStatus ?? [])[$selectedDeviceId] ?? null;
        $isOnline = (bool) ($st['online'] ?? false);

        $lastText = '-';
        if (!empty($st['last'])) {
            $lastText = \Illuminate\Support\Carbon::parse($st['last'])->setTimezone('Asia/Jakarta')->format('d M H:i');
        }
    @endphp

    <div x-data="dashboard" x-init="init()" class="flex-1 w-full px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ===== TOP ROW ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-stretch">

            {{-- LEFT: Device + Settings --}}
            <div
                class="lg:col-span-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950/60 p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    {{-- Device badge --}}
                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm
                            border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/40">
                            <span
                                class="inline-flex h-2.5 w-2.5 rounded-full {{ $isOnline ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>
                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $deviceName }}</span>
                            <span class="text-xs text-zinc-500">{{ $isOnline ? 'Online' : 'Offline' }}</span>
                        </span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 justify-end">
                        {{-- dropdown device (admin/operator) kalau device > 1 --}}
                        @if (count($devices) > 1)
                            <select wire:model.live="selectedDeviceId"
                                class="text-xs rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100
                                       px-3 py-2 cursor-pointer">
                                @foreach ($devices as $d)
                                    @php
                                        $dst = ($deviceStatus ?? [])[$d['id']] ?? null;
                                        $online = (bool) ($dst['online'] ?? false);
                                    @endphp
                                    <option value="{{ $d['id'] }}">
                                        {{ $d['name'] }} • {{ $online ? 'Online' : 'Offline' }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        <button type="button" wire:click="openSettings"
                            class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-zinc-800
                                   bg-white dark:bg-zinc-950 px-3 py-2 text-xs
                                   text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition cursor-pointer">
                            <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                            Settings
                        </button>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Risk + Theme --}}
            <div class="rounded-xl border p-4 bg-white dark:bg-zinc-950/60 border-zinc-200 dark:border-zinc-800
                        flex items-center justify-between"
                x-bind:class="[riskStyles.bg, riskStyles.border].join(' ')">

                <div class="flex items-center gap-3">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" x-bind:class="riskStyles.text" />
                    <div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Status</div>
                        <div class="text-lg font-bold" x-bind:class="riskStyles.text" x-text="risk"></div>
                    </div>
                </div>

                {{-- theme toggle --}}
                <button type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-zinc-800
                           bg-white/70 dark:bg-zinc-950/50 px-3 py-2 text-xs text-zinc-700 dark:text-zinc-200
                           hover:bg-zinc-100 dark:hover:bg-zinc-900 transition cursor-pointer"
                    x-on:click="toggleTheme()" title="Theme">
                    <span x-text="theme === 'dark' ? 'Dark' : 'Light'"></span>
                    <span
                        class="relative inline-flex h-5 w-9 items-center rounded-full border
                                border-zinc-300 dark:border-zinc-700 bg-zinc-200 dark:bg-zinc-800">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white dark:bg-zinc-200 transition"
                            :class="theme === 'dark' ? 'translate-x-4' : 'translate-x-1'"></span>
                    </span>
                </button>
            </div>
        </div>

        {{-- ===== METRIC CARDS ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
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
                            'border' => 'border-orange-500/40',
                            'icon' => 'fire',
                            'valueKey' => 'suhu',
                        ],
                        'kelembapan' => [
                            'label' => 'Kelembapan',
                            'unit' => '%',
                            'border' => 'border-cyan-500/30',
                            'icon' => 'beaker',
                            'valueKey' => 'kelembapan',
                        ],
                        'tekanan_udara' => [
                            'label' => 'Tekanan Udara',
                            'unit' => 'hPa',
                            'border' => 'border-emerald-500/30',
                            'icon' => 'cloud',
                            'valueKey' => 'tekanan_udara',
                        ],
                        'kecepatan_angin' => [
                            'label' => 'Kecepatan Angin',
                            'unit' => 'm/s',
                            'border' => 'border-amber-500/30',
                            'icon' => 'flag',
                            'valueKey' => 'kecepatan_angin',
                        ],
                        'arah_angin' => [
                            'label' => 'Arah Angin',
                            'unit' => '°',
                            'border' => 'border-purple-500/30',
                            'icon' => 'arrow-path-rounded-square',
                            'valueKey' => 'arah_angin',
                        ],
                        'ketinggian_air' => [
                            'label' => 'Ketinggian Air',
                            'unit' => 'cm',
                            'border' => 'border-blue-500/30',
                            'icon' => 'arrow-trending-up',
                            'valueKey' => 'ketinggian_air',
                        ],
                    ];
                    $c = $cardMap[$metric];
                @endphp

                <div wire:click="openMetric('{{ $metric }}')"
                    class="bg-white dark:bg-zinc-900 p-6 rounded-xl border {{ $c['border'] }}
                           cursor-pointer hover:opacity-95 transition">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-zinc-900 dark:text-white">{{ $c['label'] }}</p>
                        @if ($c['icon'] === 'fire')
                            <x-heroicon-o-fire class="w-5 h-5 text-orange-500" />
                        @elseif($c['icon'] === 'beaker')
                            <x-heroicon-o-beaker class="w-5 h-5 text-cyan-400" />
                        @elseif($c['icon'] === 'cloud')
                            <x-heroicon-o-cloud class="w-5 h-5 text-emerald-400" />
                        @elseif($c['icon'] === 'flag')
                            <x-heroicon-o-flag class="w-5 h-5 text-amber-400" />
                        @elseif($c['icon'] === 'arrow-path-rounded-square')
                            <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5 text-purple-400" />
                        @else
                            <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-blue-400" />
                        @endif
                    </div>

                    @if ($metric === 'arah_angin')
                        <div class="flex justify-between items-end mt-4">
                            <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">
                                <span x-text="data.arah_angin ?? '--'"></span>°
                            </h2>
                            <span class="text-lg text-purple-400 font-medium"
                                x-text="data.arah_angin_label ?? '-'"></span>
                        </div>
                    @else
                        <h2 class="text-3xl font-bold text-zinc-900 dark:text-white mt-3">
                            <span x-text="data.{{ $c['valueKey'] }} ?? '--'"></span> {{ $c['unit'] }}
                        </h2>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- ===== CHART ===== --}}
        <div class="bg-white dark:bg-zinc-950 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Trend Sensor</h3>

                <div class="flex flex-wrap items-center gap-2">
                    <select wire:model.live="chartMetric"
                        class="text-xs rounded-xl border border-zinc-200 dark:border-zinc-800
                               bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 cursor-pointer">
                        @foreach ($metricLabels as $k => $lbl)
                            <option value="{{ $k }}">{{ $lbl }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="selectedTimeRange"
                        class="text-xs rounded-xl border border-zinc-200 dark:border-zinc-800
                               bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white px-3 py-2 cursor-pointer">
                        @foreach ($timeRanges as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    <span class="text-xs text-zinc-400">Range</span>
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
                        {{-- Device (admin/operator) kalau lebih dari 1 --}}
                        {{-- paling atas: device selector --}}
                        @if (count($devices) > 1 || !$canManageDevices)
                            <div class="mt-4">
                                <div class="text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-2">
                                    Pilih Alat (Device)
                                </div>

                                <select wire:model.live="selectedDeviceId"
                                    class="w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                   bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 cursor-pointer">
                                    @foreach ($devices as $d)
                                        @php
                                            $dst = ($deviceStatus ?? [])[$d['id']] ?? null;
                                            $online = (bool) ($dst['online'] ?? false);
                                        @endphp
                                        <option value="{{ $d['id'] }}">
                                            {{ $d['name'] }} • {{ $online ? 'Online' : 'Offline' }}
                                        </option>
                                    @endforeach
                                </select>

                                @if (empty($devices))
                                    <div class="text-xs text-red-500 mt-2">
                                        Belum ada alat yang diberikan admin.
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mt-4 space-y-4">
                            {{-- visible sensors --}}
                            ...
                        </div>
                        {{-- visible sensors --}}
                        <div>
                            <div class="text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-2">Sensor yang
                                ditampilkan (kartu)</div>
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

                        {{-- chart metric --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <div class="text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-2">Sensor Trend
                                    (Chart)</div>
                                <select wire:model.live="chartMetric"
                                    class="w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                           bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
                                    @foreach ($metricLabels as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <div class="text-sm font-medium text-zinc-700 dark:text-zinc-200 mb-2">Range Default
                                </div>
                                <select wire:model.live="selectedTimeRange"
                                    class="w-full rounded-xl border border-zinc-200 dark:border-zinc-800
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

        {{-- MODAL metric --}}
        @if ($modalOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center">
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

                    <div wire:poll.3s="pollMetric"></div>

                    <div class="h-[360px]" wire:ignore>
                        <canvas id="metricChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
