<?php

namespace App\Livewire\Admin;

use App\Models\Device;
use App\Models\SensorReading;
use App\Models\DashSetting;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Dashboard')]
class Dashboard extends Component
{
    // ===== Dashboard state =====
    public array $data = [];
    public string $risk = 'AMAN';
    public float|int $riskScore = 1;

    // ===== Settings =====
    public bool $showSettings = false;
    public string $theme = 'dark';
    public ?int $selectedDeviceId = null;

    // metric untuk chart utama
    public string $selectedSensor = 'ketinggian_air';

    // ===== Time ranges =====
    public array $timeRanges = [
        '1m'  => '1 Menit',
        '30m' => '30 Menit',
        '1h'  => '1 Jam',
        '12h' => '12 Jam',
        '24h' => '24 Jam',
        '1w'  => '1 Minggu',
        '1mo' => '1 Bulan',
        '1y'  => '1 Tahun',
    ];

    // chart utama
    public string $selectedTimeRange = '30m';

    // ===== Modal metric chart state =====
    public bool $modalOpen = false;
    public string $selectedMetric = 'ketinggian_air';

    // range modal (independen dari chart utama)
    public string $modalTimeRange = '30m';

    // batasi max points untuk modal (akan di-override oleh range)
    public int $limit = 300;

    public array $metricLabels = [
        'suhu' => 'Temperature (°C)',
        'kelembapan' => 'Kelembapan (%)',
        'tekanan_udara' => 'Tekanan Udara (hPa)',
        'kecepatan_angin' => 'Kecepatan Angin (m/s)',
        'arah_angin' => 'Arah Angin (°)',
        'ketinggian_air' => 'Ketinggian Air (cm)',
    ];

    protected $listeners = [
        'open-dashboard-settings' => 'openSettings',
    ];

    public function mount(): void
    {
        $device = Device::query()->first();
        if (!$device) return;

        $setting = DashSetting::query()
            ->where('user_id', auth()->id())
            ->first();

        if (!$setting) {
            $setting = DashSetting::query()->create([
                'user_id' => auth()->id(),
                'theme' => 'dark',
                'selected_device_id' => $device->id,
                'selected_sensor' => 'ketinggian_air',
                'selected_time_range' => '30m',
            ]);
        }

        $this->theme = $setting->theme ?? 'dark';
        $this->selectedDeviceId = (int) ($setting->selected_device_id ?? $device->id);
        $this->selectedSensor = $setting->selected_sensor ?? 'ketinggian_air';
        $this->selectedTimeRange = $setting->selected_time_range ?? '30m';

        // modal default ikutin chart utama (boleh kamu ubah)
        $this->modalTimeRange = $this->selectedTimeRange;
    }

    // ==========================
    // MAIN DASHBOARD (Realtime)
    // ==========================
    public function fetchData(): void
    {
        if (!$this->selectedDeviceId) return;

        $latest = SensorReading::query()
            ->where('device_id', $this->selectedDeviceId)
            ->latest('timestamp')
            ->first();

        if (!$latest) return;

        $this->data = [
            'project' => $latest->project ?? 'ROB Monitoring',
            'timestamp' => $latest->timestamp,
            'suhu' => $latest->suhu,
            'tekanan_udara' => $latest->tekanan_udara,
            'kelembapan' => $latest->kelembapan,
            'ketinggian_air' => $latest->ketinggian_air,
            'arah_angin' => $latest->arah_angin,
            'kecepatan_angin' => $latest->kecepatan_angin,
            'arah_angin_label' => $this->getWindDirectionLabel($latest->arah_angin),
        ];

        $this->calculateRisk();

        // chart utama
        $this->dispatchMainChart();

        // update state Alpine
        $this->dispatch(
            'dashboard-updated',
            data: $this->data,
            risk: $this->risk,
            riskScore: $this->riskScore,
            riskStyles: $this->riskStyles
        );

        // kalau modal lagi terbuka, keep updated juga
        if ($this->modalOpen) {
            $this->dispatchMetricChart();
        }
    }

    // ==========================
    // SETTINGS
    // ==========================
    public function openSettings(): void
    {
        $this->showSettings = true;
    }

    public function updated($property): void
    {
        // persist setting yang relevan
        if (in_array($property, [
            'theme',
            'selectedDeviceId',
            'selectedSensor',
            'selectedTimeRange',
        ], true)) {
            if (!$this->selectedDeviceId) return;

            DashSetting::query()->updateOrCreate(
                ['user_id' => auth()->id()],
                [
                    'theme' => $this->theme,
                    'selected_device_id' => $this->selectedDeviceId,
                    'selected_sensor' => $this->selectedSensor,
                    'selected_time_range' => $this->selectedTimeRange,
                ]
            );

            // refresh dashboard + chart
            $this->fetchData();
        }

        // update chart utama jika range berubah
        if ($property === 'selectedTimeRange') {
            $this->dispatchMainChart();
        }

        // update modal chart jika range modal berubah
        if ($property === 'modalTimeRange' && $this->modalOpen) {
            $this->dispatchMetricChart();
        }
    }

    // ==========================
    // TIME RANGE HELPERS (UTC filter + fallback)
    // ==========================
    private function rangeStartUtc(string $range): ?Carbon
    {
        $now = now('UTC');

        return match ($range) {
            '1m'  => $now->copy()->subMinute(),
            '30m' => $now->copy()->subMinutes(30),
            '1h'  => $now->copy()->subHour(),
            '12h' => $now->copy()->subHours(12),
            '24h' => $now->copy()->subDay(),
            '1w'  => $now->copy()->subWeek(),
            '1mo' => $now->copy()->subMonth(),
            '1y'  => $now->copy()->subYear(),
            default => null,
        };
    }

