<div wire:poll.1s="fetchData" class="flex-1 w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center">

        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-500 rounded-xl flex items-center justify-center">
                <x-heroicon-o-bell-alert class="w-5 h-5 text-black" />
            </div>
            <div>
                <h1 class="text-lg font-semibold text-white">
                    ROB Monitoring
                </h1>
                <p class="text-xs text-zinc-400">
                    Early Warning System
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 text-sm">
            <span class="px-3 py-1 rounded-full bg-zinc-800 text-zinc-300">
                {{ now()->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}
            </span>

            <div class="flex items-center gap-2 text-emerald-400 font-medium">
                <span class="relative flex h-2 w-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                </span>
                LIVE
            </div>
        </div>
    </div>


    {{-- ================= RISK BANNER ================= --}}
    <div
        class="rounded-xl px-4 py-3 flex items-center justify-between
        @if ($risk === 'BAHAYA') bg-red-500/10 border border-red-500/30
        @elseif($risk === 'AWAS') bg-orange-500/10 border border-orange-500/30
        @elseif($risk === 'SIAGA') bg-yellow-500/10 border border-yellow-500/30
        @else bg-emerald-500/10 border border-emerald-500/30 @endif
    ">

        <div class="flex items-center gap-3">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 {{ $riskColor }}" />
            <span class="text-sm text-zinc-400">Status Sistem:</span>
            <span class="font-semibold {{ $riskColor }}">
                {{ $risk }}
            </span>
        </div>

        <span class="text-xs text-zinc-500">
            Monitoring aktif & sinkron
        </span>
    </div>


    {{-- ================= METRICS GRID ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- WATER LEVEL --}}
        <div wire:click="openChart('ketinggian_air')"
            class="bg-zinc-900 p-6 rounded-xl border border-blue-500/30
                    shadow-[0_0_20px_rgba(59,130,246,0.05)]
                    cursor-pointer hover:border-blue-400 transition">

            <div class="flex justify-between items-center">
                <p class="text-sm text-zinc-400">Ketinggian Air</p>
                <x-heroicon-o-arrow-trending-up class="w-5 h-5 text-blue-400" />
            </div>

            <h2 class="text-4xl font-bold text-white mt-3">
                {{ $data['ketinggian_air'] ?? '--' }}
                <span class="text-base text-zinc-400">cm</span>
            </h2>
        </div>


        {{-- WIND SPEED --}}
        <div wire:click="openChart('kecepatan_angin')"
            class="bg-zinc-900 p-6 rounded-xl border border-zinc-700
                    cursor-pointer hover:border-amber-400 transition">

            <div class="flex justify-between items-center">
                <p class="text-sm text-zinc-400">Kecepatan Angin</p>
                <x-heroicon-o-flag class="w-5 h-5 text-amber-400" />
            </div>

            <h2 class="text-3xl font-bold text-white mt-3">
                {{ $data['kecepatan_angin'] ?? '--' }}
                <span class="text-base text-zinc-400">m/s</span>
            </h2>
        </div>


        {{-- TEKANAN UDARA --}}
        <div wire:click="openChart('tekanan_udara')"
            class="bg-zinc-900 p-6 rounded-xl border border-zinc-700
                    cursor-pointer hover:border-emerald-400 transition">

            <div class="flex justify-between items-center">
                <p class="text-sm text-zinc-400">Tekanan Udara</p>
                <x-heroicon-o-cloud class="w-5 h-5 text-emerald-400" />
            </div>

            <h2 class="text-3xl font-bold text-white mt-3">
                {{ $data['tekanan_udara'] ?? '--' }}
                <span class="text-base text-zinc-400">hPa</span>
            </h2>
        </div>


        {{-- TEMPERATURE --}}
        <div wire:click="openChart('suhu')"
            class="bg-zinc-900 p-6 rounded-xl border border-zinc-700
                    cursor-pointer hover:border-orange-400 transition">

            <div class="flex justify-between items-center">
                <p class="text-sm text-zinc-400">Temperature</p>
                <x-heroicon-o-fire class="w-5 h-5 text-orange-400" />
            </div>

            <h2 class="text-3xl font-bold text-white mt-3">
                {{ $data['suhu'] ?? '--' }} °C
            </h2>
        </div>


        {{-- HUMIDITY --}}
        <div wire:click="openChart('kelembapan')"
            class="bg-zinc-900 p-6 rounded-xl border border-zinc-700
                    cursor-pointer hover:border-cyan-400 transition">

            <div class="flex justify-between items-center">
                <p class="text-sm text-zinc-400">Humidity</p>
                <x-heroicon-o-beaker class="w-5 h-5 text-cyan-400" />
            </div>

            <h2 class="text-3xl font-bold text-white mt-3">
                {{ $data['kelembapan'] ?? '--' }} %
            </h2>
        </div>


        {{-- WIND DIRECTION --}}
        <div wire:click="openChart('arah_angin')"
            class="bg-zinc-900 p-6 rounded-xl border border-zinc-700
                    cursor-pointer hover:border-purple-400 transition">

            <div class="flex justify-between items-center">
                <p class="text-sm text-zinc-400">Wind Direction</p>
                <x-heroicon-o-arrow-path-rounded-square class="w-5 h-5 text-purple-400" />
            </div>

            <h2 class="text-3xl font-bold text-white mt-3">
                {{ $data['arah_angin'] ?? '--' }}°
            </h2>
        </div>

    </div>


    {{-- ================= CHART ================= --}}
    <div
        class="bg-gradient-to-br from-zinc-900 to-zinc-950
                rounded-xl border border-zinc-800 p-6 h-[360px]">

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-semibold text-white">
                Water Level Trend
            </h3>
            <span class="text-xs text-zinc-400">
                Last 30 Records
            </span>
        </div>

        <div wire:ignore class="h-[calc(100%-40px)]">
            <canvas id="waterChart"></canvas>
        </div>

    </div>


    {{-- ================= MODAL ================= --}}
    <div x-data="{ open: @entangle('showModal') }" x-show="open" x-transition
        class="fixed inset-0 bg-black/60 flex items-center justify-center z-50" style="display:none;">

        <div class="bg-zinc-900 w-[800px] h-[450px] rounded-xl p-6 border border-zinc-700">

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-white font-semibold">
                    {{ $selectedSensorLabel }}
                </h3>

                <button @click="open=false" class="text-zinc-400 hover:text-white text-xl cursor-pointer">
                    ✕
                </button>
            </div>

            <div wire:ignore class="h-[350px]">
                <canvas id="modalChart"></canvas>
            </div>

        </div>
    </div>


</div>


@push('scripts')
<script>
document.addEventListener('livewire:init', function () {

    // ================= MAIN CHART =================
    const mainCtx = document.getElementById('waterChart').getContext('2d');

    let mainChart = new Chart(mainCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                data: [],
                borderColor: '#38bdf8',
                backgroundColor: 'rgba(56,189,248,0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        }
    });

    Livewire.on('refreshChart', (data) => {
        mainChart.data.labels = data.labels;
        mainChart.data.datasets[0].data = data.values;
        mainChart.update();
    });


    // ================= MODAL CHART =================
    let modalChart = null;

    Livewire.on('refreshModalChart', (data) => {

        // kasih delay kecil supaya modal visible
        setTimeout(() => {

            const canvas = document.getElementById('modalChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            if (modalChart) {
                modalChart.destroy();
            }

            modalChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Data Sensor',
                        data: data.values,
                        borderColor: '#38bdf8',
                        backgroundColor: 'rgba(56,189,248,0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

        }, 200);

    });

});
</script>
@endpush
