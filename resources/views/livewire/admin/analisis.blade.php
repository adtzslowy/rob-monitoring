<section class="p-3 sm:p-4 md:p-6">
    <div class="mx-auto max-w-7xl space-y-5">

        {{-- Header --}}
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4 min-w-0">
                <div
                    class="shrink-0 flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500/20 to-blue-600/10 border border-blue-500/20 text-blue-400">
                    <x-heroicon-o-cpu-chip class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">
                        Manajemen Sensor
                    </h1>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                        Monitoring sensor terbaru untuk semua device
                    </p>
                </div>
            </div>
        </div>

        {{-- Main Card --}}
        <div
            class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 overflow-hidden shadow-sm">

            {{-- Toolbar --}}
            <div
                class="flex flex-col gap-3 px-4 py-3 bg-zinc-50/80 dark:bg-zinc-900/60 border-b border-zinc-200 dark:border-zinc-800">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">

                    {{-- Search --}}
                    <div class="relative w-full lg:max-w-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-zinc-400" />
                        </div>
                        <input wire:model.live.debounce.500ms="search" type="text"
                            class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700
                                   bg-white dark:bg-zinc-900/80 text-zinc-900 dark:text-zinc-100
                                   placeholder:text-zinc-400 dark:placeholder:text-zinc-500
                                   pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400/50
                                   transition"
                            placeholder="Cari nama, alias, atau ID device..." />
                        @if (!empty($search))
                            <button wire:click="$set('search','')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        @endif
                    </div>

                    {{-- Per page --}}
                    <div class="relative w-full lg:w-44">
                        <select wire:model.live="perPage"
                            class="appearance-none w-full rounded-xl border border-zinc-200 dark:border-zinc-700
                                   bg-white dark:bg-zinc-900/80 text-zinc-900 dark:text-zinc-100
                                   px-3 py-2.5 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30
                                   transition cursor-pointer">
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

                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                    1 baris = 1 device · kolom berikutnya menampilkan pembacaan sensor terbaru
                </p>
            </div>

            {{-- Table --}}
            <div wire:poll.visible.2s class="overflow-x-auto">
                <table class="min-w-[1400px] w-full text-sm table-fixed">
                    <thead>ext
                        <tr class="border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30">
                            <th
                                class="w-[60px] px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                #</th>
                            <th
                                class="w-[190px] px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                Device</th>
                            <th
                                class="w-[110px] px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                Status</th>
                            <th
                                class="w-[120px] px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                Suhu</th>
                            <th
                                class="w-[120px] px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                Kelembapan</th>
                            <th
                                class="w-[140px] px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                Tekanan</th>
                            <th
                                class="w-[140px] px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                Angin</th>
                            <th
                                class="w-[150px] px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                Arah Angin</th>
                            <th
                                class="w-[130px] px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                Ketinggian</th>
                            <th
                                class="w-[160px] px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                Last Update</th>
                            <th
                                class="w-[100px] px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @forelse($devices as $d)
                            @php
                                $lastUpdate = data_get($d, 'last_seen')
                                    ? \Illuminate\Support\Carbon::parse($d['timestamp'], 'UTC')
                                        ->setTimezone('Asia/Jakarta')
                                        ->format('d M H:i')
                                    : '-';
                            @endphp

                            <tr
                                class="group hover:bg-zinc-50/80 dark:hover:bg-zinc-900/40 transition-colors duration-150">

                                {{-- No --}}
                                <td class="px-4 py-3.5 text-center">
                                    <span class="text-xs text-zinc-400">
                                        {{ $loop->iteration }}
                                    </span>
                                </td>

                                {{-- Device --}}
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100 truncate text-sm">
                                        {{ $d['label'] }}
                                    </div>
                                    <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5 truncate">
                                        ID: {{ $d['id'] }}
                                        @if (!empty($d['name']) && $d['alias'] && $d['alias'] !== $d['name'])
                                            · {{ $d['name'] }}
                                        @endif
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3.5 text-center">
                                    @if ($d['online'])
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold border border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                            <span class="relative flex h-1.5 w-1.5">
                                                <span
                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                                                <span
                                                    class="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            </span>
                                            Online
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold border border-red-500/20 bg-red-500/10 text-red-600 dark:text-red-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Offline
                                        </span>
                                    @endif
                                </td>

                                {{-- Suhu --}}
                                <td class="px-4 py-3.5 text-center">
                                    @if ($d['suhu'] !== null)
                                        <span
                                            class="font-semibold text-orange-500 dark:text-orange-400">{{ $d['suhu'] }}</span>
                                        <span class="text-xs text-zinc-400"> °C</span>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>

                                {{-- Kelembapan --}}
                                <td class="px-4 py-3.5 text-center">
                                    @if ($d['kelembapan'] !== null)
                                        <span
                                            class="font-semibold text-cyan-500 dark:text-cyan-400">{{ $d['kelembapan'] }}</span>
                                        <span class="text-xs text-zinc-400"> %</span>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>

                                {{-- Tekanan --}}
                                <td class="px-4 py-3.5 text-center">
                                    @if ($d['tekanan_udara'] !== null)
                                        <span
                                            class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $d['tekanan_udara'] }}</span>
                                        <span class="text-xs text-zinc-400"> hPa</span>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>

                                {{-- Angin --}}
                                <td class="px-4 py-3.5 text-center">
                                    @if ($d['kecepatan_angin'] !== null)
                                        <span
                                            class="font-semibold text-amber-500 dark:text-amber-400">{{ $d['kecepatan_angin'] }}</span>
                                        <span class="text-xs text-zinc-400"> m/s</span>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>

                                {{-- Arah Angin --}}
                                <td class="px-4 py-3.5 text-center">
                                    @if ($d['arah_angin'] !== null)
                                        <span
                                            class="font-semibold text-violet-500 dark:text-violet-400">{{ $d['arah_angin'] }}°</span>
                                        <span class="text-xs text-zinc-400">
                                            ({{ $d['arah_angin'] !== null ? $this->degreesToCompass($d['arah_angin']) : '-' }})</span>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>

                                {{-- Ketinggian Air --}}
                                <td class="px-4 py-3.5 text-center">
                                    @if ($d['ketinggian_air'] !== null)
                                        <span
                                            class="font-bold text-blue-600 dark:text-blue-400">{{ $d['ketinggian_air'] }}</span>
                                        <span class="text-xs text-zinc-400"> cm</span>
                                    @else
                                        <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                    @endif
                                </td>

                                {{-- Last Update --}}
                                <td class="px-4 py-3.5 text-center">
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $lastUpdate }}</span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-3.5 text-center">
                                    <button wire:click="openDetail({{ $d['id'] }})"
                                        class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-semibold
                                               border border-zinc-200 dark:border-zinc-700
                                               bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300
                                               hover:bg-zinc-50 dark:hover:bg-zinc-800
                                               hover:border-zinc-300 dark:hover:border-zinc-600
                                               transition cursor-pointer shadow-sm">
                                        <x-heroicon-o-eye class="w-3.5 h-3.5" />
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div
                                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800/60">
                                            <x-heroicon-o-cpu-chip class="w-6 h-6 text-zinc-400" />
                                        </div>
                                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Tidak ada data
                                            sensor</p>
                                        <p class="text-xs text-zinc-400 dark:text-zinc-500">Coba ubah filter pencarian
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3
                        bg-zinc-50/80 dark:bg-zinc-900/60 border-t border-zinc-200 dark:border-zinc-800">
                {{-- Footer --}}
                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                    Menampilkan
                    <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ count($devices) }}</span>
                    device
                </span>
                <div class="overflow-x-auto">
                    {{ $devices->onEachSide(1)->links('components.pagination') }}
                </div>
            </div>
        </div>

        {{-- Modal Detail --}}
        @if ($modalOpen && $this->detailDevice)
            @php
                $detailDevice = $this->detailDevice;
                $detailReading = $this->detailReading;
                $detailHistory = $this->detailHistory;
                $detailStatus = $deviceStatus[$detailDevice->id] ?? ['online' => false, 'last' => null];
                $isOnline = (bool) ($detailStatus['online'] ?? false);
            @endphp

            <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">

                {{-- Backdrop --}}
                <button wire:click="closeModal" class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                    aria-label="Tutup modal">
                </button>

                {{-- Modal --}}
                <div
                    class="relative w-full sm:max-w-5xl mx-0 sm:mx-4
                            rounded-t-3xl sm:rounded-3xl
                            border border-zinc-200 dark:border-zinc-800
                            bg-white dark:bg-zinc-950
                            shadow-2xl max-h-[92vh] overflow-y-auto">

                    {{-- Modal Header --}}
                    <div
                        class="sticky top-0 z-10 flex items-center justify-between gap-3 px-5 py-4
                                border-b border-zinc-100 dark:border-zinc-800
                                bg-white/90 dark:bg-zinc-950/90 backdrop-blur-sm">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-500 shrink-0">
                                <x-heroicon-o-cpu-chip class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100 leading-tight">
                                    {{ $detailDevice->alias ?: ($detailDevice->name ?: 'ROB ' . $detailDevice->id) }}
                                </h2>
                                <p class="text-xs text-zinc-400 mt-0.5">Detail Sensor Device</p>
                            </div>
                        </div>
                        <button wire:click="closeModal"
                            class="flex h-8 w-8 items-center justify-center rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition shrink-0">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="p-5 space-y-5">

                        {{-- Info Cards --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div
                                class="rounded-2xl border border-zinc-100 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-900/40 px-4 py-3">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-zinc-400">Device ID
                                </p>
                                <p class="mt-1.5 text-sm font-bold text-zinc-900 dark:text-zinc-100 font-mono">
                                    #{{ $detailDevice->id }}
                                </p>
                            </div>
                            <div
                                class="rounded-2xl border border-zinc-100 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-900/40 px-4 py-3">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-zinc-400">Status</p>
                                <p
                                    class="mt-1.5 text-sm font-bold {{ $isOnline ? 'text-emerald-500' : 'text-red-500' }}">
                                    {{ $isOnline ? '● Online' : '● Offline' }}
                                </p>
                            </div>
                            <div
                                class="rounded-2xl border border-zinc-100 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-900/40 px-4 py-3">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-zinc-400">Last Update
                                </p>
                                <p class="mt-1.5 text-sm font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $this->detailLastUpdateText }}
                                </p>
                            </div>
                        </div>

                        {{-- Latest Reading Grid --}}
                        <div>
                            <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wide mb-3">Pembacaan
                                Terbaru</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                                @foreach ([['label' => 'Suhu', 'value' => $detailReading?->suhu, 'unit' => '°C', 'color' => 'orange'], ['label' => 'Kelembapan', 'value' => $detailReading?->kelembapan, 'unit' => '%', 'color' => 'cyan'], ['label' => 'Tekanan', 'value' => $detailReading?->tekanan_udara, 'unit' => 'hPa', 'color' => 'emerald'], ['label' => 'Angin', 'value' => $detailReading?->kecepatan_angin, 'unit' => 'm/s', 'color' => 'amber'], ['label' => 'Arah Angin', 'value' => $detailReading?->arah_angin, 'unit' => '°', 'color' => 'violet'], ['label' => 'Ketinggian', 'value' => $detailReading?->ketinggian_air, 'unit' => 'cm', 'color' => 'blue']] as $sensor)
                                    <div
                                        class="rounded-2xl border border-{{ $sensor['color'] }}-100 dark:border-{{ $sensor['color'] }}-500/20 bg-{{ $sensor['color'] }}-50 dark:bg-{{ $sensor['color'] }}-500/10 p-3 text-center">
                                        <p
                                            class="text-[10px] font-semibold uppercase tracking-wide text-{{ $sensor['color'] }}-600 dark:text-{{ $sensor['color'] }}-400">
                                            {{ $sensor['label'] }}
                                        </p>
                                        @if ($sensor['value'] !== null)
                                            <p class="text-xl font-extrabold text-zinc-900 dark:text-white mt-1.5">
                                                {{ $sensor['value'] }}</p>
                                            <p class="text-[10px] text-zinc-400 mt-0.5">{{ $sensor['unit'] }}</p>
                                        @else
                                            <p class="text-xl font-extrabold text-zinc-300 dark:text-zinc-600 mt-1.5">—
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- History --}}
                        <div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                                <div>
                                    <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wide">Riwayat
                                        Sensor</p>
                                    <p class="text-xs text-zinc-400 mt-0.5">Range: {{ $this->detailRangeLabel }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <div class="relative">
                                        <select wire:model.live="detailRange"
                                            class="appearance-none rounded-xl border border-zinc-200 dark:border-zinc-700
                                                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100
                                                   px-3 py-2 pr-8 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition cursor-pointer">
                                            <option value="1m">1 Menit</option>
                                            <option value="1h">1 Jam</option>
                                            <option value="1d">1 Hari</option>
                                            <option value="1w">1 Minggu</option>
                                            <option value="1mo">1 Bulan</option>
                                            <option value="1y">1 Tahun</option>
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-zinc-400">
                                            <x-heroicon-o-chevron-down class="w-3 h-3" />
                                        </div>
                                    </div>
                                    <div class="relative">
                                        <select wire:model.live="detailPerPage"
                                            class="appearance-none rounded-xl border border-zinc-200 dark:border-zinc-700
                                                   bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100
                                                   px-3 py-2 pr-8 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition cursor-pointer">
                                            <option value="10">10 / hal</option>
                                            <option value="25">25 / hal</option>
                                            <option value="50">50 / hal</option>
                                            <option value="100">100 / hal</option>
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-zinc-400">
                                            <x-heroicon-o-chevron-down class="w-3 h-3" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-[900px] w-full text-sm">
                                        <thead>
                                            <tr
                                                class="border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-900/50">
                                                <th
                                                    class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                                    Waktu</th>
                                                <th
                                                    class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                                    Suhu</th>
                                                <th
                                                    class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                                    Kelembapan</th>
                                                <th
                                                    class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                                    Tekanan</th>
                                                <th
                                                    class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                                    Angin</th>
                                                <th
                                                    class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                                    Arah</th>
                                                <th
                                                    class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                                                    Ketinggian</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                                            @forelse ($detailHistory as $row)
                                                <tr
                                                    class="hover:bg-zinc-50/80 dark:hover:bg-zinc-900/40 transition-colors">
                                                    <td
                                                        class="px-4 py-3 whitespace-nowrap text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                                        {{ $row['timestamp'] }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-xs">
                                                        @if ($row['suhu'] !== null)
                                                            <span
                                                                class="font-semibold text-orange-500">{{ $row['suhu'] }}</span>
                                                            <span class="text-zinc-400"> °C</span>
                                                        @else
                                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-xs">
                                                        @if ($row['kelembapan'] !== null)
                                                            <span
                                                                class="font-semibold text-cyan-500">{{ $row['kelembapan'] }}</span>
                                                            <span class="text-zinc-400"> %</span>
                                                        @else
                                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-xs">
                                                        @if ($row['tekanan_udara'] !== null)
                                                            <span
                                                                class="font-semibold text-emerald-500">{{ $row['tekanan_udara'] }}</span>
                                                            <span class="text-zinc-400"> hPa</span>
                                                        @else
                                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-xs">
                                                        @if ($row['kecepatan_angin'] !== null)
                                                            <span
                                                                class="font-semibold text-amber-500">{{ $row['kecepatan_angin'] }}</span>
                                                            <span class="text-zinc-400"> m/s</span>
                                                        @else
                                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-xs">
                                                        @if ($row['arah_angin'] !== null)
                                                            <span
                                                                class="font-semibold text-violet-500">{{ $row['arah_angin'] }}°</span>
                                                            <span class="text-zinc-400">
                                                                ({{ $row['arah_angin_label'] }})</span>
                                                        @else
                                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-xs">
                                                        @if ($row['ketinggian_air'] !== null)
                                                            <span
                                                                class="font-bold text-blue-500">{{ $row['ketinggian_air'] }}</span>
                                                            <span class="text-zinc-400"> cm</span>
                                                        @else
                                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="px-4 py-10 text-center">
                                                        <p class="text-sm text-zinc-400">Tidak ada data pada range ini
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Table Footer --}}
                                <div
                                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3
                                            border-t border-zinc-100 dark:border-zinc-800
                                            bg-zinc-50/80 dark:bg-zinc-900/60">
                                    <span class="text-xs text-zinc-400">
                                        Menampilkan
                                        <span
                                            class="font-semibold text-zinc-600 dark:text-zinc-300">{{ $detailHistory->firstItem() ?? 0 }}–{{ $detailHistory->lastItem() ?? 0 }}</span>
                                        dari
                                        <span
                                            class="font-semibold text-zinc-600 dark:text-zinc-300">{{ $detailHistory->total() }}</span>
                                        data
                                    </span>
                                    <div class="overflow-x-auto">
                                        {{ $detailHistory->onEachSide(1)->links('components.pagination') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Modal Footer --}}
                    <div
                        class="sticky bottom-0 flex items-center justify-end gap-2 px-5 py-4
                                border-t border-zinc-100 dark:border-zinc-800
                                bg-white/90 dark:bg-zinc-950/90 backdrop-blur-sm">
                        <button wire:click="closeModal"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                                   border border-zinc-200 dark:border-zinc-700
                                   text-zinc-700 dark:text-zinc-300
                                   hover:bg-zinc-50 dark:hover:bg-zinc-800
                                   transition cursor-pointer">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</section>
