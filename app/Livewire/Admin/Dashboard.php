<?php

namespace App\Livewire\Admin;

use App\Models\DashboardLog;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public $data = [];
    public $risk = 'AMAN';
    public $riskColor = 'text-emerald-500';

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
        try {
            $url = config('services.iot.url');

            $response = Http::timeout(5)->acceptJson()->get($url);

            if (!$response->successful()) {
                return;
            }

            $json = $response->json();
            $payload = $json['data'] ?? [];

            $this->data = [
                'project' => $json['project'] ?? 'ROB Monitoring',
                'timestamp' => $json['timestamp'] ?? now()->format('Y-m-d H:i:s'),
                'suhu' => $payload['suhu'] ?? null,
                'tekanan_udara' => $payload['tekanan_udara'] ?? null,
                'kelembapan' => $payload['kelembapan'] ?? null,
                'ketinggian_air' => $payload['ketinggian_air'] ?? null,
                'arah_angin' => $payload['arah_angin'] ?? null,
                'kecepatan_angin' => $payload['kecepatan_angin'] ?? null,
            ];

            $this->calculateRisk();

            // update main chart (30 data terakhir saja)
            $this->dispatchMainChart();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }
    }

    private function dispatchMainChart()
    {
        $records = DashboardLog::latest('timestamp')->take(30)->get()->reverse();

        $labels = $records->pluck('timestamp')->map(fn($t) => Carbon::parse($t)->setTimezone('Asia/Jakarta')->format('H:i:s'))->toArray();

        $values = $records->pluck('ketinggian_air')->map(fn($v) => (float) $v)->toArray();

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

        // buka modal dulu
        $this->showModal = true;

        // ambil data
        $records = DashboardLog::latest('timestamp')->take(30)->get()->reverse();

        $labels = $records->pluck('timestamp')->map(fn($t) => Carbon::parse($t)->setTimezone('Asia/Jakarta')->format('H:i:s'))->toArray();

        $values = $records->pluck($sensor)->map(fn($v) => (float) $v)->toArray();

        // kirim event SETELAH modal state berubah
        $this->dispatch('refreshModalChart', labels: $labels, values: $values);
    }

    private function calculateRisk()
    {
        $water = (float) ($this->data['ketinggian_air'] ?? 0);

        if ($water > 140) {
            $this->risk = 'BAHAYA';
            $this->riskColor = 'text-red-500';
        } elseif ($water > 130) {
            $this->risk = 'AWAS';
            $this->riskColor = 'text-orange-400';
        } elseif ($water > 120) {
            $this->risk = 'SIAGA';
            $this->riskColor = 'text-yellow-400';
        } else {
            $this->risk = 'AMAN';
            $this->riskColor = 'text-emerald-500';
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