    private function applyTimeRangeUtc($query, string $range)
    {
        $start = $this->rangeStartUtc($range);
        if (!$start) return $query;

        return $query->where('timestamp', '>=', $start);
    }

    private function takePointsForRange(string $range): int
    {
        // aman untuk kebanyakan kasus (tidak terlalu berat)
        return match ($range) {
            '1m'  => 120,
            '30m' => 300,
            '1h'  => 600,
            '12h' => 1200,
            '24h' => 1800,
            '1w'  => 2500,
            '1mo' => 4000,
            '1y'  => 6000,
            default => 300,
        };
    }

    // ==========================
    // MAIN CHART (Water Level Trend)
    // Event: refreshChart
    // ==========================
    private function dispatchMainChart(): void
    {
        if (!$this->selectedDeviceId) return;

        $base = SensorReading::query()
            ->where('device_id', $this->selectedDeviceId);

        $query = $this->applyTimeRangeUtc(clone $base, $this->selectedTimeRange);

        $take = $this->takePointsForRange($this->selectedTimeRange);

        $records = $query->latest('timestamp')->take($take)->get();

        // ✅ fallback kalau filter kosong
        if ($records->count() === 0) {
            $records = $base->latest('timestamp')->take(300)->get();
        }

        $records = $records->reverse()->values();

        // timezone buat tampilan label
        $tzDisplay = config('app.timezone', 'Asia/Jakarta');

        $labels = $records->pluck('timestamp')
            ->map(fn($t) => Carbon::parse($t)->setTimezone($tzDisplay)->format('d M H:i'))
            ->toArray();

        $metric = $this->selectedSensor ?: 'ketinggian_air';

        $values = $records->pluck($metric)
            ->map(fn($v) => (float) ($v ?? 0))
            ->toArray();

        $this->dispatch('refreshChart', labels: $labels, values: $values);
    }

    // ==========================
    // RISK
    // ==========================
    private function calculateRisk(): void
    {
        $water = (float) ($this->data['ketinggian_air'] ?? 0);
        $wind  = (float) ($this->data['kecepatan_angin'] ?? 0);

        $score = ($water * 0.5) + ($wind * 0.3);
        $this->riskScore = round($score, 2);

        if ($score > 200) $this->risk = 'BAHAYA';
        elseif ($score > 150) $this->risk = 'SIAGA';
        elseif ($score > 100) $this->risk = 'WASPADA';
        else $this->risk = 'AMAN';
    }

    public function getRiskStylesProperty(): array
    {
        return match ($this->risk) {
            'BAHAYA' => [
                'bg' => 'bg-red-500/10',
                'border' => 'border-red-500/30',
                'text' => 'text-red-400',
            ],
            'SIAGA' => [
                'bg' => 'bg-yellow-500/10',
                'border' => 'border-yellow-500/30',
                'text' => 'text-yellow-400',
            ],
            'WASPADA' => [
                'bg' => 'bg-orange-500/10',
                'border' => 'border-orange-500/30',
                'text' => 'text-orange-400',
            ],
            default => [
                'bg' => 'bg-emerald-500/10',
                'border' => 'border-emerald-500/30',
                'text' => 'text-emerald-400',
            ],
        };
    }

    private function getWindDirectionLabel($degree): string
    {
        if ($degree === null) return '-';

        $directions = [
            'Utara',
            'Timur Laut',
            'Timur',
            'Tenggara',
            'Selatan',
            'Barat Daya',
            'Barat',
            'Barat Laut'
        ];

        $index = (int) floor(((float) $degree + 22.5) / 45) % 8;

        return $directions[$index];
    }

    // ==========================
    // MODAL METRIC CHART
    // Event: modalChart
    // ==========================
    public function openMetric(string $metric): void
    {
        if (!array_key_exists($metric, $this->metricLabels)) return;

        $this->selectedMetric = $metric;
        $this->modalOpen = true;

        // modal default ngikutin chart utama (opsional)
        if (!$this->modalTimeRange) {
            $this->modalTimeRange = $this->selectedTimeRange;
        }

        $this->dispatchMetricChart();
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
    }

    public function pollMetric(): void
    {
        if (!$this->modalOpen) return;
        $this->dispatchMetricChart();
    }

    protected function dispatchMetricChart(): void
    {
        if (!$this->selectedDeviceId) return;

        $base = SensorReading::query()
            ->where('device_id', $this->selectedDeviceId);

        $query = $this->applyTimeRangeUtc(clone $base, $this->modalTimeRange);

        $take = $this->takePointsForRange($this->modalTimeRange);

        $rows = $query->latest('timestamp')
            ->take($take)
            ->get(['timestamp', $this->selectedMetric]);

        // ✅ fallback kalau kosong
        if ($rows->count() === 0) {
            $rows = $base->latest('timestamp')
                ->take(300)
                ->get(['timestamp', $this->selectedMetric]);
        }

        $rows = $rows->reverse()->values();

        $tzDisplay = config('app.timezone', 'Asia/Jakarta');

        $labels = $rows->map(fn($r) =>
            Carbon::parse($r->timestamp)->setTimezone($tzDisplay)->format('d M H:i')
        )->all();

        $values = $rows->map(fn($r) =>
            (float) ($r->{$this->selectedMetric} ?? 0)
        )->all();

        $this->dispatch(
            'modalChart',
            title: ($this->metricLabels[$this->selectedMetric] ?? $this->selectedMetric),
            labels: $labels,
            values: $values
        );
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
