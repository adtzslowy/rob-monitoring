<?php

namespace App\Livewire\Admin;

use App\Models\DashboardLog;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public $data = [];
    public $risk = 'AMAN';
    public $riskScore = 1;

    public $showModal = false;
    public $selectedSensor = null;
    public $selectedSensorLabel = null;

    public function mount()
    {
        $this->data = [
            'project' => 'ROB Monitoring',
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'suhu' => null,
            'tekanan_udara' => null,
            'kelembapan' => null,
            'ketinggian_air' => null,
            'arah_angin' => null,
            'kecepatan_angin' => null,
        ];

        $this->fetchData();
    }

    public function fetchData()
    {
        $latest = DashboardLog::latest('timestamp')->first();

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

        $this->data['arah_angin_label'] = $this->getWindDirectionLabel($this->data['arah_angin']);
        $this->calculateRiskFuzzy();
        $this->dispatchMainChart();
    }

    // ================= FUZZY =================

    private function triangular($x, $a, $b, $c)
    {
        if ($x <= $a || $x >= $c) return 0;
        if ($x == $b) return 1;
        if ($x > $a && $x < $b) return ($x - $a) / ($b - $a);
        return ($c - $x) / ($c - $b);
    }

    private function fuzzify($water, $wind, $direction)
    {
        return [
            'water_low' => $this->triangular($water, 0, 150, 200),
            'water_medium' => $this->triangular($water, 150, 220, 260),
            'water_high' => $this->triangular($water, 220, 300, 350),

            'wind_weak' => $this->triangular($wind, 0, 3, 6),
            'wind_medium' => $this->triangular($wind, 4, 8, 12),
            'wind_strong' => $this->triangular($wind, 10, 15, 20),

            'west_danger' => $this->triangular($direction, 225, 270, 315),
            'east_safe' => $this->triangular($direction, 45, 90, 135),
        ];
    }

    private function fuzzyInterface($m)
    {
        return [
            'bahaya' => min(
                $m['water_high'],
                $m['wind_strong'],
                $m['west_danger'],
            ),

            'siaga' => max(
                min($m['water_high'], $m['wind_medium'], $m['west_danger']),
                min($m['water_medium'], $m['wind_strong'], $m['west_danger']),
            ),

            'waspada' => min(
                $m['water_medium'],
                $m['wind_medium'],
            ),

            'aman' => max(
                $m['water_low'],
                $m['east_safe'],
            ),
        ];
    }

    private function defuzzify($rules)
    {
        $num = ($rules['aman'] * 1)
            + ($rules['waspada'] * 2)
            + ($rules['siaga'] * 3)
            + ($rules['bahaya'] * 4);

        $den = array_sum($rules);
        return $den == 0 ? 1 : $num / $den;
    }

    private function calculateRiskFuzzy()
    {
        $water = $this->data['ketinggian_air'] ?? 0;
        $wind = $this->data['kecepatan_angin'] ?? 0;
        $direction = $this->data['arah_angin'] ?? 0;

        $m = $this->fuzzify($water, $wind, $direction);
        $rules = $this->fuzzyInterface($m);
        $score = $this->defuzzify($rules);

        $this->riskScore = round($score, 2);

        if ($score >= 3.5) $this->risk = 'BAHAYA';
        elseif ($score >= 2.5) $this->risk = 'SIAGA';
        elseif ($score >= 1.5) $this->risk = 'WASPADA';
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

    // ================= CHART =================

    private function dispatchMainChart()
    {
        $records = DashboardLog::latest('timestamp')
            ->take(30)->get()->reverse();

        $labels = $records->pluck('timestamp')
            ->map(fn($t) => Carbon::parse($t)->format('H:i:s'))->toArray();

        $values = $records->pluck('ketinggian_air')
            ->map(fn($v) => (float)$v)->toArray();

        $this->dispatch('refreshChart', labels: $labels, values: $values);
    }

    public function openChart($sensor)
    {
        $labelMap = [
            'ketinggian_air' => 'Ketinggian Air',
            'tekanan_udara' => 'Tekanan Udara',
            'kecepatan_angin' => 'Kecepatan Angin',
            'suhu' => 'Temperature',
            'kelembapan' => 'Humidity',
            'arah_angin' => 'Wind Direction',
        ];

        $this->selectedSensor = $sensor;
        $this->selectedSensorLabel = $labelMap[$sensor] ?? $sensor;
        $this->showModal = true;

        $records = DashboardLog::latest('timestamp')
            ->take(30)->get()->reverse();

        $labels = $records->pluck('timestamp')
            ->map(fn($t) => Carbon::parse($t)->format('H:i:s'))->toArray();

        $values = $records->pluck($sensor)
            ->map(fn($v) => (float)$v)->toArray();

        $this->dispatch('refreshModalChart', labels: $labels, values: $values);
    }

    private function getWindDirectionLabel($degree)
    {
        if ($degree === null) return '-';

        $degree = fmod((float) $degree + 360, 360);

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

        $index = (int) round($degree / 45) % 8;

        return $directions[$index];
    }


    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
