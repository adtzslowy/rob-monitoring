<section class="p-3 sm:p-4 md:p-6">
    <div class="mx-auto space-y-4 sm:space-y-5">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3 min-w-0">
                <div class="shrink-0 p-2.5 rounded-2xl bg-blue-500/10 text-blue-400 border border-blue-500/10">
                    <x-heroicon-o-cpu-chip class="w-5 h-5 sm:w-6 sm:h-6" />
                </div>
                <div class="min-w-0">
                    <h1 class="text-lg sm:text-xl md:text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                        Manajemen Sensor
                    </h1>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Monitoring sensor terbaru untuk semua device.
                    </p>
                </div>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950/60 overflow-hidden">
            <div class="flex flex-col gap-3 p-3 sm:p-4 bg-zinc-50/70 dark:bg-zinc-900/40 border-b border-zinc-200 dark:border-zinc-800">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    <div class="w-full lg:max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <x-heroicon-o-magnifying-glass class="w-5 h-5 text-zinc-400" />
                            </div>
                            <input wire:model.live.debounce.500ms="search" type="text"
                                class="w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100
                                       placeholder:text-zinc-400 dark:placeholder:text-zinc-500
                                       pl-10 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                                placeholder="Search nama / alias / ID device..." />
                        </div>
                    </div>
                    <div class="w-full lg:w-auto flex flex-col sm:flex-row sm:items-center gap-2">
                        <div class="relative w-full sm:w-[170px]">
                            <select wire:model.live="perPage"
                                class="appearance-none w-full rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100
                                       px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
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

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        1 baris = 1 device. Kolom <strong>Risk</strong> dihitung otomatis via Fuzzy Logic dari threshold yang diatur.
                    </div>
                    @if (!empty($search))
                        <button wire:click="$set('search','')"
                            class="text-xs inline-flex items-center gap-1 text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                            Reset
                        </button>
                    @endif
                </div>
            </div>

            {{-- Table --}}
            <div wire:poll.visible.2s class="overflow-x-auto">
                <table class="min-w-[1500px] w-full text-sm border-collapse table-auto">
                    <thead class="text-[11px] sm:text-xs uppercase bg-zinc-50 dark:bg-zinc-900/50 text-zinc-600 dark:text-zinc-400">
                        <tr>
                            <th class="w-[60px] px-3 sm:px-4 py-3 text-center">No</th>
                            <th class="w-[110px] px-3 sm:px-4 py-3 text-left">Device</th>
                            <th class="w-[110px] px-3 sm:px-4 py-3 text-center">Status</th>
                            <th class="w-[110px] px-3 sm:px-4 py-3 text-center">Temp</th>
                            <th class="w-[110px] px-3 sm:px-4 py-3 text-center">Hum</th>
                            <th class="w-[120px] px-3 sm:px-4 py-3 text-center">Pressure</th>
                            <th class="w-[120px] px-3 sm:px-4 py-3 text-center">Wind Speed</th>
                            <th class="w-[130px] px-3 sm:px-4 py-3 text-center">Wind Dir</th>
                            <th class="w-[110px] px-3 sm:px-4 py-3 text-center">Water</th>
                            <th class="w-[120px] px-3 sm:px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1">
                                    Risk
                                </span>
                            </th>
                            <th class="w-[155px] px-3 sm:px-4 py-3 text-center">Last Update</th>
                            <th class="w-[110px] px-3 sm:px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($devices as $d)
                            @php
                                $lastUpdate = $d['timestamp']
                                    ? \Illuminate\Support\Carbon::parse($d['timestamp'], 'UTC')
                                        ->setTimezone('Asia/Jakarta')
                                        ->format('d M H:i')
                                    : '-';

                                // Risk badge styling
                                $riskLabel = $d['risk_label'] ?? 'AMAN';
                                $riskStyle = match($riskLabel) {
                                    'BAHAYA'  => 'border-red-500/30 bg-red-500/10 text-red-600 dark:text-red-400',
                                    'SIAGA'   => 'border-orange-500/30 bg-orange-500/10 text-orange-600 dark:text-orange-400',
                                    'WASPADA' => 'border-amber-500/30 bg-amber-500/10 text-amber-600 dark:text-amber-400',
                                    default   => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                                };
                                $riskDot = match($riskLabel) {
                                    'BAHAYA'  => 'bg-red-500',
                                    'SIAGA'   => 'bg-orange-500',
                                    'WASPADA' => 'bg-amber-500',
                                    default   => 'bg-emerald-500',
                                };
                            @endphp

                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40 transition">
                                <td class="px-3 sm:px-4 py-3 sm:py-4 text-center whitespace-nowrap text-zinc-500 dark:text-zinc-400">
                                    {{ ($devices->firstItem() ?? 1) + $loop->index }}
                                </td>

                                <td class="px-3 sm:px-4 py-3 sm:py-4 text-left">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $d['label'] }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                                        ID: {{ $d['id'] }}
                                        @if (!empty($d['name']) && $d['alias'] && $d['alias'] !== $d['name'])
                                            • {{ $d['name'] }}
                                        @endif
                                    </div>
                                </td>

                                <td class="px-3 sm:px-4 py-3 sm:py-4 text-center">
                                    <span class="inline-flex items-center justify-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-medium border
                                        {{ $d['online']
                                            ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                            : 'border-red-500/20 bg-red-500/10 text-red-600 dark:text-red-400' }}">
                                        <span class="inline-flex h-1.5 w-1.5 rounded-full {{ $d['online'] ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                        {{ $d['online'] ? 'Online' : 'Offline' }}
                                    </span>
                                </td>

                                <td class="px-3 sm:px-4 py-3 sm:py-4 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">
                                    {{ $d['suhu'] !== null ? $d['suhu'] . ' °C' : '-' }}
                                </td>
                                <td class="px-3 sm:px-4 py-3 sm:py-4 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">
                                    {{ $d['kelembapan'] !== null ? $d['kelembapan'] . ' %' : '-' }}
                                </td>
                                <td class="px-3 sm:px-4 py-3 sm:py-4 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">
                                    {{ $d['tekanan_udara'] !== null ? $d['tekanan_udara'] . ' hPa' : '-' }}
                                </td>
                                <td class="px-3 sm:px-4 py-3 sm:py-4 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">
                                    {{ $d['kecepatan_angin'] !== null ? $d['kecepatan_angin'] . ' m/s' : '-' }}
                                </td>
                                <td class="px-3 sm:px-4 py-3 sm:py-4 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">
                                    @if ($d['arah_angin'] !== null)
                                        {{ $d['arah_angin'] }}°
                                        <span class="text-zinc-400">({{ $d['arah_angin_label'] }})</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-3 sm:px-4 py-3 sm:py-4 text-center whitespace-nowrap">
                                    <span class="font-semibold text-blue-600 dark:text-blue-400">
                                        {{ $d['ketinggian_air'] !== null ? $d['ketinggian_air'] . ' cm' : '-' }}
                                    </span>
                                </td>

                                {{-- ── Risk Badge ─────────────────────────────── --}}
                                <td class="px-3 sm:px-4 py-3 sm:py-4 text-center">
                                    <span class="inline-flex items-center justify-center gap-1.5 rounded-full
                                                 px-2.5 py-1 text-[11px] font-semibold border {{ $riskStyle }}">
                                        <span class="inline-flex h-1.5 w-1.5 rounded-full {{ $riskDot }}"></span>
                                        {{ $riskLabel }}
                                    </span>
                                </td>

                                <td class="px-3 sm:px-4 py-3 sm:py-4 text-center whitespace-nowrap text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $lastUpdate }}
                                </td>

                                {{-- ── Aksi ────────────────────────────────────── --}}
                                <td class="px-3 sm:px-4 py-3 sm:py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if ($canManageDevices)
                                            <button wire:click="openThreshold({{ $d['id'] }})"
                                                title="Atur Threshold"
                                                class="group relative inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                       border border-amber-200 dark:border-amber-800/60
                                                       bg-amber-50 dark:bg-amber-950/40
                                                       text-amber-600 dark:text-amber-400
                                                       hover:bg-amber-100 dark:hover:bg-amber-900/50
                                                       transition-all duration-150 cursor-pointer">
                                                <x-heroicon-o-adjustments-horizontal class="w-4 h-4" />
                                                <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2
                                                             rounded-md px-2 py-1 text-[10px] font-medium whitespace-nowrap
                                                             bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900
                                                             opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                                    Threshold
                                                </span>
                                            </button>
                                        @endif
                                        <button wire:click="openDetail({{ $d['id'] }})"
                                            title="Lihat Detail"
                                            class="group relative inline-flex items-center justify-center w-8 h-8 rounded-lg
                                                   border border-zinc-200 dark:border-zinc-700
                                                   bg-white dark:bg-zinc-900
                                                   text-zinc-500 dark:text-zinc-400
                                                   hover:bg-zinc-50 dark:hover:bg-zinc-800
                                                   hover:text-zinc-700 dark:hover:text-zinc-200
                                                   transition-all duration-150 cursor-pointer">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                            <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2
                                                         rounded-md px-2 py-1 text-[10px] font-medium whitespace-nowrap
                                                         bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900
                                                         opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                                Detail
                                            </span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-5 py-14 text-center">
                                    <div class="flex flex-col items-center gap-2 text-zinc-400 dark:text-zinc-600">
                                        <x-heroicon-o-cpu-chip class="w-8 h-8" />
                                        <span class="text-sm">Tidak ada data sensor.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 sm:p-4
                        bg-zinc-50/70 dark:bg-zinc-900/40 border-t border-zinc-200 dark:border-zinc-800">
                <span class="text-sm text-zinc-600 dark:text-zinc-400">
                    Menampilkan
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $devices->firstItem() ?? 0 }}&ndash;{{ $devices->lastItem() ?? 0 }}
                    </span>
                    dari
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $devices->total() }}</span>
                </span>
                <div class="overflow-x-auto">
                    {{ $devices->onEachSide(1)->links('components.pagination') }}
                </div>
            </div>
        </div>


        {{-- ════════════════════════════════════════════════════════════════ --}}
        {{-- Modal Threshold Sensor — 4 level: Aman / Waspada / Siaga / Bahaya --}}
        {{-- ════════════════════════════════════════════════════════════════ --}}
        @if ($thresholdModalOpen)
            <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
                <button wire:click="closeThresholdModal" class="absolute inset-0 bg-black/60 backdrop-blur-[1px]"></button>

                <div class="relative w-full sm:max-w-3xl mx-0 sm:mx-4 rounded-t-2xl sm:rounded-2xl
                            border border-zinc-200 dark:border-zinc-800
                            bg-white dark:bg-zinc-950 shadow-xl max-h-[90vh] overflow-y-auto">

                    {{-- Header --}}
                    <div class="sticky top-0 z-10 flex items-start justify-between gap-3 p-4 sm:p-5
                                bg-white dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="shrink-0 p-2 rounded-xl bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                <x-heroicon-o-adjustments-horizontal class="w-5 h-5" />
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                                    Threshold &amp; Risk Fuzzy
                                </h2>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400 truncate">
                                    {{ $thresholdDeviceName }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeThresholdModal"
                            class="shrink-0 p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer">
                            <x-heroicon-o-x-mark class="w-5 h-5 text-zinc-500" />
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-4 sm:p-5 space-y-4">

                        {{-- Info --}}
                        <div class="rounded-xl border border-blue-200 dark:border-blue-800/50
                                    bg-blue-50 dark:bg-blue-950/30 px-4 py-3 flex gap-3">
                            <x-heroicon-o-information-circle class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" />
                            <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                                Nilai threshold menentukan level risiko Fuzzy Logic secara otomatis.
                                Pastikan urutan nilai: <strong>Aman &lt; Waspada &lt; Siaga &lt; Bahaya</strong>.
                                Kosongkan field jika tidak ingin mengatur batas untuk sensor tersebut.
                            </p>
                        </div>

                        {{-- Legend level --}}
                        <div class="flex flex-wrap gap-2">
                            @foreach ([
                                ['label' => 'Aman',    'dot' => 'bg-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-300'],
                                ['label' => 'Waspada', 'dot' => 'bg-amber-500',   'bg' => 'bg-amber-50 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800/50 text-amber-700 dark:text-amber-300'],
                                ['label' => 'Siaga',   'dot' => 'bg-orange-500',  'bg' => 'bg-orange-50 dark:bg-orange-950/30 border-orange-200 dark:border-orange-800/50 text-orange-700 dark:text-orange-300'],
                                ['label' => 'Bahaya',  'dot' => 'bg-red-500',     'bg' => 'bg-red-50 dark:bg-red-950/30 border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300'],
                            ] as $lv)
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium border {{ $lv['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $lv['dot'] }}"></span>
                                    {{ $lv['label'] }}
                                </span>
                            @endforeach
                        </div>

                        {{-- Error --}}
                        @error('threshold')
                            <div class="rounded-xl border border-red-200 dark:border-red-800/50
                                        bg-red-50 dark:bg-red-950/30 px-4 py-3 flex gap-3">
                                <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
                                <p class="text-xs text-red-700 dark:text-red-300">{{ $message }}</p>
                            </div>
                        @enderror

                        {{-- Success --}}
                        @if ($thresholdSaved)
                            <div class="rounded-xl border border-emerald-200 dark:border-emerald-800/50
                                        bg-emerald-50 dark:bg-emerald-950/30 px-4 py-3 flex gap-3">
                                <x-heroicon-o-check-circle class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                                <p class="text-xs text-emerald-700 dark:text-emerald-300 font-medium">
                                    Threshold berhasil disimpan! Risk akan diperbarui pada polling berikutnya.
                                </p>
                            </div>
                        @endif

                        {{-- Sensor rows --}}
                        @php
                            $sensorRows = [
                                ['key' => 'ketinggian_air',  'label' => 'Ketinggian Air', 'unit' => 'cm',  'desc' => 'Nilai makin tinggi = makin berbahaya (sensor di atas permukaan air)'],
                                ['key' => 'suhu',            'label' => 'Temperature',    'unit' => '°C',  'desc' => 'Nilai makin tinggi = makin berbahaya'],
                                ['key' => 'kelembapan',      'label' => 'Kelembapan',     'unit' => '%',   'desc' => 'Nilai makin tinggi = makin berbahaya'],
                                ['key' => 'tekanan_udara',   'label' => 'Tekanan Udara',  'unit' => 'hPa', 'desc' => 'Nilai makin rendah = makin berbahaya'],
                                ['key' => 'kecepatan_angin', 'label' => 'Kec. Angin',     'unit' => 'm/s', 'desc' => 'Nilai makin tinggi = makin berbahaya'],
                                ['key' => 'arah_angin',      'label' => 'Arah Angin',     'unit' => '°',   'desc' => 'Onshore (270°) paling berbahaya, offshore (90°) paling aman'],
                            ];

                            $levelCols = [
                                ['key' => 'aman',    'label' => 'Aman',    'ring' => 'focus:ring-emerald-500/30 focus:border-emerald-500/50', 'badge' => 'text-emerald-600 dark:text-emerald-400'],
                                ['key' => 'waspada', 'label' => 'Waspada', 'ring' => 'focus:ring-amber-500/30 focus:border-amber-500/50',   'badge' => 'text-amber-600 dark:text-amber-400'],
                                ['key' => 'siaga',   'label' => 'Siaga',   'ring' => 'focus:ring-orange-500/30 focus:border-orange-500/50', 'badge' => 'text-orange-600 dark:text-orange-400'],
                                ['key' => 'bahaya',  'label' => 'Bahaya',  'ring' => 'focus:ring-red-500/30 focus:border-red-500/50',       'badge' => 'text-red-600 dark:text-red-400'],
                            ];
                        @endphp

                        <div class="space-y-3">
                            @foreach ($sensorRows as $row)
                                <div class="rounded-xl border border-zinc-200 dark:border-zinc-800
                                            bg-zinc-50/60 dark:bg-zinc-900/30 px-4 py-3.5">

                                    {{-- Sensor header --}}
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                            {{ $row['label'] }}
                                        </span>
                                        <span class="text-[11px] text-zinc-400 dark:text-zinc-500 bg-zinc-100 dark:bg-zinc-800
                                                     px-1.5 py-0.5 rounded-md font-mono">
                                            {{ $row['unit'] }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mb-3 leading-relaxed">
                                        {{ $row['desc'] }}
                                    </p>

                                    {{-- 4 level inputs --}}
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                        @foreach ($levelCols as $lv)
                                            <div>
                                                <label class="block text-[11px] font-medium mb-1.5 {{ $lv['badge'] }}">
                                                    {{ $lv['label'] }}
                                                </label>
                                                <div class="relative">
                                                    <input
                                                        wire:model="thresholdForm.{{ $row['key'] }}_{{ $lv['key'] }}"
                                                        type="number"
                                                        step="any"
                                                        placeholder="—"
                                                        class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700
                                                               bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100
                                                               placeholder:text-zinc-300 dark:placeholder:text-zinc-600
                                                               px-3 py-2 pr-10 text-sm focus:outline-none
                                                               {{ $lv['ring'] }}" />
                                                    <span class="absolute inset-y-0 right-0 flex items-center pr-2.5
                                                                 text-[10px] text-zinc-400 dark:text-zinc-500
                                                                 pointer-events-none font-mono">
                                                        {{ $row['unit'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="sticky bottom-0 flex items-center justify-end gap-3 px-4 sm:px-5 py-4
                                bg-white dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800">
                        <button wire:click="closeThresholdModal"
                            class="rounded-xl px-4 py-2.5 text-sm border border-zinc-200 dark:border-zinc-800
                                   text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-900
                                   transition cursor-pointer">
                            Batal
                        </button>
                        <button wire:click="saveThreshold" wire:loading.attr="disabled"
                            class="rounded-xl px-5 py-2.5 text-sm font-medium
                                   bg-blue-600 hover:bg-blue-700 text-white transition cursor-pointer
                                   disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center gap-2">
                            <span wire:loading wire:target="saveThreshold">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </span>
                            Simpan Threshold
                        </button>
                    </div>
                </div>
            </div>
        @endif


        {{-- ════════════════════════════════════════════════════════════════ --}}
        {{-- Modal Detail Sensor                                             --}}
        {{-- ════════════════════════════════════════════════════════════════ --}}
        @if ($modalOpen && $this->detailDevice)
            @php
                $detailDevice  = $this->detailDevice;
                $detailReading = $this->detailReading;
                $detailHistory = $this->detailHistory;
                $detailStatus  = $deviceStatus[$detailDevice->id] ?? ['online' => false, 'last' => null];
                $isOnline      = (bool) ($detailStatus['online'] ?? false);

                $detailRisk      = $detailRiskResult ?? null;
                $detailRiskLabel = $detailRisk['label'] ?? 'AMAN';
                $detailRiskScore = $detailRisk['score'] ?? 0;
                $detailMemberships = $detailRisk['memberships'] ?? [
                    'muAman' => 0, 'muWaspada' => 0, 'muSiaga' => 0, 'muBahaya' => 0,
                ];

                $riskColors = [
                    'BAHAYA'  => ['badge' => 'border-red-500/30 bg-red-500/10 text-red-600 dark:text-red-400',     'bar' => 'bg-red-500',     'dot' => 'bg-red-500'],
                    'SIAGA'   => ['badge' => 'border-orange-500/30 bg-orange-500/10 text-orange-600 dark:text-orange-400', 'bar' => 'bg-orange-500', 'dot' => 'bg-orange-500'],
                    'WASPADA' => ['badge' => 'border-amber-500/30 bg-amber-500/10 text-amber-600 dark:text-amber-400',   'bar' => 'bg-amber-500',  'dot' => 'bg-amber-500'],
                    'AMAN'    => ['badge' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400', 'bar' => 'bg-emerald-500','dot' => 'bg-emerald-500'],
                ];
                $rc = $riskColors[$detailRiskLabel] ?? $riskColors['AMAN'];

                $sensorCards = [
                    ['label' => 'Temperature',   'value' => $detailReading?->suhu !== null ? $detailReading->suhu . ' °C' : '-',                       'dot' => 'bg-amber-400'],
                    ['label' => 'Kelembapan',    'value' => $detailReading?->kelembapan !== null ? $detailReading->kelembapan . ' %' : '-',            'dot' => 'bg-blue-400'],
                    ['label' => 'Tekanan Udara', 'value' => $detailReading?->tekanan_udara !== null ? $detailReading->tekanan_udara . ' hPa' : '-',    'dot' => 'bg-violet-400'],
                    ['label' => 'Kec. Angin',    'value' => $detailReading?->kecepatan_angin !== null ? $detailReading->kecepatan_angin . ' m/s' : '-','dot' => 'bg-emerald-400'],
                    ['label' => 'Arah Angin',    'value' => $detailReading?->arah_angin !== null ? $detailReading->arah_angin . '° (' . $this->getWindDirectionLabel($detailReading->arah_angin) . ')' : '-', 'dot' => 'bg-zinc-400'],
                    ['label' => 'Ketinggian Air','value' => $detailReading?->ketinggian_air !== null ? $detailReading->ketinggian_air . ' cm' : '-',   'dot' => 'bg-blue-500', 'water' => true],
                ];

                $membershipBars = [
                    ['key' => 'muAman',    'label' => 'Aman',    'color' => 'bg-emerald-500', 'text' => 'text-emerald-600 dark:text-emerald-400'],
                    ['key' => 'muWaspada', 'label' => 'Waspada', 'color' => 'bg-amber-500',   'text' => 'text-amber-600 dark:text-amber-400'],
                    ['key' => 'muSiaga',   'label' => 'Siaga',   'color' => 'bg-orange-500',  'text' => 'text-orange-600 dark:text-orange-400'],
                    ['key' => 'muBahaya',  'label' => 'Bahaya',  'color' => 'bg-red-500',     'text' => 'text-red-600 dark:text-red-400'],
                ];
            @endphp

            <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
                <button wire:click="closeModal" class="absolute inset-0 bg-black/60 backdrop-blur-[1px]"></button>

                <div class="relative w-full sm:max-w-5xl mx-0 sm:mx-4 rounded-t-2xl sm:rounded-2xl
                            border border-zinc-200 dark:border-zinc-800
                            bg-white dark:bg-zinc-950 shadow-xl max-h-[92vh] flex flex-col overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center justify-between gap-3 px-5 py-4
                                border-b border-zinc-200 dark:border-zinc-800 shrink-0">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="shrink-0 p-2 rounded-xl bg-blue-500/10 text-blue-500 border border-blue-500/20">
                                <x-heroicon-o-cpu-chip class="w-5 h-5" />
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 truncate">
                                    Detail Sensor Device
                                </h2>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400 truncate">
                                    {{ $detailDevice->alias ?: ($detailDevice->name ?: 'ROB ' . $detailDevice->id) }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeModal"
                            class="shrink-0 p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer">
                            <x-heroicon-o-x-mark class="w-5 h-5 text-zinc-500" />
                        </button>
                    </div>

                    {{-- Scrollable body --}}
                    <div class="overflow-y-auto flex-1 p-4 sm:p-5 space-y-5">

                        {{-- Controls --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                Pilih range data sensor untuk melihat riwayat pembacaan.
                            </p>
                            <div class="flex gap-2 flex-wrap">
                                <div class="relative">
                                    <select wire:model.live="detailRange"
                                        class="appearance-none rounded-xl border border-zinc-200 dark:border-zinc-800
                                               bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100
                                               pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                        <option value="1m">1 Menit</option>
                                        <option value="1h">1 Jam</option>
                                        <option value="1d">1 Hari</option>
                                        <option value="1w">1 Minggu</option>
                                        <option value="1mo">1 Bulan</option>
                                        <option value="1y">1 Tahun</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-zinc-400">
                                        <x-heroicon-o-chevron-down class="w-3.5 h-3.5" />
                                    </div>
                                </div>
                                <div class="relative">
                                    <select wire:model.live="detailPerPage"
                                        class="appearance-none rounded-xl border border-zinc-200 dark:border-zinc-800
                                               bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100
                                               pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                        <option value="10">10 / halaman</option>
                                        <option value="25">25 / halaman</option>
                                        <option value="50">50 / halaman</option>
                                        <option value="100">100 / halaman</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-zinc-400">
                                        <x-heroicon-o-chevron-down class="w-3.5 h-3.5" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Stat cards --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div class="rounded-xl bg-zinc-50 dark:bg-zinc-900/50
                                        border border-zinc-200 dark:border-zinc-800 px-4 py-3">
                                <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mb-1">Device ID</div>
                                <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $detailDevice->id }}</div>
                            </div>
                            <div class="rounded-xl bg-zinc-50 dark:bg-zinc-900/50
                                        border border-zinc-200 dark:border-zinc-800 px-4 py-3">
                                <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mb-1">Status</div>
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex h-2 w-2 rounded-full {{ $isOnline ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    <span class="text-sm font-semibold {{ $isOnline ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $isOnline ? 'Online' : 'Offline' }}
                                    </span>
                                </div>
                            </div>
                            <div class="rounded-xl bg-zinc-50 dark:bg-zinc-900/50
                                        border border-zinc-200 dark:border-zinc-800 px-4 py-3">
                                <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mb-1">Last Update</div>
                                <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $this->detailLastUpdateText }}
                                </div>
                            </div>
                        </div>

                        {{-- ── Risk Panel ──────────────────────────────────────── --}}
                        <div class="rounded-xl border {{ $rc['badge'] }} overflow-hidden">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-4 py-4">
                                {{-- Label + score --}}
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-xl
                                                border {{ $rc['badge'] }}">
                                        <x-heroicon-o-shield-exclamation class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400 mb-0.5">
                                            Risk Level (Fuzzy Score)
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-bold {{ str_replace('border ', '', explode(' ', $rc['badge'])[0]) }}">
                                                {{ $detailRiskLabel }}
                                            </span>
                                            <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $detailRiskScore }} / 100
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Score bar --}}
                                <div class="flex-1 sm:max-w-xs">
                                    <div class="flex justify-between text-[10px] text-zinc-400 mb-1">
                                        <span>Aman</span>
                                        <span>Bahaya</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $rc['bar'] }}"
                                             style="width: {{ $detailRiskScore }}%"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Membership bars --}}
                            <div class="border-t border-zinc-200 dark:border-zinc-800 px-4 py-3
                                        grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach ($membershipBars as $mb)
                                    @php $val = $detailMemberships[$mb['key']] ?? 0; @endphp
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-[11px] {{ $mb['text'] }} font-medium">{{ $mb['label'] }}</span>
                                            <span class="text-[11px] text-zinc-500 dark:text-zinc-400">{{ number_format($val, 2) }}</span>
                                        </div>
                                        <div class="h-1.5 rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                            <div class="h-full rounded-full {{ $mb['color'] }}"
                                                 style="width: {{ round($val * 100) }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Current readings grid --}}
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Pembacaan Terkini</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                @foreach ($sensorCards as $card)
                                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800
                                                bg-white dark:bg-zinc-900/40 px-4 py-3 flex flex-col gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex w-2 h-2 rounded-full shrink-0 {{ $card['dot'] }}"></span>
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $card['label'] }}</span>
                                        </div>
                                        <div class="text-xl font-semibold leading-none
                                                    {{ ($card['water'] ?? false) ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                                            {{ $card['value'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- History table --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Riwayat Sensor</h3>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400
                                             bg-zinc-100 dark:bg-zinc-800 rounded-lg px-2.5 py-1">
                                    Range: {{ $this->detailRangeLabel }}
                                </span>
                            </div>
                            <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-[820px] w-full text-sm border-collapse">
                                        <thead class="bg-zinc-50 dark:bg-zinc-900/60 text-[11px] uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-medium">Waktu</th>
                                                <th class="px-4 py-3 text-center font-medium">Temp</th>
                                                <th class="px-4 py-3 text-center font-medium">Hum</th>
                                                <th class="px-4 py-3 text-center font-medium">Pressure</th>
                                                <th class="px-4 py-3 text-center font-medium">Wind Speed</th>
                                                <th class="px-4 py-3 text-center font-medium">Wind Dir</th>
                                                <th class="px-4 py-3 text-center font-medium">Water</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/70">
                                            @forelse ($detailHistory as $row)
                                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/30 transition">
                                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-zinc-500 dark:text-zinc-400">{{ $row['timestamp'] }}</td>
                                                    <td class="px-4 py-3 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">{{ $row['suhu'] !== null ? $row['suhu'] . ' °C' : '-' }}</td>
                                                    <td class="px-4 py-3 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">{{ $row['kelembapan'] !== null ? $row['kelembapan'] . ' %' : '-' }}</td>
                                                    <td class="px-4 py-3 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">{{ $row['tekanan_udara'] !== null ? $row['tekanan_udara'] . ' hPa' : '-' }}</td>
                                                    <td class="px-4 py-3 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">{{ $row['kecepatan_angin'] !== null ? $row['kecepatan_angin'] . ' m/s' : '-' }}</td>
                                                    <td class="px-4 py-3 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">
                                                        @if ($row['arah_angin'] !== null)
                                                            {{ $row['arah_angin'] }}° <span class="text-zinc-400">({{ $row['arah_angin_label'] }})</span>
                                                        @else -
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center whitespace-nowrap font-semibold text-blue-600 dark:text-blue-400">
                                                        {{ $row['ketinggian_air'] !== null ? $row['ketinggian_air'] . ' cm' : '-' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="px-4 py-10 text-center">
                                                        <div class="flex flex-col items-center gap-2 text-zinc-400 dark:text-zinc-600">
                                                            <x-heroicon-o-chart-bar class="w-7 h-7" />
                                                            <span class="text-sm">Tidak ada data pada range ini.</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3
                                        px-3 py-2.5 rounded-xl bg-zinc-50/70 dark:bg-zinc-900/40
                                        border border-zinc-200 dark:border-zinc-800">
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    Menampilkan
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $detailHistory->firstItem() ?? 0 }}&ndash;{{ $detailHistory->lastItem() ?? 0 }}
                                    </span>
                                    dari
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $detailHistory->total() }}</span>
                                    data
                                </span>
                                <div class="overflow-x-auto">
                                    {{ $detailHistory->onEachSide(1)->links('components.pagination') }}
                                </div>
                            </div>
                        </div>

                    </div>{{-- /scrollable body --}}

                    {{-- Footer --}}
                    <div class="flex items-center justify-end px-5 py-4
                                border-t border-zinc-200 dark:border-zinc-800 shrink-0">
                        <button wire:click="closeModal"
                            class="rounded-xl px-4 py-2.5 text-sm font-medium
                                   border border-zinc-200 dark:border-zinc-800
                                   text-zinc-700 dark:text-zinc-300
                                   hover:bg-zinc-50 dark:hover:bg-zinc-900
                                   transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</section>