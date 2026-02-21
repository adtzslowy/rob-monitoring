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
    public $data = [];
    public $risk = 'AMAN';
    public $riskScore = 1;

    public $deviceId;
    public $showModal = false;
    public $selectedSensorLabel = null;

    // SETTINGS
    public $showSettings = false;
    public $theme = 'dark';
    public $selectedDeviceId;
    public $selectedSensor = 'ketinggian_air';
    public $selectedTimeRange = '1m';

    protected $listeners = [
        'open-dashboard-settings' => 'openSettings',
    ];

    public function mount()
    {
        $setting = DashSetting::where('user_id', auth()->id())->first();
        $device = Device::first();

        if (!$device) return;

        if (!$setting) {
            $setting = DashSetting::create([
                'user_id' => auth()->id(),
                'theme' => 'dark',
                'selected_device_id' => $device->id,
                'selected_sensor' => 'ketinggian_air',
                'selected_time_range' => '1m',
            ]);
        }

        $this->theme = $setting->theme;
        $this->selectedDeviceId = $setting->selected_device_id;
        $this->selectedSensor = $setting->selected_sensor;
        $this->selectedTimeRange = $setting->selected_time_range;

        $this->deviceId = $this->selectedDeviceId;

        $this->fetchData();
    }

    public function fetchData()
    {
        $latest = SensorReading::where('device_id', $this->selectedDeviceId)
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
        ];

        $this->data['arah_angin_label'] =
            $this->getWindDirectionLabel($this->data['arah_angin']);

        $this->calculateRiskFuzzy();
        $this->dispatchMainChart();

        $this->dispatch(
            'dashboard-updated',
            data: $this->data,
            risk: $this->risk,
            riskScore: $this->riskScore,
            riskStyles: $this->riskStyles
        );
    }

    /* ================= SETTINGS ================= */

    public function openSettings()
    {
        $this->showSettings = true;
    }

    public function updated($property)
    {
        if (in_array($property, [
            'theme',
            'selectedDeviceId',
            'selectedSensor',
            'selectedTimeRange'
        ])) {

            DashSetting::updateOrCreate(
                ['user_id' => auth()->id()],
                [
                    'theme' => $this->theme,
                    'selected_device_id' => $this->selectedDeviceId,
                    'selected_sensor' => $this->selectedSensor,
                    'selected_time_range' => $this->selectedTimeRange,
                ]
            );

            $this->deviceId = $this->selectedDeviceId;

            $this->fetchData();
        }
    }

    /* ================= CHART ================= */

    private function dispatchMainChart()
    {
        $query = SensorReading::where('device_id', $this->selectedDeviceId)
            ->latest('timestamp');

        // time range logic
        // if ($this->selectedTimeRange === '30s') {
        //     $query->where('timestamp', '>=', now()->subSeconds(30));
        // } elseif ($this->selectedTimeRange === '1m') {
        //     $query->where('timestamp', '>=', now()->subMinute());
        // } elseif ($this->selectedTimeRange === '1h') {
        //     $query->where('timestamp', '>=', now()->subHour());
        // } elseif ($this->selectedTimeRange === '1d') {
        //     $query->where('timestamp', '>=', now()->subDay());
        // }

        $records = $query->take(100)->get()->reverse();

        $labels = $records->pluck('timestamp')
            ->map(fn($t) => Carbon::parse($t)
                ->setTimezone('Asia/Jakarta')
                ->format('H:i:s'))
            ->toArray();

        $values = $records->pluck($this->selectedSensor)
            ->map(fn($v) => (float)$v)
            ->toArray();

        $this->dispatch('refreshChart', labels: $labels, values: $values);
    }

    /* ================= FUZZY ================= */

    private function triangular($x, $a, $b, $c)
    {
        if ($x <= $a || $x >= $c) return 0;
        if ($x == $b) return 1;
        if ($x < $b) return ($x - $a) / ($b - $a);
        return ($c - $x) / ($c - $b);
    }

    private function calculateRiskFuzzy()
    {
        $water = $this->data['ketinggian_air'] ?? 0;
        $wind = $this->data['kecepatan_angin'] ?? 0;
        $direction = $this->data['arah_angin'] ?? 0;

        $score = ($water * 0.5) + ($wind * 0.3);

        $this->riskScore = round($score, 2);

        if ($score > 200) $this->risk = 'BAHAYA';
        elseif ($score > 150) $this->risk = 'SIAGA';
        elseif ($score > 100) $this->risk = 'WASPADA';
        else $this->risk = 'AMAN';
    }

    public function getRiskStylesProperty()
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

    private function getWindDirectionLabel($degree)
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

        return $directions[(int) round($degree / 45) % 8];
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
