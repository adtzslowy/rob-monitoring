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
        <div
            class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950/60 overflow-hidden">
            <div
                class="flex flex-col gap-3 p-3 sm:p-4 bg-zinc-50/70 dark:bg-zinc-900/40 border-b border-zinc-200 dark:border-zinc-800">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">

                    {{-- Search --}}
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

                    {{-- Right actions --}}
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

                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400">
                                <x-heroicon-o-chevron-down class="w-4 h-4" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        1 baris = 1 device, kolom berikutnya menampilkan sensor terbaru.
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
                <table class="min-w-[1400px] w-full text-sm border-collapse table-fixed">
                    <thead
                        class="text-[11px] sm:text-xs uppercase bg-zinc-50 dark:bg-zinc-900/50 text-zinc-600 dark:text-zinc-400">
                        <tr>
                            <th class="w-[70px] px-3 sm:px-5 py-3 text-center">No</th>
                            <th class="w-[180px] px-3 sm:px-5 py-3 text-left">Device</th>
                            <th class="w-[120px] px-3 sm:px-5 py-3 text-center">Status</th>
                            <th class="w-[120px] px-3 sm:px-5 py-3 text-center">Temp</th>
                            <th class="w-[120px] px-3 sm:px-5 py-3 text-center">Hum</th>
                            <th class="w-[140px] px-3 sm:px-5 py-3 text-center">Pressure</th>
                            <th class="w-[140px] px-3 sm:px-5 py-3 text-center">Wind Speed</th>
                            <th class="w-[150px] px-3 sm:px-5 py-3 text-center">Wind Dir</th>
                            <th class="w-[130px] px-3 sm:px-5 py-3 text-center">Water</th>
                            <th class="w-[170px] px-3 sm:px-5 py-3 text-center">Last Update</th>
                            <th class="w-[120px] px-3 sm:px-5 py-3 text-center">Aksi</th>
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
                            @endphp

                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40 transition">
                                <td class="px-3 sm:px-5 py-3 sm:py-4 text-center whitespace-nowrap">
                                    {{ ($devices->firstItem() ?? 1) + $loop->index }}
                                </td>

                                <td class="px-3 sm:px-5 py-3 sm:py-4 text-left">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $d['label'] }}
                                    </div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                                        ID: {{ $d['id'] }} @if (!empty($d['name']) && $d['alias'] && $d['alias'] !== $d['name'])
                                            • {{ $d['name'] }}
                                        @endif
                                    </div>
                                </td>

                                <td class="px-3 sm:px-5 py-3 sm:py-4 text-center">
                                    <span
                                        class="inline-flex items-center justify-center gap-2 rounded-full px-2.5 py-1 text-[11px] sm:text-xs font-medium border
                                        {{ $d['online']
                                            ? 'border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-300'
                                            : 'border-red-500/20 bg-red-500/10 text-red-600 dark:text-red-300' }}">
                                        <span
                                            class="inline-flex h-2 w-2 rounded-full {{ $d['online'] ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                        {{ $d['online'] ? 'Online' : 'Offline' }}
                                    </span>
                                </td>

                                <td
                                    class="px-3 sm:px-5 py-3 sm:py-4 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">
                                    {{ $d['suhu'] !== null ? $d['suhu'] . ' °C' : '-' }}
                                </td>

                                <td
                                    class="px-3 sm:px-5 py-3 sm:py-4 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">
                                    {{ $d['kelembapan'] !== null ? $d['kelembapan'] . ' %' : '-' }}
                                </td>

                                <td
                                    class="px-3 sm:px-5 py-3 sm:py-4 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">
                                    {{ $d['tekanan_udara'] !== null ? $d['tekanan_udara'] . ' hPa' : '-' }}
                                </td>

                                <td
                                    class="px-3 sm:px-5 py-3 sm:py-4 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">
                                    {{ $d['kecepatan_angin'] !== null ? $d['kecepatan_angin'] . ' m/s' : '-' }}
                                </td>

                                <td
                                    class="px-3 sm:px-5 py-3 sm:py-4 text-center whitespace-nowrap text-zinc-700 dark:text-zinc-200">
                                    @if ($d['arah_angin'] !== null)
                                        {{ $d['arah_angin'] }}° <span
                                            class="text-zinc-500">({{ $d['arah_angin_label'] }})</span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-3 sm:px-5 py-3 sm:py-4 text-center whitespace-nowrap">
                                    <span class="font-semibold text-blue-600 dark:text-blue-300">
                                        {{ $d['ketinggian_air'] !== null ? $d['ketinggian_air'] . ' cm' : '-' }}
                                    </span>
                                </td>

                                <td
                                    class="px-3 sm:px-5 py-3 sm:py-4 text-center whitespace-nowrap text-zinc-600 dark:text-zinc-300">
                                    {{ $lastUpdate }}
                                </td>

                                <td class="px-3 sm:px-5 py-3 sm:py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="openDetail({{ $d['id'] }})"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-medium
                                                   border border-zinc-200 dark:border-zinc-800
                                                   bg-white dark:bg-zinc-950 text-zinc-700 dark:text-zinc-200
                                                   hover:bg-zinc-50 dark:hover:bg-zinc-900 transition cursor-pointer">
                                            Detail
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-5 py-12 text-center text-zinc-500">
                                    Tidak ada data sensor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 sm:p-4
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

                <div class="overflow-x-auto">
                    {{ $devices->onEachSide(1)->links('components.pagination') }}
                </div>
            </div>
        </div>

        {{-- Modal detail --}}
        @if ($modalOpen && $this->detailDevice)
            @php
                $detailDevice = $this->detailDevice;
                $detailReading = $this->detailReading;
                $detailHistory = $this->detailHistory;
                $detailStatus = $deviceStatus[$detailDevice->id] ?? ['online' => false, 'last' => null];
                $isOnline = (bool) ($detailStatus['online'] ?? false);
            @endphp

            <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center">
                <button wire:click="closeModal" class="absolute inset-0 bg-black/60"></button>

                <div
                    class="relative w-full sm:max-w-6xl mx-0 sm:mx-4 rounded-t-2xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800
                            bg-white dark:bg-zinc-950 p-4 sm:p-5 shadow-xl max-h-[90vh] overflow-y-auto">

                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                Detail Sensor Device
                            </h2>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $detailDevice->alias ?: ($detailDevice->name ?: 'ROB ' . $detailDevice->id) }}
                            </p>
                        </div>

                        <button wire:click="closeModal" class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            <x-heroicon-o-x-mark class="w-5 h-5 text-zinc-500" />
                        </button>
                    </div>

                    <div class="mt-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                            Pilih range data sensor untuk melihat riwayat pembacaan.
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <select wire:model.live="detailRange"
                                class="appearance-none w-full sm:w-[180px] rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100
                                       px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                <option value="1m">1 Menit</option>
                                <option value="1h">1 Jam</option>
                                <option value="1d">1 Hari</option>
                                <option value="1w">1 Minggu</option>
                                <option value="1mo">1 Bulan</option>
                                <option value="1y">1 Tahun</option>
                            </select>

                            <select wire:model.live="detailPerPage"
                                class="appearance-none w-full sm:w-[150px] rounded-xl border border-zinc-200 dark:border-zinc-800
                                       bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100
                                       px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                <option value="10">10 / halaman</option>
                                <option value="25">25 / halaman</option>
                                <option value="50">50 / halaman</option>
                                <option value="100">100 / halaman</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div
                            class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-900/40 px-4 py-3">
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Device ID</div>
                            <div class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $detailDevice->id }}
                            </div>
                        </div>

                        <div
                            class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-900/40 px-4 py-3">
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Status</div>
                            <div
                                class="mt-1 text-sm font-medium {{ $isOnline ? 'text-emerald-600 dark:text-emerald-300' : 'text-red-600 dark:text-red-300' }}">
                                {{ $isOnline ? 'Online' : 'Offline' }}
                            </div>
                        </div>

                        <div
                            class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-900/40 px-4 py-3">
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Last Update</div>
                            <div class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $this->detailLastUpdateText }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full w-full text-sm border-collapse">
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                <tr>
                                    <td class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Temperature</td>
                                    <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                        {{ $detailReading?->suhu !== null ? $detailReading->suhu . ' °C' : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Kelembapan</td>
                                    <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                        {{ $detailReading?->kelembapan !== null ? $detailReading->kelembapan . ' %' : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Tekanan Udara
                                    </td>
                                    <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                        {{ $detailReading?->tekanan_udara !== null ? $detailReading->tekanan_udara . ' hPa' : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Kecepatan Angin
                                    </td>
                                    <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                        {{ $detailReading?->kecepatan_angin !== null ? $detailReading->kecepatan_angin . ' m/s' : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Arah Angin</td>
                                    <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                        @if ($detailReading?->arah_angin !== null)
                                            {{ $detailReading->arah_angin }}°
                                            ({{ $this->getWindDirectionLabel($detailReading->arah_angin) }})
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-medium text-zinc-600 dark:text-zinc-400">Ketinggian Air
                                    </td>
                                    <td class="px-4 py-3 text-zinc-900 dark:text-zinc-100">
                                        {{ $detailReading?->ketinggian_air !== null ? $detailReading->ketinggian_air . ' cm' : '-' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                Riwayat Sensor
                            </h3>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                Range: {{ $this->detailRangeLabel }}
                            </span>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-800">
                            <table class="min-w-[950px] w-full text-sm border-collapse">
                                <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-600 dark:text-zinc-400">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Waktu</th>
                                        <th class="px-4 py-3 text-center">Temp</th>
                                        <th class="px-4 py-3 text-center">Hum</th>
                                        <th class="px-4 py-3 text-center">Pressure</th>
                                        <th class="px-4 py-3 text-center">Wind Speed</th>
                                        <th class="px-4 py-3 text-center">Wind Dir</th>
                                        <th class="px-4 py-3 text-center">Water</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                    @forelse ($detailHistory as $row)
                                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/40 transition">
                                            <td class="px-4 py-3 whitespace-nowrap text-zinc-700 dark:text-zinc-200">
                                                {{ $row['timestamp'] }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-zinc-700 dark:text-zinc-200">
                                                {{ $row['suhu'] !== null ? $row['suhu'] . ' °C' : '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-zinc-700 dark:text-zinc-200">
                                                {{ $row['kelembapan'] !== null ? $row['kelembapan'] . ' %' : '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-zinc-700 dark:text-zinc-200">
                                                {{ $row['tekanan_udara'] !== null ? $row['tekanan_udara'] . ' hPa' : '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-zinc-700 dark:text-zinc-200">
                                                {{ $row['kecepatan_angin'] !== null ? $row['kecepatan_angin'] . ' m/s' : '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-zinc-700 dark:text-zinc-200">
                                                @if ($row['arah_angin'] !== null)
                                                    {{ $row['arah_angin'] }}° ({{ $row['arah_angin_label'] }})
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center font-medium text-blue-600 dark:text-blue-300">
                                                {{ $row['ketinggian_air'] !== null ? $row['ketinggian_air'] . ' cm' : '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7"
                                                class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                                Tidak ada data pada range ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div
                            class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 rounded-xl
                                   bg-zinc-50/70 dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-800">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">
                                Menampilkan
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $detailHistory->firstItem() ?? 0 }}-{{ $detailHistory->lastItem() ?? 0 }}
                                </span>
                                dari
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $detailHistory->total() }}
                                </span>
                                data
                            </span>

                            <div class="overflow-x-auto">
                                {{ $detailHistory->onEachSide(1)->links('components.pagination') }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-end">
                        <button wire:click="closeModal"
                            class="rounded-xl px-4 py-2.5 text-sm border border-zinc-200 dark:border-zinc-800 cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</section>